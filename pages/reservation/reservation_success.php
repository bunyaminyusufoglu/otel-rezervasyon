<?php 
include '../../includes/header.php'; 
?>

<!-- Success Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 text-center">
        <div class="card shadow">
          <div class="card-body p-5">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill display-1 text-success"></i>
            </div>
            <h1 class="display-4 fw-bold text-success mb-4">Rezervasyon Başarılı!</h1>
            <p class="lead mb-4">Rezervasyonunuz başarıyla oluşturuldu. Rezervasyon detaylarınız aşağıda yer almaktadır.</p>
            
            <?php if (isset($_GET['id'])): ?>
            <div class="alert alert-info">
              <strong>Rezervasyon Numarası:</strong> #<?php echo htmlspecialchars($_GET['id']); ?>
            </div>
            <?php endif; ?>
            
            <div class="row g-4 mt-4">
              <div class="col-md-6">
                <div class="card border-0 bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-envelope-fill display-4 text-primary mb-3"></i>
                    <h5>E-posta Onayı</h5>
                    <p class="text-muted">Rezervasyon detayları e-posta adresinize gönderilecektir.</p>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card border-0 bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-clock-fill display-4 text-warning mb-3"></i>
                    <h5>24 Saat İçinde</h5>
                    <p class="text-muted">Rezervasyonunuz 24 saat içinde onaylanacaktır.</p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-5">
              <a href="../../index.php" class="btn btn-primary btn-lg me-3">
                <i class="bi bi-house-door"></i> Ana Sayfa
              </a>
              <a href="rezervasyon.php" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-calendar-plus"></i> Yeni Rezervasyon
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Important Information -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Önemli Bilgiler</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-credit-card display-4 text-success mb-3"></i>
          <h5>Ödeme</h5>
          <p>Ödemenizi otelde yapabilirsiniz. Kredi kartı, nakit veya banka kartı kabul edilir.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-clock-history display-4 text-warning mb-3"></i>
          <h5>Check-in</h5>
          <p>Check-in saatleri: 14:00 - 00:00. Erken check-in için lütfen önceden bilgi verin.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-x-circle display-4 text-danger mb-3"></i>
          <h5>İptal</h5>
          <p>Rezervasyon iptali için giriş tarihinden 24 saat öncesine kadar ücretsizdir.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?> 