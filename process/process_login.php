<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/CSRFHelper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token doğrulama
    if (!CSRFHelper::validatePostToken()) {
        CSRFHelper::handleValidationFailure();
    }
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);

    $errors = [];

    if (empty($email)) { $errors[] = "E-posta alanı zorunludur."; }
    if (empty($password)) { $errors[] = "Şifre alanı zorunludur."; }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                $errors[] = "Bu e-posta adresi ile kayıtlı kullanıcı bulunamadı.";
            } else {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'] ?? 'customer';

                    if ($remember) {
                        setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
                    }

                    $_SESSION['success'] = "Hoş geldiniz, " . $user['first_name'] . "!";
                    
                    // Admin kullanıcıları dashboard'a, normal kullanıcıları ana sayfaya yönlendir
                    if (($user['role'] ?? 'customer') === 'admin') {
                        header("Location: ../pages/admin/dashboard.php");
                    } else {
                        header("Location: ../index.php");
                    }
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