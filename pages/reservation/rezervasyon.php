<?php 
include '../../includes/header.php'; 

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Rezervasyon yapmak için önce giriş yapmalısınız.";
    header("Location: ../auth/login.php");
    exit();
}

// Admin engeli
if (($_SESSION['user_role'] ?? 'customer') === 'admin') {
    $_SESSION['error'] = "Yöneticiler rezervasyon oluşturamaz. Lütfen müşteri hesabı ile giriş yapın.";
    header("Location: ../admin/reservations.php");
    exit();
}

// Kullanıcı bilgilerini al
require_once '../../includes/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!-- Hero Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-4 fw-bold mb-4 text-primary">Rezervasyon Yap</h1>
        <p class="lead mb-4">Hayalinizdeki oteli seçin ve hemen rezervasyon yapın.</p>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <img src="../../assets/reservation-hero.jpg" alt="Rezervasyon" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</section>

<!-- Reservation Form -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-calendar-check"></i> Rezervasyon Formu</h3>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="../../process/process_reservation.php">
              <div class="row g-3">
                <!-- Kişisel Bilgiler -->
                <div class="col-12">
                  <h5 class="text-primary mb-3">Kişisel Bilgiler</h5>
                </div>
                <div class="col-md-6">
                  <label for="first_name" class="form-label">Ad *</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required readonly>
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Soyad *</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required readonly>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">E-posta *</label>
                  <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Telefon *</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required readonly>
                </div>

                <!-- Rezervasyon Detayları -->
                <div class="col-12">
                  <h5 class="text-primary mb-3 mt-4">Rezervasyon Detayları</h5>
                </div>
                <div class="col-md-6">
                  <label for="checkin_date" class="form-label">Giriş Tarihi *</label>
                  <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
                </div>
                <div class="col-md-6">
                  <label for="checkout_date" class="form-label">Çıkış Tarihi *</label>
                  <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
                </div>
                <div class="col-md-6">
                  <label for="room_type" class="form-label">Oda Tipi *</label>
                  <select class="form-select" id="room_type" name="room_type" required>
                    <option value="">Oda tipi seçin</option>
                    <option value="standard">Standart Oda - ₺500/gece</option>
                    <option value="deluxe">Deluxe Oda - ₺800/gece</option>
                    <option value="suite">Suite - ₺1200/gece</option>
                    <option value="family">Aile Odası - ₺900/gece</option>
                    <option value="economy">Ekonomik Oda - ₺300/gece</option>
                    <option value="premium">Premium Suite - ₺1500/gece</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="guests" class="form-label">Misafir Sayısı *</label>
                  <select class="form-select" id="guests" name="guests" required>
                    <option value="">Misafir sayısı seçin</option>
                    <option value="1">1 Kişi</option>
                    <option value="2">2 Kişi</option>
                    <option value="3">3 Kişi</option>
                    <option value="4">4 Kişi</option>
                    <option value="5">5+ Kişi</option>
                  </select>
                </div>

                <!-- Özel İstekler -->
                <div class="col-12">
                  <label for="special_requests" class="form-label">Özel İstekler</label>
                  <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Varsa özel isteklerinizi buraya yazabilirsiniz..."></textarea>
                </div>

                <!-- Onay -->
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                      <a href="#" class="text-decoration-none">Kullanım şartlarını</a> ve <a href="#" class="text-decoration-none">gizlilik politikasını</a> okudum ve kabul ediyorum.
                    </label>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center mt-4">
                  <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-check-circle"></i> Rezervasyonu Onayla
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Reservation Info -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Rezervasyon Bilgileri</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-clock display-4 text-primary mb-3"></i>
          <h5>Hızlı Rezervasyon</h5>
          <p>5 dakika içinde rezervasyonunuzu tamamlayın.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-shield-check display-4 text-success mb-3"></i>
          <h5>Güvenli Ödeme</h5>
          <p>SSL şifreleme ile güvenli ödeme işlemleri.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-arrow-clockwise display-4 text-warning mb-3"></i>
          <h5>Ücretsiz İptal</h5>
          <p>24 saat öncesine kadar ücretsiz iptal imkanı.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?> 