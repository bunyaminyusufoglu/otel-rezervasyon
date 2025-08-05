    </div> <!-- container div'ini kapat -->
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">   
                <div class="col-md-4 mb-4">
                    <h5><i class="bi bi-building"></i> Otel Rezervasyon</h5>
                    <p class="text-white">Türkiye'nin en iyi otellerinde lüksü, konforu ve huzuru bir arada yaşayın.</p>
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
                        <li><a href="/otel-rezervasyon/index.php" class="text-white text-decoration-none">Ana Sayfa</a></li>
                        <li><a href="/otel-rezervasyon/pages/rooms/rooms.php" class="text-white text-decoration-none">Odalar</a></li>
                        <li><a href="/otel-rezervasyon/pages/reservation/rezervasyon.php" class="text-white text-decoration-none">Rezervasyon</a></li>
                        <li><a href="/otel-rezervasyon/pages/auth/login.php" class="text-white text-decoration-none">Giriş Yap</a></li>
                        <li><a href="/otel-rezervasyon/pages/auth/register.php" class="text-white text-decoration-none">Kayıt Ol</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>İletişim</h5>
                    <ul class="list-unstyled text-white">
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
                    <a href="#" class="text-white text-decoration-none me-3">Gizlilik Politikası</a>
                    <a href="#" class="text-white text-decoration-none">Kullanım Şartları</a>
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

                    // Rezervasyon detay modalı için
                    const reservationDetailModal = document.getElementById('reservationDetailModal');
                    if (reservationDetailModal) {
                        reservationDetailModal.addEventListener('show.bs.modal', function (event) {
                            const button = event.relatedTarget;
                            const reservationData = JSON.parse(button.getAttribute('data-reservation'));
                            
                            const modalBody = document.getElementById('reservationDetailContent');
                            
                            // Durum badge'i için
                            let statusBadge = '';
                            switch(reservationData.status) {
                                case 'pending': statusBadge = '<span class="badge bg-warning">Beklemede</span>'; break;
                                case 'confirmed': statusBadge = '<span class="badge bg-success">Onaylandı</span>'; break;
                                case 'cancelled': statusBadge = '<span class="badge bg-danger">İptal Edildi</span>'; break;
                                case 'completed': statusBadge = '<span class="badge bg-info">Tamamlandı</span>'; break;
                            }

                            // Tarih formatı
                            const checkinDate = new Date(reservationData.checkin_date).toLocaleDateString('tr-TR');
                            const checkoutDate = new Date(reservationData.checkout_date).toLocaleDateString('tr-TR');
                            const createdDate = new Date(reservationData.created_at).toLocaleDateString('tr-TR');

                            modalBody.innerHTML = `
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Rezervasyon Bilgileri</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Rezervasyon No:</strong></td>
                                                <td>#${reservationData.id}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Oda:</strong></td>
                                                <td>${reservationData.room_name}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Oda Tipi:</strong></td>
                                                <td>${reservationData.room_type}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Giriş Tarihi:</strong></td>
                                                <td>${checkinDate}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Çıkış Tarihi:</strong></td>
                                                <td>${checkoutDate}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gece Sayısı:</strong></td>
                                                <td>${reservationData.nights} gece</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">Kişisel Bilgiler</h6>
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Ad Soyad:</strong></td>
                                                <td>${reservationData.first_name} ${reservationData.last_name}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>E-posta:</strong></td>
                                                <td>${reservationData.email}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Telefon:</strong></td>
                                                <td>${reservationData.phone}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Durum:</strong></td>
                                                <td>${statusBadge}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Rezervasyon Tarihi:</strong></td>
                                                <td>${createdDate}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary">Fiyat Bilgileri</h6>
                                        <div class="alert alert-info">
                                            <div class="d-flex justify-content-between">
                                                <span><strong>Toplam Tutar:</strong></span>
                                                <span><strong>₺${parseFloat(reservationData.total_price).toLocaleString('tr-TR', {minimumFractionDigits: 2})}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                ${reservationData.notes ? `
                                <div class="row">
                                    <div class="col-12">
                                        <h6 class="text-primary">Özel Notlar</h6>
                                        <div class="alert alert-light">
                                            ${reservationData.notes}
                                        </div>
                                    </div>
                                </div>
                                ` : ''}
                            `;
                        });
                    }
                });
            </script>
</body>
</html> 