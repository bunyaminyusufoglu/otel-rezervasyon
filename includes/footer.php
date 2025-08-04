    </div> <!-- container div'ini kapat -->
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">   
                <div class="col-md-4 mb-4">
                    <h5><i class="bi bi-building"></i> Otel Rezervasyon</h5>
                    <p class="text-muted">Türkiye'nin en iyi otellerinde lüksü, konforu ve huzuru bir arada yaşayın.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-light me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Hızlı Linkler</h5>
                    <ul class="list-unstyled">
                        <li><a href="/otel-rezervasyon/index.php" class="text-muted text-decoration-none">Ana Sayfa</a></li>
                        <li><a href="/otel-rezervasyon/pages/rooms/rooms.php" class="text-muted text-decoration-none">Odalar</a></li>
                        <li><a href="/otel-rezervasyon/pages/reservation/rezervasyon.php" class="text-muted text-decoration-none">Rezervasyon</a></li>
                        <li><a href="/otel-rezervasyon/pages/auth/login.php" class="text-muted text-decoration-none">Giriş Yap</a></li>
                        <li><a href="/otel-rezervasyon/pages/auth/register.php" class="text-muted text-decoration-none">Kayıt Ol</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>İletişim</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-geo-alt"></i> İstanbul, Türkiye</li>
                        <li><i class="bi bi-telephone"></i> +90 555 123 4567</li>
                        <li><i class="bi bi-envelope"></i> info@otelsitesi.com</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">&copy; 2024 Otel Rezervasyon. Tüm hakları saklıdır.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Gizlilik Politikası</a>
                    <a href="#" class="text-muted text-decoration-none">Kullanım Şartları</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Form validation için tarih kontrolü
        document.addEventListener('DOMContentLoaded', function() {
            const checkinInput = document.getElementById('checkin_date');
            const checkoutInput = document.getElementById('checkout_date');
            
            if (checkinInput && checkoutInput) {
                // Minimum tarih bugün
                const today = new Date().toISOString().split('T')[0];
                checkinInput.min = today;
                
                checkinInput.addEventListener('change', function() {
                    checkoutInput.min = this.value;
                    if (checkoutInput.value && checkoutInput.value <= this.value) {
                        checkoutInput.value = '';
                    }
                });
            }
        });
    </script>
</body>
</html> 