<?php session_start(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otel Rezervasyon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/otel-rezervasyon/assets/css/style.css" rel="stylesheet">
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
        <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/pages/rooms/odalar.php"><i class="bi bi-door-open"></i> Odalar</a></li>
        <?php if (($_SESSION['user_role'] ?? 'customer') !== 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/pages/reservation/rezervasyon.php"><i class="bi bi-calendar-check"></i> Rezervasyon</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </a>
            <ul class="dropdown-menu">
              <?php if (($_SESSION['user_role'] ?? 'customer') === 'admin'): ?>
                <li><a class="dropdown-item" href="/otel-rezervasyon/pages/admin/reservations.php"><i class="bi bi-speedometer2"></i> Yönetim</a></li>
                <li><hr class="dropdown-divider"></li>
              <?php endif; ?>
              <li><a class="dropdown-item" href="/otel-rezervasyon/pages/auth/profile.php"><i class="bi bi-person"></i> Profilim</a></li>
              <?php if (($_SESSION['user_role'] ?? 'customer') !== 'admin'): ?>
                <li><a class="dropdown-item" href="/otel-rezervasyon/pages/reservation/rezervasyon.php"><i class="bi bi-calendar-plus"></i> Yeni Rezervasyon</a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="/otel-rezervasyon/process/process_logout.php"><i class="bi bi-box-arrow-right"></i> Çıkış Yap</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/pages/auth/login.php"><i class="bi bi-box-arrow-in-right"></i> Giriş</a></li>
          <li class="nav-item"><a class="nav-link" href="/otel-rezervasyon/pages/auth/register.php"><i class="bi bi-person-plus"></i> Kayıt Ol</a></li>
        <?php endif; ?>
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

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo htmlspecialchars($_SESSION['error']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    echo '</div>';
    unset($_SESSION['error']);
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