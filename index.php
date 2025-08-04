<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-4 fw-bold mb-4 text-primary">Hayalinizdeki Otel Sizi Bekliyor</h1>
        <p class="lead mb-4">Türkiye’nin en iyi otellerinde lüksü, konforu ve huzuru bir arada yaşayın.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="pages/reservation/rezervasyon.php" class="btn btn-primary btn-lg px-4 rounded-pill">Rezervasyon Yap</a>
        <?php else: ?>
          <a href="pages/auth/login.php" class="btn btn-primary btn-lg px-4 rounded-pill">Giriş Yap ve Rezervasyon Yap</a>
        <?php endif; ?>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <img src="assets/hotel-hero.jpg" alt="Otel Görseli" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</section>

<!-- Why Choose Us -->
<section class="py-5 bg-white text-center">
  <div class="container">
    <h2 class="fw-bold mb-4">Neden Bizi Seçmelisiniz?</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <i class="bi bi-star-fill display-4 text-warning"></i>
        <h5 class="mt-3">Lüks Oteller</h5>
        <p>5 yıldızlı otellerde üst düzey konfor ve hizmet.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-cash-coin display-4 text-success"></i>
        <h5 class="mt-3">Uygun Fiyat Garantisi</h5>
        <p>Piyasanın en iyi fiyatlarıyla rezervasyon yapın.</p>
      </div>
      <div class="col-md-4">
        <i class="bi bi-shield-check display-4 text-primary"></i>
        <h5 class="mt-3">Güvenli Ödeme</h5>
        <p>256-bit şifreleme ile korunan ödeme sistemi.</p>
      </div>
    </div>
  </div>
</section>

<!-- Featured Hotels -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Popüler Oteller</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="assets/hotel1.jpg" class="card-img-top" alt="Hotel 1">
          <div class="card-body">
            <h5 class="card-title">İstanbul Şehir Oteli</h5>
            <p class="card-text">Tarihi yarımadaya yürüme mesafesinde, lüks konaklama imkanı.</p>
            <a href="#" class="btn btn-outline-primary">Detaylar</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="assets/hotel2.jpg" class="card-img-top" alt="Hotel 2">
          <div class="card-body">
            <h5 class="card-title">Antalya Resort</h5>
            <p class="card-text">Denize sıfır, her şey dahil konseptiyle unutulmaz bir tatil.</p>
            <a href="#" class="btn btn-outline-primary">Detaylar</a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm">
          <img src="assets/hotel3.jpg" class="card-img-top" alt="Hotel 3">
          <div class="card-body">
            <h5 class="card-title">Kapadokya Cave Hotel</h5>
            <p class="card-text">Doğal taş odalarda eşsiz bir konaklama deneyimi.</p>
            <a href="#" class="btn btn-outline-primary">Detaylar</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Reservation Form -->
<section id="reservation" class="py-5 bg-white">
  <div class="container">
    <h2 class="text-center fw-bold mb-4">Rezervasyon Yap</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
      <div class="text-center mb-4">
        <p class="lead">Hoş geldiniz, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        <a href="pages/reservation/rezervasyon.php" class="btn btn-primary btn-lg px-5">Rezervasyon Formuna Git</a>
      </div>
    <?php else: ?>
      <div class="text-center mb-4">
        <p class="lead">Rezervasyon yapmak için önce giriş yapmalısınız.</p>
        <a href="pages/auth/login.php" class="btn btn-primary btn-lg px-5 me-3">Giriş Yap</a>
        <a href="pages/auth/register.php" class="btn btn-outline-primary btn-lg px-5">Kayıt Ol</a>
      </div>
    <?php endif; ?>
  </div>
</section>


<!-- Contact -->
<section class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="fw-bold mb-4">Bize Ulaşın</h2>
    <p class="mb-3">Her türlü soru ve görüşünüz için bize ulaşabilirsiniz.</p>
    <p class="mb-1"><i class="bi bi-envelope-fill"></i> info@otelsitesi.com</p>
    <p><i class="bi bi-telephone-fill"></i> +90 555 123 4567</p>
    <!-- Harita yer tutucu -->
    <div class="mt-4">
      <iframe src="https://maps.google.com/maps?q=istanbul&t=&z=10&ie=UTF8&iwloc=&output=embed"
              width="100%" height="300" style="border:0;" allowfullscreen></iframe>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
