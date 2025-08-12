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
    $user_id = $_POST['user_id'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? '';
    
    // Validasyon
    if (empty($user_id) || empty($first_name) || empty($last_name) || empty($email) || empty($role)) {
        $_SESSION['error'] = 'Tüm zorunlu alanları doldurun.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Geçerli bir e-posta adresi girin.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    if (!in_array($role, ['admin', 'customer'], true)) {
        $_SESSION['error'] = 'Geçersiz rol.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    // Kendi rolünü değiştirmeye çalışıyorsa engelle
    if ($user_id == $_SESSION['user_id'] && $role !== 'admin') {
        $_SESSION['error'] = 'Kendi rolünüzü değiştiremezsiniz.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    try {
        // E-posta adresi başka bir kullanıcıda var mı kontrol et
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.';
            header('Location: ../pages/admin/users.php');
            exit();
        }
        
        // Kullanıcıyı güncelle
        $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $result = $stmt->execute([$first_name, $last_name, $email, $phone, $role, $user_id]);
        
        if ($result) {
            $_SESSION['success'] = 'Kullanıcı bilgileri başarıyla güncellendi.';
        } else {
            $_SESSION['error'] = 'Kullanıcı güncellenirken bir hata oluştu.';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Veritabanı hatası: ' . $e->getMessage();
    }
    
} else {
    $_SESSION['error'] = 'Geçersiz istek metodu.';
}

header('Location: ../pages/admin/users.php');
exit();
?>
