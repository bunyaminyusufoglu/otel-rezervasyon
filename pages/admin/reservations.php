<?php
include '../../includes/header.php';
require_once '../../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ../../index.php');
    exit();
}

$statusFilter = $_GET['status'] ?? '';
$whereSql = '';
$params = [];
if (in_array($statusFilter, ['pending','confirmed','cancelled','completed'], true)) {
    $whereSql = 'WHERE r.status = ?';
    $params[] = $statusFilter;
}

$stmt = $pdo->prepare("
    SELECT r.*, u.first_name, u.last_name, u.email, rm.name AS room_name, rm.type AS room_type
    FROM reservations r
    LEFT JOIN users u ON r.user_id = u.id
    LEFT JOIN rooms rm ON r.room_id = rm.id
    $whereSql
    ORDER BY r.created_at DESC
");
$stmt->execute($params);
$reservations = $stmt->fetchAll();
?>

<section class="py-4">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0"><i class="bi bi-speedometer2"></i> Rezervasyon Yönetimi</h1>
      <div>
        <a href="?" class="btn btn-sm btn-outline-secondary">Tümü</a>
        <a href="?status=pending" class="btn btn-sm btn-outline-warning">Beklemede</a>
        <a href="?status=confirmed" class="btn btn-sm btn-outline-success">Onaylandı</a>
        <a href="?status=cancelled" class="btn btn-sm btn-outline-danger">İptal</a>
        <a href="?status=completed" class="btn btn-sm btn-outline-info">Tamamlandı</a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (empty($reservations)): ?>
          <p class="text-muted mb-0">Kayıt bulunamadı.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Kullanıcı</th>
                  <th>E-posta</th>
                  <th>Oda</th>
                  <th>Tarih</th>
                  <th>Durum</th>
                  <th>Toplam</th>
                  <th class="text-end">İşlemler</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($reservations as $res): ?>
                <tr>
                  <td><?php echo (int)$res['id']; ?></td>
                  <td><?php echo htmlspecialchars(($res['first_name'] ?? 'Misafir') . ' ' . ($res['last_name'] ?? '')); ?></td>
                  <td><?php echo htmlspecialchars($res['email'] ?? '-'); ?></td>
                  <td>
                    <?php echo htmlspecialchars($res['room_name']); ?><br>
                    <small class="text-muted"><?php echo htmlspecialchars($res['room_type']); ?></small>
                  </td>
                  <td>
                    <?php echo date('d.m.Y', strtotime($res['checkin_date'])); ?> -
                    <?php echo date('d.m.Y', strtotime($res['checkout_date'])); ?>
                  </td>
                  <td>
                    <?php
                      $badge='secondary'; $text=$res['status'];
                      if ($res['status']==='pending') {$badge='warning'; $text='Beklemede';}
                      elseif ($res['status']==='confirmed') {$badge='success'; $text='Onaylandı';}
                      elseif ($res['status']==='cancelled') {$badge='danger'; $text='İptal';}
                      elseif ($res['status']==='completed') {$badge='info'; $text='Tamamlandı';}
                    ?>
                    <span class="badge bg-<?php echo $badge; ?>"><?php echo $text; ?></span>
                  </td>
                  <td><strong>₺<?php echo number_format($res['total_price'], 2); ?></strong></td>
                  <td class="text-end">
                    <button 
                      class="btn btn-sm btn-outline-primary" 
                      data-bs-toggle="modal" 
                      data-bs-target="#editReservationModal"
                      data-id="<?php echo (int)$res['id']; ?>"
                      data-room_type="<?php echo htmlspecialchars($res['room_type']); ?>"
                      data-checkin_date="<?php echo htmlspecialchars($res['checkin_date']); ?>"
                      data-checkout_date="<?php echo htmlspecialchars($res['checkout_date']); ?>"
                      data-guests="<?php echo (int)$res['guests']; ?>"
                      data-special_requests="<?php echo htmlspecialchars($res['special_requests']); ?>"
                      data-status="<?php echo htmlspecialchars($res['status']); ?>"
                    >
                      Düzenle
                    </button>

                    <form method="POST" action="../../process/process_reservation_status.php" class="d-inline ms-1">
                      <input type="hidden" name="reservation_id" value="<?php echo (int)$res['id']; ?>">
                      <input type="hidden" name="action" value="confirm">
                      <button class="btn btn-sm btn-success" <?php echo $res['status']!=='pending' ? 'disabled' : ''; ?>><i class="bi bi-check-circle"></i> Onayla</button>
                    </form>
                    <form method="POST" action="../../process/process_reservation_status.php" class="d-inline ms-1">
                      <input type="hidden" name="reservation_id" value="<?php echo (int)$res['id']; ?>">
                      <input type="hidden" name="action" value="complete">
                      <button class="btn btn-sm btn-info" <?php echo $res['status']!=='confirmed' ? 'disabled' : ''; ?>><i class="bi bi-check2-all"></i> Tamamla</button>
                    </form>
                    <form method="POST" action="../../process/process_reservation_status.php" class="d-inline ms-1">
                      <input type="hidden" name="reservation_id" value="<?php echo (int)$res['id']; ?>">
                      <input type="hidden" name="action" value="cancel">
                      <button class="btn btn-sm btn-outline-danger" <?php echo in_array($res['status'],['cancelled','completed'],true) ? 'disabled' : ''; ?>><i class="bi bi-x-circle"></i> İptal Et</button>
                    </form>
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
</section>

<!-- Edit Reservation Modal -->
<div class="modal fade" id="editReservationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Rezervasyonu Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_reservation_update.php">
        <div class="modal-body">
          <input type="hidden" id="res_id" name="reservation_id">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label" for="res_room_type">Oda Tipi</label>
              <select class="form-select" id="res_room_type" name="room_type" required>
                <option value="standard">Standart</option>
                <option value="deluxe">Deluxe</option>
                <option value="suite">Suite</option>
                <option value="family">Aile</option>
                <option value="economy">Ekonomik</option>
                <option value="premium">Premium</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="res_checkin">Giriş Tarihi</label>
              <input type="date" class="form-control" id="res_checkin" name="checkin_date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="res_checkout">Çıkış Tarihi</label>
              <input type="date" class="form-control" id="res_checkout" name="checkout_date" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="res_guests">Misafir</label>
              <input type="number" class="form-control" id="res_guests" name="guests" min="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="res_status">Durum</label>
              <select class="form-select" id="res_status" name="status">
                <option value="pending">Beklemede</option>
                <option value="confirmed">Onaylandı</option>
                <option value="cancelled">İptal</option>
                <option value="completed">Tamamlandı</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label" for="res_special">Özel İstekler</label>
              <textarea class="form-control" id="res_special" name="special_requests" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
          <button type="submit" class="btn btn-primary">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editReservationModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      document.getElementById('res_id').value = btn.getAttribute('data-id');
      document.getElementById('res_room_type').value = btn.getAttribute('data-room_type');
      document.getElementById('res_checkin').value = btn.getAttribute('data-checkin_date');
      document.getElementById('res_checkout').value = btn.getAttribute('data-checkout_date');
      document.getElementById('res_guests').value = btn.getAttribute('data-guests');
      document.getElementById('res_special').value = btn.getAttribute('data-special_requests');
      document.getElementById('res_status').value = btn.getAttribute('data-status');
      // tarih min
      const today = new Date().toISOString().split('T')[0];
      document.getElementById('res_checkin').min = today;
      document.getElementById('res_checkout').min = document.getElementById('res_checkin').value || today;
    });
    document.getElementById('res_checkin')?.addEventListener('change', function(){
      document.getElementById('res_checkout').min = this.value;
      const out = document.getElementById('res_checkout');
      if (out.value && out.value <= this.value) out.value = '';
    });
  });
</script>

<?php include '../../includes/footer.php'; ?>