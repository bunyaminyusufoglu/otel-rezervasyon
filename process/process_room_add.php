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
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? '';
    $capacity = (int)($_POST['capacity'] ?? 2);
    $price = (float)($_POST['price'] ?? 0);
    $status = $_POST['status'] ?? 'available';
    $description = trim($_POST['description'] ?? '');
    $amenities = trim($_POST['amenities'] ?? '');
    
    // Validasyon
    if (empty($name) || empty($type) || $capacity < 1 || $price <= 0) {
        $_SESSION['error'] = 'Lütfen tüm zorunlu alanları doldurun ve geçerli değerler girin.';
        header('Location: ../pages/admin/rooms.php');
        exit();
    }
    
    try {
        // Oda ekle (mevcut veritabanı yapısıyla uyumlu)
        $stmt = $pdo->prepare("
            INSERT INTO rooms (name, type, price, capacity, description, amenities, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $name, $type, $price, $capacity, $description, $amenities, $status
        ]);
        
        if ($result) {
            $_SESSION['success'] = "Oda başarıyla eklendi.";
            
        } else {
            $_SESSION['error'] = 'Oda eklenirken bir hata oluştu.';
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
