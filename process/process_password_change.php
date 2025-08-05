<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Giriş kontrolü
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Bu işlem için giriş yapmalısınız.";
        header("Location: ../pages/auth/login.php");
        exit();
    }

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    $errors = [];

    // Validation
    if (empty($current_password)) {
        $errors[] = "Mevcut şifre alanı zorunludur.";
    }

    if (empty($new_password)) {
        $errors[] = "Yeni şifre alanı zorunludur.";
    } elseif (strlen($new_password) < 8) {
        $errors[] = "Yeni şifre en az 8 karakter olmalıdır.";
    }

    if ($new_password !== $confirm_new_password) {
        $errors[] = "Yeni şifreler eşleşmiyor.";
    }

    if (empty($errors)) {
        try {
            // Mevcut şifreyi kontrol et
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($current_password, $user['password'])) {
                $errors[] = "Mevcut şifre hatalı.";
            } else {
                // Yeni şifreyi hashle ve güncelle
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
                $stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                $_SESSION['success'] = "Şifreniz başarıyla değiştirildi.";
                header("Location: ../pages/auth/profile.php");
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Şifre değiştirilirken bir hata oluştu: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../pages/auth/profile.php");
        exit();
    }
} else {
    header("Location: ../pages/auth/profile.php");
    exit();
}
?> 