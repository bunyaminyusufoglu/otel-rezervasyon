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
$roomType = $_POST['room_type'] ?? '';
$checkin = $_POST['checkin_date'] ?? '';
$checkout = $_POST['checkout_date'] ?? '';
$guests = (int)($_POST['guests'] ?? 1);
$status = $_POST['status'] ?? 'pending';
$special = trim($_POST['special_requests'] ?? '');

$errors = [];

if ($reservationId <= 0) { $errors[] = 'Geçersiz rezervasyon.'; }
if (!in_array($roomType, ['standard','deluxe','suite','family','economy','premium'], true)) { $errors[] = 'Geçersiz oda tipi.'; }
if (empty($checkin) || empty($checkout)) { $errors[] = 'Tarihler zorunludur.'; }
if ($guests < 1) { $errors[] = 'Misafir sayısı en az 1 olmalıdır.'; }
if (!in_array($status, ['pending','confirmed','cancelled','completed'], true)) { $errors[] = 'Geçersiz durum.'; }

if (empty($errors)) {
    try {
        $in = new DateTime($checkin);
        $out = new DateTime($checkout);
        if ($out <= $in) { $errors[] = 'Çıkış tarihi girişten sonra olmalıdır.'; }
        if ($in < (new DateTime('today'))) { $errors[] = 'Giriş tarihi bugünden önce olamaz.'; }
    } catch (Exception $e) {
        $errors[] = 'Geçersiz tarih formatı.';
    }
}

if (empty($errors)) {
    try {
        // Oda ücreti
        $stmt = $pdo->prepare('SELECT id, price FROM rooms WHERE type = ?');
        $stmt->execute([$roomType]);
        $room = $stmt->fetch();
        if (!$room) { $errors[] = 'Oda bulunamadı.'; }
        else {
            $nights = (new DateTime($checkin))->diff(new DateTime($checkout))->days;
            $total = $room['price'] * max(1,$nights);
            
            $upd = $pdo->prepare('UPDATE reservations SET room_id = ?, checkin_date = ?, checkout_date = ?, guests = ?, total_price = ?, special_requests = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
            $upd->execute([$room['id'], $checkin, $checkout, $guests, $total, $special, $status, $reservationId]);
            
            $_SESSION['success'] = 'Rezervasyon güncellendi.';
            header('Location: ../pages/admin/reservations.php');
            exit();
        }
    } catch (PDOException $e) {
        $errors[] = 'Güncelleme hatası: ' . $e->getMessage();
    }
}

$_SESSION['errors'] = $errors;
header('Location: ../pages/admin/reservations.php');
exit();