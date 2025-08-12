<?php
include '../../includes/header.php';
require_once '../../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ../../index.php');
    exit();
}

$roleFilter = $_GET['role'] ?? '';
$whereSql = '';
$params = [];
if (in_array($roleFilter, ['admin', 'customer'], true)) {
    $whereSql = 'WHERE role = ?';
    $params[] = $roleFilter;
}

$stmt = $pdo->prepare("
    SELECT u.*, 
           COUNT(r.id) as reservation_count,
           SUM(CASE WHEN r.status = 'completed' THEN 1 ELSE 0 END) as completed_reservations
    FROM users u
    LEFT JOIN reservations r ON u.id = r.user_id
    $whereSql
    GROUP BY u.id
    ORDER BY u.created_at DESC
");
$stmt->execute($params);
$users = $stmt->fetchAll();
?>

<section class="py-4">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h1 class="h3 mb-0"><i class="bi bi-people"></i> Kullanıcı Yönetimi</h1>
      <div>
        <a href="?" class="btn btn-sm btn-outline-secondary">Tümü</a>
        <a href="?role=admin" class="btn btn-sm btn-outline-danger">Admin</a>
        <a href="?role=customer" class="btn btn-sm btn-outline-primary">Müşteri</a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <?php if (empty($users)): ?>
          <p class="text-muted mb-0">Kayıt bulunamadı.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Ad Soyad</th>
                  <th>E-posta</th>
                  <th>Telefon</th>
                  <th>Rol</th>
                  <th>Rezervasyon</th>
                  <th>Kayıt Tarihi</th>
                  <th class="text-end">İşlemler</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                  <td><?php echo (int)$user['id']; ?></td>
                  <td>
                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                      <span class="badge bg-info ms-2">Siz</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                  <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                  <td>
                    <?php
                      $badge = $user['role'] === 'admin' ? 'danger' : 'primary';
                      $text = $user['role'] === 'admin' ? 'Admin' : 'Müşteri';
                    ?>
                    <span class="badge bg-<?php echo $badge; ?>"><?php echo $text; ?></span>
                  </td>
                  <td>
                    <small class="text-muted">
                      Toplam: <strong><?php echo (int)$user['reservation_count']; ?></strong><br>
                      Tamamlanan: <strong><?php echo (int)$user['completed_reservations']; ?></strong>
                    </small>
                  </td>
                  <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                  <td class="text-end">
                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                      <button 
                        class="btn btn-sm btn-outline-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#editUserModal"
                        data-id="<?php echo (int)$user['id']; ?>"
                        data-first_name="<?php echo htmlspecialchars($user['first_name']); ?>"
                        data-last_name="<?php echo htmlspecialchars($user['last_name']); ?>"
                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                        data-phone="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                        data-role="<?php echo htmlspecialchars($user['role']); ?>"
                      >
                        Düzenle
                      </button>

                      <form method="POST" action="../../process/process_user_role.php" class="d-inline ms-1">
                        <input type="hidden" name="user_id" value="<?php echo (int)$user['id']; ?>">
                        <?php if ($user['role'] === 'admin'): ?>
                          <input type="hidden" name="action" value="make_customer">
                          <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Bu kullanıcıyı müşteri yapmak istediğinizden emin misiniz?')">
                            <i class="bi bi-person-down"></i> Müşteri Yap
                          </button>
                        <?php else: ?>
                          <input type="hidden" name="action" value="make_admin">
                          <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bu kullanıcıyı admin yapmak istediğinizden emin misiniz?')">
                            <i class="bi bi-person-up"></i> Admin Yap
                          </button>
                        <?php endif; ?>
                      </form>
                    <?php else: ?>
                      <span class="text-muted">Kendi hesabınız</span>
                    <?php endif; ?>
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kullanıcı Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_user_update.php">
        <div class="modal-body">
          <input type="hidden" id="user_id" name="user_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="user_first_name">Ad</label>
              <input type="text" class="form-control" id="user_first_name" name="first_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="user_last_name">Soyad</label>
              <input type="text" class="form-control" id="user_last_name" name="last_name" required>
            </div>
            <div class="col-12">
              <label class="form-label" for="user_email">E-posta</label>
              <input type="email" class="form-control" id="user_email" name="email" required>
            </div>
            <div class="col-12">
              <label class="form-label" for="user_phone">Telefon</label>
              <input type="tel" class="form-control" id="user_phone" name="phone">
            </div>
            <div class="col-12">
              <label class="form-label" for="user_role">Rol</label>
              <select class="form-select" id="user_role" name="role" required>
                <option value="customer">Müşteri</option>
                <option value="admin">Admin</option>
              </select>
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
    const modal = document.getElementById('editUserModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', function (event) {
      const btn = event.relatedTarget;
      document.getElementById('user_id').value = btn.getAttribute('data-id');
      document.getElementById('user_first_name').value = btn.getAttribute('data-first_name');
      document.getElementById('user_last_name').value = btn.getAttribute('data-last_name');
      document.getElementById('user_email').value = btn.getAttribute('data-email');
      document.getElementById('user_phone').value = btn.getAttribute('data-phone');
      document.getElementById('user_role').value = btn.getAttribute('data-role');
    });
  });
</script>

<?php include '../../includes/footer.php'; ?>
