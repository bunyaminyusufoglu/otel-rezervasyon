<?php
session_start();

// Session'ı temizle
session_destroy();

// Remember me cookie'sini sil
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, '/');
}

// Ana sayfaya yönlendir
$_SESSION['success'] = "Başarıyla çıkış yaptınız.";
header("Location: ../index.php");
exit();
?> 