<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/CSRFHelper.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token doğrulama
    if (!CSRFHelper::validatePostToken()) {
        CSRFHelper::handleValidationFailure();
    }
    // Giriş kontrolü
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Bu işlem için giriş yapmalısınız.";
        header("Location: ../pages/auth/login.php");
        exit();
    }

    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address'] ?? '');

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

    // E-posta kontrolü (başka kullanıcı tarafından kullanılıyor mu?)
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $errors[] = "Bu e-posta adresi başka bir kullanıcı tarafından kullanılıyor.";
        }
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $address, $_SESSION['user_id']]);
            
            // Session'daki kullanıcı adını güncelle
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $_SESSION['user_email'] = $email;
            
            $_SESSION['success'] = "Profil bilgileriniz başarıyla güncellendi.";
            header("Location: ../pages/auth/profile.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Profil güncellenirken bir hata oluştu: " . $e->getMessage();
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