<?php 
include '../../includes/header.php'; 
require_once '../../includes/CSRFHelper.php';

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
        <img src="../../assets/images/reservation-hero.jpg" alt="Rezervasyon" class="img-fluid rounded shadow">
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
            <form method="POST" action="../../process/process_reservation.php" id="reservationForm">
              <?php echo CSRFHelper::getTokenField(); ?>
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
                  <input type="date" class="form-control" id="checkin_date" name="checkin_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-6">
                  <label for="checkout_date" class="form-label">Çıkış Tarihi *</label>
                  <input type="date" class="form-control" id="checkout_date" name="checkout_date" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
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
                    <option value="5">5 Kişi</option>
                    <option value="6">6 Kişi</option>
                  </select>
                </div>
                <div class="col-12">
                  <label for="special_requests" class="form-label">Özel İstekler</label>
                  <textarea class="form-control" id="special_requests" name="special_requests" rows="3" placeholder="Varsa özel isteklerinizi belirtin..."></textarea>
                </div>

                <!-- Fiyat Özeti -->
                <div class="col-12">
                  <div class="card bg-light border-0" id="priceSummary" style="display: none;">
                    <div class="card-body">
                      <h6 class="text-primary mb-3"><i class="bi bi-calculator"></i> Fiyat Özeti</h6>
                      <div class="row">
                        <div class="col-md-6">
                          <p class="mb-1"><strong>Oda Tipi:</strong> <span id="selectedRoomType">-</span></p>
                          <p class="mb-1"><strong>Giriş:</strong> <span id="selectedCheckin">-</span></p>
                          <p class="mb-1"><strong>Çıkış:</strong> <span id="selectedCheckout">-</span></p>
                          <p class="mb-1"><strong>Gece Sayısı:</strong> <span id="nightsCount">-</span></p>
                        </div>
                        <div class="col-md-6">
                          <p class="mb-1"><strong>Gecelik Ücret:</strong> ₺<span id="nightlyPrice">-</span></p>
                          <p class="mb-1"><strong>İndirim:</strong> <span id="discountInfo">-</span></p>
                          <p class="mb-1"><strong>Toplam:</strong> ₺<span id="totalPrice">-</span></p>
                        </div>
                      </div>
                      <div class="alert alert-info mt-2 mb-0" id="availabilityStatus">
                        <i class="bi bi-info-circle"></i> <span id="availabilityText">Müsaitlik kontrol ediliyor...</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 text-center mt-4">
                  <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn" disabled>
                    <i class="bi bi-check-circle"></i> Rezervasyon Yap
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkinDate = document.getElementById('checkin_date');
    const checkoutDate = document.getElementById('checkout_date');
    const roomType = document.getElementById('room_type');
    const guests = document.getElementById('guests');
    const priceSummary = document.getElementById('priceSummary');
    const submitBtn = document.getElementById('submitBtn');
    
    // Tarih değişikliklerini dinle
    [checkinDate, checkoutDate, roomType, guests].forEach(element => {
        element.addEventListener('change', calculatePrice);
    });
    
    // Minimum checkout tarihini güncelle
    checkinDate.addEventListener('change', function() {
        if (this.value) {
            const minCheckout = new Date(this.value);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkoutDate.min = minCheckout.toISOString().split('T')[0];
            
            // Eğer checkout tarihi artık geçersizse, temizle
            if (checkoutDate.value && checkoutDate.value <= this.value) {
                checkoutDate.value = '';
            }
        }
    });
    
    function calculatePrice() {
        if (!checkinDate.value || !checkoutDate.value || !roomType.value || !guests.value) {
            priceSummary.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        
        // Gece sayısını hesapla
        const checkin = new Date(checkinDate.value);
        const checkout = new Date(checkoutDate.value);
        const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
        
        if (nights < 1) {
            priceSummary.style.display = 'none';
            submitBtn.disabled = true;
            return;
        }
        
        // Oda fiyatlarını tanımla
        const roomPrices = {
            'standard': 500,
            'deluxe': 800,
            'suite': 1200,
            'family': 900,
            'economy': 300,
            'premium': 1500
        };
        
        const basePrice = roomPrices[roomType.value];
        let totalPrice = basePrice * nights;
        let discount = 0;
        
        // 7+ gece indirimi
        if (nights >= 7) {
            discount = totalPrice * 0.1;
            totalPrice = totalPrice * 0.9;
        }
        
        // Fiyat özetini güncelle
        document.getElementById('selectedRoomType').textContent = roomType.options[roomType.selectedIndex].text;
        document.getElementById('selectedCheckin').textContent = new Date(checkinDate.value).toLocaleDateString('tr-TR');
        document.getElementById('selectedCheckout').textContent = new Date(checkoutDate.value).toLocaleDateString('tr-TR');
        document.getElementById('nightsCount').textContent = nights + ' gece';
        document.getElementById('nightlyPrice').textContent = basePrice.toFixed(2);
        document.getElementById('discountInfo').textContent = discount > 0 ? `%10 (₺${discount.toFixed(2)})` : 'Yok';
        document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
        
        // Gerçek zamanlı müsaitlik kontrolü
        checkAvailability(roomType.value, checkinDate.value, checkoutDate.value);
        
        priceSummary.style.display = 'block';
        submitBtn.disabled = true; // Müsaitlik kontrolü tamamlanana kadar devre dışı
    }
    
    function checkAvailability(roomType, checkin, checkout) {
        const availabilityStatus = document.getElementById('availabilityStatus');
        const availabilityText = document.getElementById('availabilityText');
        
        // AJAX ile müsaitlik kontrolü
        const formData = new FormData();
        formData.append('room_type', roomType);
        formData.append('checkin_date', checkin);
        formData.append('checkout_date', checkout);
        formData.append('csrf_token', '<?php echo CSRFHelper::generateToken(); ?>');
        
        fetch('../../process/check_availability.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.available) {
                    availabilityStatus.className = 'alert alert-success mt-2 mb-0';
                    availabilityText.innerHTML = '<i class="bi bi-check-circle"></i> Seçilen tarihlerde oda müsait!';
                    submitBtn.disabled = false;
                    
                    // Fiyat bilgilerini güncelle (sunucudan gelen verilerle)
                    if (data.price_info && data.price_info.success) {
                        document.getElementById('nightsCount').textContent = data.price_info.nights + ' gece';
                        document.getElementById('nightlyPrice').textContent = data.price_info.base_price.toFixed(2);
                        document.getElementById('discountInfo').textContent = data.price_info.discount > 0 ? `%${data.price_info.discount} (₺${(data.price_info.base_price * data.price_info.nights * data.price_info.discount / 100).toFixed(2)})` : 'Yok';
                        document.getElementById('totalPrice').textContent = data.price_info.total_price.toFixed(2);
                    }
                } else {
                    availabilityStatus.className = 'alert alert-warning mt-2 mb-0';
                    availabilityText.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + data.message;
                    submitBtn.disabled = true;
                }
            } else {
                availabilityStatus.className = 'alert alert-danger mt-2 mb-0';
                availabilityText.innerHTML = '<i class="bi bi-x-circle"></i> Hata: ' + data.message;
                submitBtn.disabled = true;
            }
        })
        .catch(error => {
            availabilityStatus.className = 'alert alert-danger mt-2 mb-0';
            availabilityText.innerHTML = '<i class="bi bi-x-circle"></i> Bağlantı hatası: ' + error.message;
            submitBtn.disabled = true;
        });
    }
});
</script>

<?php include '../../includes/footer.php'; ?> 