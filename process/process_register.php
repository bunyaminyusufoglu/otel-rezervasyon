<?php
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = trim($_POST['address'] ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $terms = isset($_POST['terms']) ? 1 : 0;

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

    if (empty($password)) {
        $errors[] = "Şifre alanı zorunludur.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Şifre en az 8 karakter olmalıdır.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Şifreler eşleşmiyor.";
    }

    if (!$terms) {
        $errors[] = "Kullanım şartlarını kabul etmelisiniz.";
    }

    // E-posta kontrolü
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Bu e-posta adresi zaten kullanılıyor.";
        }
    }

    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password, address, newsletter) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password, $address, $newsletter]);
            
            $_SESSION['success'] = "Hesabınız başarıyla oluşturuldu. Şimdi giriş yapabilirsiniz.";
            header("Location: ../../pages/auth/login.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Kayıt sırasında bir hata oluştu: " . $e->getMessage();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: ../../pages/auth/register.php");
        exit();
    }
} else {
    header("Location: ../../pages/auth/register.php");
    exit();
}
?> 