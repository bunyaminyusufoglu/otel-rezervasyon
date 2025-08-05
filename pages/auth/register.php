<?php include '../../includes/header.php'; ?>

<!-- Register Form -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-person-plus"></i> Yeni Hesap Oluştur</h3>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="../../process/process_register.php">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="first_name" class="form-label">Ad *</label>
                  <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Soyad *</label>
                  <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">E-posta *</label>
                  <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="col-md-6">
                  <label for="phone" class="form-label">Telefon *</label>
                  <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="col-md-6">
                  <label for="password" class="form-label">Şifre *</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                  <div class="form-text">En az 8 karakter olmalıdır.</div>
                </div>
                <div class="col-md-6">
                  <label for="confirm_password" class="form-label">Şifre Tekrar *</label>
                  <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="col-12">
                  <label for="address" class="form-label">Adres</label>
                  <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                    <label class="form-check-label" for="newsletter">
                      E-posta ile kampanya ve fırsatlardan haberdar olmak istiyorum.
                    </label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                      <a href="#" class="text-decoration-none">Kullanım şartlarını</a> ve <a href="#" class="text-decoration-none">gizlilik politikasını</a> okudum ve kabul ediyorum.
                    </label>
                  </div>
                </div>
                <div class="col-12 text-center mt-4">
                  <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="bi bi-person-plus"></i> Hesap Oluştur
                  </button>
                </div>
              </div>
            </form>
            <hr class="my-4">
            <div class="text-center">
              <p class="mb-2">Zaten hesabınız var mı?</p>
              <a href="login.php" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-right"></i> Giriş Yap
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Benefits -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="fw-bold text-center mb-4">Üye Olmanın Avantajları</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-star display-4 text-warning mb-3"></i>
          <h5>Özel Fırsatlar</h5>
          <p>Üyelere özel indirimler ve kampanyalar.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-calendar-check display-4 text-success mb-3"></i>
          <h5>Kolay Rezervasyon</h5>
          <p>Hızlı ve kolay rezervasyon yapma imkanı.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center">
          <i class="bi bi-gear display-4 text-primary mb-3"></i>
          <h5>Hesap Yönetimi</h5>
          <p>Rezervasyonlarınızı kolayca yönetin.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?> 