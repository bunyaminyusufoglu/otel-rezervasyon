<?php 
include '../../includes/header.php'; 

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Profil sayfasına erişmek için giriş yapmalısınız.";
    header("Location: login.php");
    exit();
}

// Kullanıcı bilgilerini al
require_once '../../includes/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Kullanıcının rezervasyonlarını al
$stmt = $pdo->prepare("
    SELECT r.*, rm.name as room_name, rm.type as room_type 
    FROM reservations r 
    JOIN rooms rm ON r.room_id = rm.id 
    WHERE r.user_id = ? 
    ORDER BY r.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();
?>

<!-- Profile Header -->
<section class="bg-light py-5">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h1 class="display-4 fw-bold mb-3 text-primary">Profilim</h1>
        <p class="lead mb-0">Hoş geldiniz, <?php echo htmlspecialchars($user['first_name']); ?>!</p>
      </div>
      <div class="col-lg-4 text-end">
        <a href="../../pages/reservation/rezervasyon.php" class="btn btn-primary">
          <i class="bi bi-calendar-plus"></i> Yeni Rezervasyon
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Profile Content -->
<section class="py-5">
  <div class="container">
    <div class="row">
      <!-- Sol Kolon - Profil Bilgileri -->
      <div class="col-lg-4 mb-4">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-person-circle"></i> Profil Bilgileri</h5>
          </div>
          <div class="card-body">
            <div class="text-center mb-4">
              <i class="bi bi-person-circle display-1 text-primary"></i>
            </div>
            <div class="mb-3">
              <strong>Ad Soyad:</strong><br>
              <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
            </div>
            <div class="mb-3">
              <strong>E-posta:</strong><br>
              <?php echo htmlspecialchars($user['email']); ?>
            </div>
            <div class="mb-3">
              <strong>Telefon:</strong><br>
              <?php echo htmlspecialchars($user['phone']); ?>
            </div>
            <?php if (!empty($user['address'])): ?>
            <div class="mb-3">
              <strong>Adres:</strong><br>
              <?php echo htmlspecialchars($user['address']); ?>
            </div>
            <?php endif; ?>
            <div class="mb-3">
              <strong>Üyelik Tarihi:</strong><br>
              <?php echo date('d.m.Y', strtotime($user['created_at'])); ?>
            </div>
            <div class="d-grid gap-2">
              <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil"></i> Profili Düzenle
              </button>
              <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-key"></i> Şifre Değiştir
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Sağ Kolon - Rezervasyon Geçmişi -->
      <div class="col-lg-8">
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Rezervasyon Geçmişi</h5>
          </div>
          <div class="card-body">
            <?php if (empty($reservations)): ?>
              <div class="text-center py-4">
                <i class="bi bi-calendar-x display-4 text-muted mb-3"></i>
                <h5 class="text-muted">Henüz rezervasyonunuz bulunmuyor</h5>
                <p class="text-muted">İlk rezervasyonunuzu yapmak için aşağıdaki butona tıklayın.</p>
                <a href="../../pages/reservation/rezervasyon.php" class="btn btn-primary">
                  <i class="bi bi-calendar-plus"></i> Rezervasyon Yap
                </a>
              </div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Rezervasyon No</th>
                      <th>Oda</th>
                      <th>Tarih</th>
                      <th>Durum</th>
                      <th>Toplam</th>
                      <th>İşlem</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                    <tr>
                      <td><strong>#<?php echo $reservation['id']; ?></strong></td>
                      <td><?php echo htmlspecialchars($reservation['room_name']); ?></td>
                      <td>
                        <?php echo date('d.m.Y', strtotime($reservation['checkin_date'])); ?> - 
                        <?php echo date('d.m.Y', strtotime($reservation['checkout_date'])); ?>
                      </td>
                      <td>
                        <?php 
                        $status_class = '';
                        $status_text = '';
                        switch($reservation['status']) {
                            case 'pending': $status_class = 'warning'; $status_text = 'Beklemede'; break;
                            case 'confirmed': $status_class = 'success'; $status_text = 'Onaylandı'; break;
                            case 'cancelled': $status_class = 'danger'; $status_text = 'İptal Edildi'; break;
                            case 'completed': $status_class = 'info'; $status_text = 'Tamamlandı'; break;
                        }
                        ?>
                        <span class="badge bg-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                      </td>
                      <td><strong>₺<?php echo number_format($reservation['total_price'], 2); ?></strong></td>
                      <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#reservationDetailModal" data-reservation='<?php echo json_encode($reservation); ?>'>
                          <i class="bi bi-eye"></i> Detay
                        </button>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Profil Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_profile_update.php">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="edit_first_name" class="form-label">Ad</label>
              <input type="text" class="form-control" id="edit_first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="edit_last_name" class="form-label">Soyad</label>
              <input type="text" class="form-control" id="edit_last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="edit_email" class="form-label">E-posta</label>
              <input type="email" class="form-control" id="edit_email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="edit_phone" class="form-label">Telefon</label>
              <input type="tel" class="form-control" id="edit_phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="col-12">
              <label for="edit_address" class="form-label">Adres</label>
              <textarea class="form-control" id="edit_address" name="address" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="btn btn-primary">Güncelle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Şifre Değiştir</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_password_change.php">
        <div class="modal-body">
          <div class="mb-3">
            <label for="current_password" class="form-label">Mevcut Şifre</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
          </div>
          <div class="mb-3">
            <label for="new_password" class="form-label">Yeni Şifre</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
            <div class="form-text">En az 8 karakter olmalıdır.</div>
          </div>
          <div class="mb-3">
            <label for="confirm_new_password" class="form-label">Yeni Şifre Tekrar</label>
            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="btn btn-primary">Şifreyi Değiştir</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reservation Detail Modal -->
<div class="modal fade" id="reservationDetailModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Rezervasyon Detayı</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="reservationDetailContent">
        <!-- İçerik JavaScript ile doldurulacak -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>

<?php include '../../includes/footer.php'; ?> 