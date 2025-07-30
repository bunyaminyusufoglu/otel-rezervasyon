<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6 text-center text-lg-start">
        <h1 class="display-4 fw-bold mb-4 text-primary">Giriş Yap</h1>
        <p class="lead mb-4">Hesabınıza giriş yaparak rezervasyonlarınızı yönetin.</p>
      </div>
      <div class="col-lg-6 text-center mt-4 mt-lg-0">
        <img src="assets/login-hero.jpg" alt="Giriş" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</section>

<!-- Login Form -->
<section class="py-5 bg-white">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h3 class="mb-0"><i class="bi bi-box-arrow-in-right"></i> Giriş Yap</h3>
          </div>
          <div class="card-body p-4">
            <form method="POST" action="process_login.php">
              <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">
                  Beni hatırla
                </label>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                  <i class="bi bi-box-arrow-in-right"></i> Giriş Yap
                </button>
              </div>
            </form>
            <hr class="my-4">
            <div class="text-center">
              <p class="mb-2">Hesabınız yok mu?</p>
              <a href="register.php" class="btn btn-outline-primary">
                <i class="bi bi-person-plus"></i> Kayıt Ol
              </a>
            </div>
            <div class="text-center mt-3">
              <a href="forgot_password.php" class="text-decoration-none">Şifremi unuttum</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?> 