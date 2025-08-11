<?php
include '../../includes/header.php';
require_once '../../includes/db.php';

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Profil sayfasına erişmek için giriş yapmalısınız.";
    header("Location: login.php");
    exit();
}

// Kullanıcı bilgilerini al
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Kullanıcının rezervasyonlarını al
$stmt = $pdo->prepare("
    SELECT r.*, rm.name as room_name, rm.type as room_type 
    FROM reservations r 
    LEFT JOIN rooms rm ON r.room_id = rm.id 
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-4 fw-bold mb-4 text-primary">Profilim</h1>
        <p class="lead mb-4">Kişisel bilgilerinizi güncelleyin ve rezervasyon geçmişinizi görüntüleyin.</p>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <i class="bi bi-person-circle display-1 text-primary"></i>
      </div>
    </div>
  </div>
</section>

<!-- Profile Content -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row">
      <!-- Profile Form -->
      <div class="col-lg-6 mb-5">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-person-gear"></i> Profil Bilgileri</h3>
          </div>
          <div class="card-body p-4">
            <?php if (isset($_SESSION['success'])): ?>
              <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
              <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="../../process/process_profile_update.php">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="first_name" class="form-label">Ad *</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Soyad *</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">E-posta *</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Telefon</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>
                <div class="col-12">
                  <label for="address" class="form-label">Adres</label>
                  <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" <?php echo ($user['newsletter'] ?? false) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="newsletter">
                      Bülten ve kampanyalardan haberdar olmak istiyorum
                    </label>
                  </div>
                </div>
                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-check-circle"></i> Güncelle
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        
        <!-- Password Change Form -->
        <div class="card shadow mt-4">
          <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="bi bi-key"></i> Şifre Değiştir</h4>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="../../process/process_password_change.php">
              <div class="row g-3">
                <div class="col-12">
                  <label for="current_password" class="form-label">Mevcut Şifre *</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="col-md-6">
                  <label for="new_password" class="form-label">Yeni Şifre *</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                </div>
                <div class="col-md-6">
                  <label for="confirm_password" class="form-label">Şifre Tekrar *</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                </div>
                <div class="col-12 text-center">
                  <button type="submit" class="btn btn-warning btn-lg px-5">
                    <i class="bi bi-shield-lock"></i> Şifre Değiştir
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <!-- Reservation History -->
      <div class="col-lg-6">
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            <h3 class="mb-0"><i class="bi bi-calendar-check"></i> Rezervasyon Geçmişi</h3>
          </div>
          <div class="card-body p-4">
            <?php if (empty($reservations)): ?>
              <div class="text-center py-4">
                <i class="bi bi-calendar-x display-4 text-muted"></i>
                <p class="lead text-muted mt-3">Henüz rezervasyon yapmadınız.</p>
                <a href="../reservation/rezervasyon.php" class="btn btn-primary btn-lg">
                  <i class="bi bi-plus-circle"></i> İlk Rezervasyonunuzu Yapın
                </a>
              </div>
            <?php else: ?>
              <div class="accordion" id="reservationAccordion">
                <?php foreach ($reservations as $index => $reservation): ?>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $index; ?>">
                      <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>">
                        <div class="d-flex justify-content-between align-items-center w-100 me-3">
                          <span>
                            <strong>#<?php echo $reservation['id']; ?></strong> - 
                            <?php echo htmlspecialchars($reservation['room_name']); ?>
                          </span>
                          <?php
                            $badge = 'secondary';
                            $text = $reservation['status'];
                            if ($reservation['status'] === 'pending') {$badge = 'warning'; $text = 'Beklemede';}
                            elseif ($reservation['status'] === 'confirmed') {$badge = 'success'; $text = 'Onaylandı';}
                            elseif ($reservation['status'] === 'cancelled') {$badge = 'danger'; $text = 'İptal';}
                            elseif ($reservation['status'] === 'completed') {$badge = 'info'; $text = 'Tamamlandı';}
                          ?>
                          <span class="badge bg-<?php echo $badge; ?>"><?php echo $text; ?></span>
                        </div>
                      </button>
                    </h2>
                    <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#reservationAccordion">
                      <div class="accordion-body">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <p class="mb-1"><strong>Giriş:</strong><br><?php echo date('d.m.Y', strtotime($reservation['checkin_date'])); ?></p>
                            <p class="mb-1"><strong>Çıkış:</strong><br><?php echo date('d.m.Y', strtotime($reservation['checkout_date'])); ?></p>
                          </div>
                          <div class="col-md-6">
                            <p class="mb-1"><strong>Misafir:</strong><br><?php echo $reservation['guests']; ?> kişi</p>
                            <p class="mb-1"><strong>Toplam:</strong><br>₺<?php echo number_format($reservation['total_price'], 2); ?></p>
                          </div>
                          <?php if (!empty($reservation['special_requests'])): ?>
                            <div class="col-12">
                              <p class="mb-1"><strong>Özel İstekler:</strong><br><?php echo htmlspecialchars($reservation['special_requests']); ?></p>
                            </div>
                          <?php endif; ?>
                          <div class="col-12">
                            <p class="mb-0 text-muted">
                              <small>Oluşturulma: <?php echo date('d.m.Y H:i', strtotime($reservation['created_at'])); ?></small>
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="text-center mt-4">
                <a href="../reservation/rezervasyon.php" class="btn btn-success btn-lg">
                  <i class="bi bi-plus-circle"></i> Yeni Rezervasyon
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Şifre eşleşme kontrolü
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Şifreler eşleşmiyor');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>

<?php include '../../includes/footer.php'; ?> 