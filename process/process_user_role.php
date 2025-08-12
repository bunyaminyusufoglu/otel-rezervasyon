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
    $action = $_POST['action'] ?? '';
    
    if (empty($user_id) || empty($action)) {
        $_SESSION['error'] = 'Geçersiz istek.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    // Kendi rolünü değiştirmeye çalışıyorsa engelle
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = 'Kendi rolünüzü değiştiremezsiniz.';
        header('Location: ../pages/admin/users.php');
        exit();
    }
    
    try {
        $new_role = '';
        
        if ($action === 'make_admin') {
            $new_role = 'admin';
        } elseif ($action === 'make_customer') {
            $new_role = 'customer';
        } else {
            $_SESSION['error'] = 'Geçersiz işlem.';
            header('Location: ../pages/admin/users.php');
            exit();
        }
        
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $result = $stmt->execute([$new_role, $user_id]);
        
        if ($result) {
            $role_text = $new_role === 'admin' ? 'admin' : 'müşteri';
            $_SESSION['success'] = "Kullanıcı başarıyla $role_text olarak ayarlandı.";
        } else {
            $_SESSION['error'] = 'Rol güncellenirken bir hata oluştu.';
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
