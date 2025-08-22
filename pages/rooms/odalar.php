<?php include '../../includes/header.php'; ?>
<?php require_once '../../includes/db.php'; ?>

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

<!-- Room List (Dynamic) -->
<section id="room-list" class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Mevcut Odalar</h2>
    <?php
      try {
        $stmt = $pdo->prepare("SELECT id, name, type, description, price, capacity, amenities, image, status FROM rooms ORDER BY price ASC");
        $stmt->execute();
        $rooms = $stmt->fetchAll();
      } catch (PDOException $e) {
        $rooms = [];
        echo '<p class="text-danger text-center">Odalar yüklenirken bir hata oluştu.</p>';
      }

      $typeToImage = [
        'standard' => 'room-standard.jpg',
        'deluxe'   => 'room-deluxe.jpg',
        'suite'    => 'room-suite.jpg',
        'family'   => 'room-family.jpg',
        'economy'  => 'room-economy.jpg',
        'premium'  => 'room-premium.jpg',
      ];
    ?>

    <div class="row g-4">
      <?php if (empty($rooms)): ?>
        <div class="col-12">
          <div class="alert alert-info text-center mb-0">Şu anda görüntülenecek oda bulunmuyor.</div>
        </div>
      <?php else: ?>
        <?php foreach ($rooms as $room): ?>
          <?php
            $type = $room['type'];
            $image = trim($room['image'] ?? '');
            $imagePath = '../../assets/images/' . ($typeToImage[$type] ?? 'room-standard.jpg');
            $isAvailable = ($room['status'] ?? 'available') === 'available';
            $amenities = array_filter(array_map('trim', explode(',', (string)($room['amenities'] ?? ''))));
            $amenities = array_slice($amenities, 0, 6);
          ?>
          <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100">
              <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($room['name']); ?>">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($room['description'] ?? ''); ?></p>

                <?php if (!empty($amenities)): ?>
                  <ul class="list-unstyled mb-3">
                    <?php foreach ($amenities as $am): ?>
                      <li><i class="bi bi-check-circle text-success"></i> <?php echo htmlspecialchars($am); ?></li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>

                <div class="mt-auto d-flex align-items-center justify-content-between">
                  <p class="text-primary fw-bold mb-0">₺<?php echo number_format((float)$room['price'], 2); ?>/gece</p>
                  <?php if ($isAvailable): ?>
                    <a href="../../pages/reservation/rezervasyon.php?type=<?php echo urlencode($type); ?>" class="btn btn-outline-primary">Rezervasyon Yap</a>
                  <?php else: ?>
                    <button class="btn btn-outline-secondary" disabled>Dolu</button>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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