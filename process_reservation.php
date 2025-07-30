<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

        if ($checkin < $today) {
            $errors[] = "Giriş tarihi bugünden önce olamaz.";
        }

        if ($checkout <= $checkin) {
            $errors[] = "Çıkış tarihi giriş tarihinden sonra olmalıdır.";
        }
    }

    if (empty($errors)) {
        try {
            // Oda fiyatını al
            $stmt = $pdo->prepare("SELECT price FROM rooms WHERE type = ?");
            $stmt->execute([$room_type]);
            $room = $stmt->fetch();

            if (!$room) {
                $errors[] = "Seçilen oda tipi bulunamadı.";
            } else {
                // Geceleri hesapla
                $checkin = new DateTime($checkin_date);
                $checkout = new DateTime($checkout_date);
                $nights = $checkin->diff($checkout)->days;
                $total_price = $room['price'] * $nights;

                // Kullanıcı ID'sini al (giriş yapmışsa)
                $user_id = $_SESSION['user_id'] ?? null;

                // Rezervasyon oluştur
                $stmt = $pdo->prepare("INSERT INTO reservations (user_id, room_id, checkin_date, checkout_date, guests, total_price, special_requests) VALUES (?, (SELECT id FROM rooms WHERE type = ?), ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $room_type, $checkin_date, $checkout_date, $guests, $total_price, $special_requests]);

                $reservation_id = $pdo->lastInsertId();

                $_SESSION['success'] = "Rezervasyonunuz başarıyla oluşturuldu. Rezervasyon numaranız: #" . $reservation_id;
                header("Location: reservation_success.php?id=" . $reservation_id);
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Rezervasyon sırasında bir hata oluştu: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: rezervasyon.php");
        exit();
    }
} else {
    header("Location: rezervasyon.php");
    exit();
}
?> 