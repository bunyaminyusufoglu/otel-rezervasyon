<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otel Rezervasyon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-gradient mb-4">
  <div class="container">
    <a class="navbar-brand" href="/otel-rezervasyon/index.php">
      <i class="bi bi-building"></i> Otel Rezervasyon
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/index.php"><i class="bi bi-house-door"></i> Ana Sayfa</a></li>
        <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/odalar.php"><i class="bi bi-door-open"></i> Odalar</a></li>
        <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/rezervasyon.php"><i class="bi bi-calendar-check"></i> Rezervasyon</a></li>
        <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/login.php"><i class="bi bi-box-arrow-in-right"></i> Giriş</a></li>
        <li class="nav-item"><a class="nav-link" href="/register.php"><i class="bi bi-person-plus"></i> Kayıt Ol</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php
// Hata ve başarı mesajlarını göster
if (isset($_SESSION['errors'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    foreach ($_SESSION['errors'] as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['errors']);
}

if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['success']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['success']);
}
?>
<div class="container"> 