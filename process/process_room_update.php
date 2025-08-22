<?php
session_start();
require_once '../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu işlemi gerçekleştirme yetkiniz yok.';
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form verilerini al
    $room_id = (int)($_POST['room_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';
    $capacity = (int)($_POST['capacity'] ?? 2);
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');
    $image = trim($_POST['image'] ?? '');
    
    // Validasyon
    if ($room_id <= 0 || empty($name) || empty($type) || $capacity < 1 || $price <= 0) {
        $_SESSION['error'] = 'Lütfen tüm zorunlu alanları doldurun ve geçerli değerler girin.';
        header('Location: ../pages/admin/rooms.php');
        exit();
    }
    
    // Oda var mı kontrol et
    $stmt = $pdo->prepare("SELECT id FROM rooms WHERE id = ?");
    $stmt->execute([$room_id]);
    if (!$stmt->fetch()) {
        $_SESSION['error'] = 'Oda bulunamadı.';
        header('Location: ../pages/admin/rooms.php');
        exit();
    }
    
    try {
        // Oda güncelle (mevcut veritabanı yapışıyla uyumlu)
        $stmt = $pdo->prepare("
            UPDATE rooms SET 
                name = ?, type = ?, price = ?, capacity = ?, description = ?, amenities = ?, status = ?, image = ?
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $name, $type, $price, $capacity, $description, $amenities, $status, $image, $room_id
        ]);
        
        if ($result) {
            $_SESSION['success'] = 'Oda başarıyla güncellendi.';
        } else {
            $_SESSION['error'] = 'Oda güncellenirken bir hata oluştu.';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    }
    
} else {
    $_SESSION['error'] = 'Geçersiz istek metodu.';
}

header('Location: ../pages/admin/rooms.php');
exit();
?>
