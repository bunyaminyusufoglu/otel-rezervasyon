<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $errors = [];

    // Validation
    if (empty($email)) {
        $errors[] = "E-posta alanı zorunludur.";
    }

    if (empty($password)) {
        $errors[] = "Şifre alanı zorunludur.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Debug: Kullanıcı bulundu mu?
            if (!$user) {
                $errors[] = "Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı.";
            } else {
                // Debug: Şifre doğru mu?
                if (password_verify($password, $user['password'])) {
                    // Giriş başarılı
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    
                    if ($remember) {
                        // Remember me cookie (30 gün)
                        setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
                    }
                    
                    $_SESSION['success'] = "Hoş geldiniz, " . $user['first_name'] . "!";
                    header("Location: ../index.php");
                    exit();
                } else {
                    $errors[] = "Şifre hatalı.";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Giriş sırasında bir hata oluştu: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ../pages/auth/login.php");
        exit();
    }
} else {
    header("Location: ../pages/auth/login.php");
    exit();
}
?> 