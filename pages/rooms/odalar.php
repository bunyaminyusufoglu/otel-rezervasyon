<?php include '../../includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-4 fw-bold mb-4 text-primary">Odalarımız</h1>
        <p class="lead mb-4">Her bütçeye ve zevke uygun, konforlu ve lüks odalarımızı keşfedin.</p>
        <a href="#room-list" class="btn btn-primary btn-lg px-4 rounded-pill">Odaları İncele</a>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <img src="../../assets/images/rooms-hero.jpg" alt="Oda Görseli" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</section>

<!-- Room Categories -->
<section class="py-5 bg-white text-center">
  <div class="container">
    <h2 class="fw-bold mb-4">Oda Kategorileri</h2>
    <div class="row g-4">
      <div class="col-md-3">
        <i class="bi bi-house display-4 text-primary"></i>
        <h5 class="mt-3">Standart Oda</h5>
        <p>Konforlu ve ekonomik konaklama seçeneği.</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-house-heart display-4 text-success"></i>
        <h5 class="mt-3">Deluxe Oda</h5>
        <p>Geniş ve lüks donanımlı odalar.</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-house-gear display-4 text-warning"></i>
        <h5 class="mt-3">Suite</h5>
        <p>En üst düzey konfor ve özel hizmetler.</p>
      </div>
      <div class="col-md-3">
        <i class="bi bi-house-door display-4 text-info"></i>
        <h5 class="mt-3">Aile Odası</h5>
        <p>Geniş aileler için ideal konaklama.</p>
      </div>
    </div>
  </div>
</section>

<!-- Room List -->
<section id="room-list" class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Mevcut Odalar</h2>
    <div class="row g-4">
      <!-- Standart Oda -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-standard.jpg" class="card-img-top" alt="Standart Oda">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Standart Oda</h5>
            <p class="card-text">25m² alanında, şehir manzaralı, konforlu konaklama imkanı.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 1 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺500/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Deluxe Oda -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-deluxe.jpg" class="card-img-top" alt="Deluxe Oda">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Deluxe Oda</h5>
            <p class="card-text">35m² alanında, deniz manzaralı, lüks donanımlı oda.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 1 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
              <li><i class="bi bi-check-circle text-success"></i> Balkon</li>
              <li><i class="bi bi-check-circle text-success"></i> Mini Bar</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺800/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Suite -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-suite.jpg" class="card-img-top" alt="Suite">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Suite</h5>
            <p class="card-text">50m² alanında, en üst düzey konfor ve özel hizmetler.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 2 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
              <li><i class="bi bi-check-circle text-success"></i> Geniş Balkon</li>
              <li><i class="bi bi-check-circle text-success"></i> Mini Bar</li>
              <li><i class="bi bi-check-circle text-success"></i> Oturma Alanı</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺1200/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Aile Odası -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-family.jpg" class="card-img-top" alt="Aile Odası">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Aile Odası</h5>
            <p class="card-text">40m² alanında, geniş aileler için ideal konaklama.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 2 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
              <li><i class="bi bi-check-circle text-success"></i> Balkon</li>
              <li><i class="bi bi-check-circle text-success"></i> Çocuk Yatakları</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺900/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Ekonomik Oda -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-economy.jpg" class="card-img-top" alt="Ekonomik Oda">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Ekonomik Oda</h5>
            <p class="card-text">20m² alanında, bütçe dostu konaklama seçeneği.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 1 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺300/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Premium Suite -->
      <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
          <img src="../../assets/images/room-premium.jpg" class="card-img-top" alt="Premium Suite">
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">Premium Suite</h5>
            <p class="card-text">70m² alanında, en lüks konaklama deneyimi.</p>
            <ul class="list-unstyled mb-3">
              <li><i class="bi bi-check-circle text-success"></i> 2 Yatak Odası</li>
              <li><i class="bi bi-check-circle text-success"></i> Özel Banyo</li>
              <li><i class="bi bi-check-circle text-success"></i> Klima</li>
              <li><i class="bi bi-check-circle text-success"></i> Ücretsiz Wi-Fi</li>
              <li><i class="bi bi-check-circle text-success"></i> Geniş Balkon</li>
              <li><i class="bi bi-check-circle text-success"></i> Mini Bar</li>
              <li><i class="bi bi-check-circle text-success"></i> Oturma Alanı</li>
              <li><i class="bi bi-check-circle text-success"></i> Jakuzi</li>
            </ul>
            <div class="mt-auto">
              <p class="text-primary fw-bold mb-2">₺1500/gece</p>
              <a href="#" class="btn btn-outline-primary">Rezervasyon Yap</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Room Amenities -->
<section class="py-5 bg-white">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Oda Özellikleri</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-wifi display-4 text-primary mb-3"></i>
          <h5>Ücretsiz Wi-Fi</h5>
          <p>Tüm odalarımızda yüksek hızlı internet erişimi.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-snow display-4 text-info mb-3"></i>
          <h5>Klima</h5>
          <p>Merkezi klima sistemi ile ideal sıcaklık.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-tv display-4 text-success mb-3"></i>
          <h5>LED TV</h5>
          <p>Uydu yayınlı büyük ekran televizyonlar.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-shield-check display-4 text-warning mb-3"></i>
          <h5>Güvenlik</h5>
          <p>24 saat güvenlik ve elektronik kilit sistemi.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-droplet display-4 text-primary mb-3"></i>
          <h5>Özel Banyo</h5>
          <p>Her odada özel banyo ve duş imkanı.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-cup-hot display-4 text-danger mb-3"></i>
          <h5>Kahve Makinesi</h5>
          <p>Odalarda ücretsiz kahve ve çay servisi.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h2 class="fw-bold mb-3">Hemen Rezervasyon Yapın</h2>
    <p class="lead mb-4">En uygun fiyatlarla hayalinizdeki odayı rezerve edin.</p>
    <a href="../../pages/reservation/rezervasyon.php" class="btn btn-light btn-lg px-4 rounded-pill">Rezervasyon Yap</a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?> 