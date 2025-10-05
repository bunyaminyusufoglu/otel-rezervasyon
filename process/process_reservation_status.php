<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/ReservationHelper.php';
require_once '../includes/CSRFHelper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

// CSRF token doğrulama
if (!CSRFHelper::validatePostToken()) {
    CSRFHelper::handleValidationFailure();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu işlem için yetkiniz yok.';
    header('Location: ../index.php');
    exit();
}

$reservationId = (int)($_POST['reservation_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($reservationId <= 0 || !in_array($action, ['confirm','cancel','complete'], true)) {
    $_SESSION['error'] = 'Geçersiz istek.';
    header('Location: ../pages/admin/reservations.php');
    exit();
}

try {
    // Rezervasyon ve kullanıcı bilgilerini al
    $stmt = $pdo->prepare('
        SELECT r.*, u.first_name, u.last_name, u.email, rm.name as room_name, rm.type as room_type 
        FROM reservations r 
        LEFT JOIN users u ON r.user_id = u.id 
        LEFT JOIN rooms rm ON r.room_id = rm.id 
        WHERE r.id = ?
    ');
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Rezervasyon bulunamadı.';
        header('Location: ../pages/admin/reservations.php');
        exit();
    }

    $reservationHelper = new ReservationHelper($pdo);
    $oldStatus = $reservation['status'];
    $newStatus = '';
    $successMessage = '';
    $emailSubject = '';
    $emailMessage = '';

    if ($action === 'confirm') {
        if ($reservation['status'] !== 'pending') {
            $_SESSION['error'] = 'Sadece beklemedeki rezervasyonlar onaylanabilir.';
            header('Location: ../pages/admin/reservations.php');
            exit();
        }
        
        $newStatus = 'confirmed';
        $successMessage = 'Rezervasyon onaylandı.';
        $emailSubject = 'Rezervasyon Onaylandı - #' . $reservationId;
        $emailMessage = "
        <html>
        <head><title>Rezervasyon Onaylandı</title></head>
        <body>
            <h2>Rezervasyon Onaylandı!</h2>
            <p>Sayın {$reservation['first_name']} {$reservation['last_name']},</p>
            <p>Rezervasyonunuz onaylanmıştır. İyi tatiller dileriz!</p>
            
            <h3>Rezervasyon Detayları:</h3>
            <ul>
                <li><strong>Rezervasyon No:</strong> #{$reservationId}</li>
                <li><strong>Oda:</strong> {$reservation['room_name']}</li>
                <li><strong>Giriş:</strong> " . date('d.m.Y', strtotime($reservation['checkin_date'])) . "</li>
                <li><strong>Çıkış:</strong> " . date('d.m.Y', strtotime($reservation['checkout_date'])) . "</li>
                <li><strong>Toplam:</strong> ₺" . number_format($reservation['total_price'], 2) . "</li>
            </ul>
            
            <p><strong>Otel Rezervasyon Sistemi</strong></p>
        </body>
        </html>
        ";

    } elseif ($action === 'cancel') {
        if (in_array($reservation['status'], ['cancelled','completed'], true)) {
            $_SESSION['error'] = 'Bu rezervasyon zaten iptal/tamamlandı.';
            header('Location: ../pages/admin/reservations.php');
            exit();
        }
        
        $newStatus = 'cancelled';
        $successMessage = 'Rezervasyon iptal edildi.';
        $emailSubject = 'Rezervasyon İptal Edildi - #' . $reservationId;
        $emailMessage = "
        <html>
        <head><title>Rezervasyon İptal Edildi</title></head>
        <body>
            <h2>Rezervasyon İptal Edildi</h2>
            <p>Sayın {$reservation['first_name']} {$reservation['last_name']},</p>
            <p>Rezervasyonunuz iptal edilmiştir.</p>
            
            <h3>Rezervasyon Bilgileri:</h3>
            <ul>
                <li><strong>Rezervasyon No:</strong> #{$reservationId}</li>
                <li><strong>Oda:</strong> {$reservation['room_name']}</li>
                <li><strong>Tarih:</strong> " . date('d.m.Y', strtotime($reservation['checkin_date'])) . " - " . date('d.m.Y', strtotime($reservation['checkout_date'])) . "</li>
            </ul>
            
            <p>Yeni rezervasyon yapmak için sitemizi ziyaret edebilirsiniz.</p>
            <p><strong>Otel Rezervasyon Sistemi</strong></p>
        </body>
        </html>
        ";

    } elseif ($action === 'complete') {
        if ($reservation['status'] !== 'confirmed') {
            $_SESSION['error'] = 'Sadece onaylanmış rezervasyonlar tamamlanabilir.';
            header('Location: ../pages/admin/reservations.php');
            exit();
        }
        
        $newStatus = 'completed';
        $successMessage = 'Rezervasyon tamamlandı.';
        $emailSubject = 'Rezervasyon Tamamlandı - #' . $reservationId;
        $emailMessage = "
        <html>
        <head><title>Rezervasyon Tamamlandı</title></head>
        <body>
            <h2>Rezervasyon Tamamlandı</h2>
            <p>Sayın {$reservation['first_name']} {$reservation['last_name']},</p>
            <p>Rezervasyonunuz başarıyla tamamlanmıştır. Tekrar görüşmek üzere!</p>
            
            <h3>Rezervasyon Bilgileri:</h3>
            <ul>
                <li><strong>Rezervasyon No:</strong> #{$reservationId}</li>
                <li><strong>Oda:</strong> {$reservation['room_name']}</li>
                <li><strong>Tarih:</strong> " . date('d.m.Y', strtotime($reservation['checkin_date'])) . " - " . date('d.m.Y', strtotime($reservation['checkout_date'])) . "</li>
            </ul>
            
            <p>Yeni rezervasyon yapmak için sitemizi ziyaret edebilirsiniz.</p>
            <p><strong>Otel Rezervasyon Sistemi</strong></p>
        </body>
        </html>
        ";
    }

    if ($newStatus) {
        // Rezervasyon durumunu güncelle
        $upd = $pdo->prepare("UPDATE reservations SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $upd->execute([$newStatus, $reservationId]);
        
        // E-posta gönder
        if ($reservation['email']) {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@otel.com" . "\r\n";
            
            mail($reservation['email'], $emailSubject, $emailMessage, $headers);
        }
        
        $_SESSION['success'] = $successMessage;
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'İşlem sırasında hata: ' . $e->getMessage();
}

header('Location: ../pages/admin/reservations.php');
exit();
?>