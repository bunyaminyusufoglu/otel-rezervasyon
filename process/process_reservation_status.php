<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu işlem için yetkiniz yok.';
    header('Location: ../index.php');
    exit();
}

$reservationId = (int)($_POST['reservation_id'] ?? 0);
$action = $_POST['action'] ?? '';

if ($reservationId <= 0 || !in_array($action, ['confirm','cancel'], true)) {
    $_SESSION['error'] = 'Geçersiz istek.';
    header('Location: ../pages/admin/reservations.php');
    exit();
}

try {
    // Rezervasyon mevcut mu?
    $stmt = $pdo->prepare('SELECT status FROM reservations WHERE id = ?');
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        $_SESSION['error'] = 'Rezervasyon bulunamadı.';
        header('Location: ../pages/admin/reservations.php');
        exit();
    }

    if ($action === 'confirm') {
        if ($reservation['status'] !== 'pending') {
            $_SESSION['error'] = 'Sadece beklemedeki rezervasyonlar onaylanabilir.';
        } else {
            $upd = $pdo->prepare("UPDATE reservations SET status = 'confirmed', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->execute([$reservationId]);
            $_SESSION['success'] = 'Rezervasyon onaylandı.';
        }
    } elseif ($action === 'cancel') {
        if (in_array($reservation['status'], ['cancelled','completed'], true)) {
            $_SESSION['error'] = 'Bu rezervasyon zaten iptal/tamamlandı.';
        } else {
            $upd = $pdo->prepare("UPDATE reservations SET status = 'cancelled', updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $upd->execute([$reservationId]);
            $_SESSION['success'] = 'Rezervasyon iptal edildi.';
        }
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'İşlem sırasında hata: ' . $e->getMessage();
}

header('Location: ../pages/admin/reservations.php');
exit();