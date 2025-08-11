<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/ReservationHelper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Admin engeli
    if (($_SESSION['user_role'] ?? 'customer') === 'admin') {
        $_SESSION['error'] = 'Yöneticiler rezervasyon oluşturamaz.';
        header('Location: ../pages/admin/reservations.php');
        exit();
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $room_type = $_POST['room_type'];
    $guests = (int)$_POST['guests'];
    $special_requests = trim($_POST['special_requests'] ?? '');

    $errors = [];

    // Validation
    if (empty($first_name)) {
        $errors[] = "Ad alanı zorunludur.";
    }

    if (empty($last_name)) {
        $errors[] = "Soyad alanı zorunludur.";
    }

    if (empty($email)) {
        $errors[] = "E-posta alanı zorunludur.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Geçerli bir e-posta adresi giriniz.";
    }

    if (empty($phone)) {
        $errors[] = "Telefon alanı zorunludur.";
    }

    if (empty($checkin_date)) {
        $errors[] = "Giriş tarihi zorunludur.";
    }

    if (empty($checkout_date)) {
        $errors[] = "Çıkış tarihi zorunludur.";
    }

    if (empty($room_type)) {
        $errors[] = "Oda tipi seçimi zorunludur.";
    }

    if ($guests < 1) {
        $errors[] = "Misafir sayısı en az 1 olmalıdır.";
    }

    // Tarih kontrolü
    if (!empty($checkin_date) && !empty($checkout_date)) {
        $checkin = new DateTime($checkin_date);
        $checkout = new DateTime($checkout_date);
        $today = new DateTime();
        $today->setTime(0, 0, 0); // Bugünün başlangıcı

        if ($checkin < $today) {
            $errors[] = "Giriş tarihi bugünden önce olamaz.";
        }

        if ($checkout <= $checkin) {
            $errors[] = "Çıkış tarihi giriş tarihinden sonra olmalıdır.";
        }

        // Maksimum konaklama süresi kontrolü (30 gün)
        $max_stay = $checkin->diff($checkout)->days;
        if ($max_stay > 30) {
            $errors[] = "Maksimum konaklama süresi 30 gündür.";
        }
    }

    if (empty($errors)) {
        try {
            $reservationHelper = new ReservationHelper($pdo);
            
            // 1. Oda müsaitlik kontrolü
            $availability = $reservationHelper->checkRoomAvailability($room_type, $checkin_date, $checkout_date);
            if (!$availability['available']) {
                $errors[] = $availability['message'];
            }
            
            // 2. Kullanıcının mevcut rezervasyonları ile çakışma kontrolü
            $user_id = $_SESSION['user_id'];
            $existing_reservations = $reservationHelper->checkUserReservations($user_id, $checkin_date, $checkout_date);
            if ($existing_reservations > 0) {
                $errors[] = "Seçilen tarihlerde zaten bir rezervasyonunuz bulunmaktadır.";
            }
            
            // 3. Dinamik fiyat hesaplama
            $price_calculation = $reservationHelper->calculatePrice($room_type, $checkin_date, $checkout_date);
            if (!$price_calculation['success']) {
                $errors[] = $price_calculation['message'];
            }
            
            if (empty($errors)) {
                // Oda ID'sini al
                $stmt = $pdo->prepare("SELECT id FROM rooms WHERE type = ?");
                $stmt->execute([$room_type]);
                $room = $stmt->fetch();
                
                if (!$room) {
                    $errors[] = "Seçilen oda tipi bulunamadı.";
                } else {
                    $room_id = $room['id'];
                    $total_price = $price_calculation['total_price'];
                    $nights = $price_calculation['nights'];
                    
                    // Rezervasyon oluştur
                    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, room_id, checkin_date, checkout_date, guests, total_price, special_requests, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
                    $stmt->execute([$user_id, $room_id, $checkin_date, $checkout_date, $guests, $total_price, $special_requests]);
                    
                    $reservation_id = $pdo->lastInsertId();
                    
                    // 4. Rezervasyon onay e-postası gönder
                    $reservation_data = [
                        'id' => $reservation_id,
                        'room_type' => $room_type,
                        'checkin_date' => $checkin_date,
                        'checkout_date' => $checkout_date,
                        'guests' => $guests,
                        'total_price' => $total_price
                    ];
                    
                    $email_result = $reservationHelper->sendConfirmationEmail($email, $first_name . ' ' . $last_name, $reservation_data);
                    
                    // Başarı mesajı
                    $success_message = "Rezervasyonunuz başarıyla oluşturuldu! No: #" . $reservation_id;
                    
                    if ($nights >= 7) {
                        $success_message .= " (7+ gece konaklama için %10 indirim uygulandı)";
                    }
                    
                    if ($email_result['success']) {
                        $success_message .= " Onay e-postası gönderildi.";
                    } else {
                        $success_message .= " E-posta gönderilemedi: " . $email_result['message'];
                    }
                    
                    $_SESSION['success'] = $success_message;
                    header("Location: ../pages/reservation/reservation_success.php?id=" . $reservation_id);
                    exit();
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Rezervasyon sırasında bir hata oluştu: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../pages/reservation/rezervasyon.php");
        exit();
    }
} else {
    header("Location: ../pages/reservation/rezervasyon.php");
    exit();
}
?> 