<?php
include '../../includes/header.php';
require_once '../../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ../../index.php');
    exit();
}

// Filtreleme parametreleri
$typeFilter = $_GET['type'] ?? '';
$statusFilter = $_GET['status'] ?? '';

// SQL sorgusu oluştur
$whereConditions = [];
$params = [];

if (!empty($typeFilter)) {
    $whereConditions[] = "r.type = ?";
    $params[] = $typeFilter;
}

if (!empty($statusFilter)) {
    $whereConditions[] = "r.status = ?";
    $params[] = $statusFilter;
}

// floor filter kaldırıldı (db şemasında yok)

$whereSql = '';
if (!empty($whereConditions)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereConditions);
}

// Odaları getir (geçici olarak room_photos tablosu olmadan)
$stmt = $pdo->prepare("
    SELECT r.*, 
           0 as photo_count,
           NULL as primary_photo
    FROM rooms r
    $whereSql
    ORDER BY r.id
");
$stmt->execute($params);
$rooms = $stmt->fetchAll();

// Oda tipleri ve durumları
$roomTypes = ['standard', 'deluxe', 'suite', 'family', 'economy', 'premium'];
$roomStatuses = ['available', 'occupied', 'maintenance'];
?>

<section class="py-4">
  <div class="container">
    <!-- Başlık ve Filtreler -->
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0"><i class="bi bi-door-open"></i> Oda Yönetimi</h1>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        <i class="bi bi-plus-circle"></i> Yeni Oda Ekle
      </button>
    </div>

    <!-- Filtreler -->
    <div class="card mb-4">
      <div class="card-body">
        <form method="GET" class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Oda Tipi</label>
            <select name="type" class="form-select">
              <option value="">Tümü</option>
              <option value="standard" <?php echo $typeFilter === 'standard' ? 'selected' : ''; ?>>Standart</option>
              <option value="deluxe" <?php echo $typeFilter === 'deluxe' ? 'selected' : ''; ?>>Deluxe</option>
              <option value="suite" <?php echo $typeFilter === 'suite' ? 'selected' : ''; ?>>Suite</option>
              <option value="family" <?php echo $typeFilter === 'family' ? 'selected' : ''; ?>>Aile</option>
              <option value="economy" <?php echo $typeFilter === 'economy' ? 'selected' : ''; ?>>Ekonomik</option>
              <option value="premium" <?php echo $typeFilter === 'premium' ? 'selected' : ''; ?>>Premium</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Durum</label>
            <select name="status" class="form-select">
              <option value="">Tümü</option>
              <option value="available" <?php echo $statusFilter === 'available' ? 'selected' : ''; ?>>Müsait</option>
              <option value="occupied" <?php echo $statusFilter === 'occupied' ? 'selected' : ''; ?>>Dolu</option>
              <option value="maintenance" <?php echo $statusFilter === 'maintenance' ? 'selected' : ''; ?>>Bakım</option>
            </select>
          </div>
          
          <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary me-2">Filtrele</button>
            <a href="?" class="btn btn-outline-secondary">Temizle</a>
          </div>
        </form>
      </div>
    </div>

    <!-- Odalar Listesi -->
    <div class="card">
      <div class="card-body">
        <?php if (empty($rooms)): ?>
          <p class="text-muted mb-0">Oda bulunamadı.</p>
        <?php else: ?>
          <div class="row">
            <?php foreach ($rooms as $room): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
              <div class="card h-100 shadow-sm">
                <!-- Oda Fotoğrafı -->
                <div class="position-relative">
                  <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                       style="height: 200px;">
                    <i class="bi bi-image text-muted display-4"></i>
                  </div>
                  
                  <!-- Durum Badge'leri -->
                  <div class="position-absolute top-0 start-0 m-2">
                    <?php
                      $statusBadge = 'secondary';
                      $statusText = $room['status'];
                      if ($room['status'] === 'available') {$statusBadge = 'success'; $statusText = 'Müsait';}
                      elseif ($room['status'] === 'occupied') {$statusBadge = 'danger'; $statusText = 'Dolu';}
                      elseif ($room['status'] === 'maintenance') {$statusBadge = 'warning'; $statusText = 'Bakım';}
                    ?>
                    <span class="badge bg-<?php echo $statusBadge; ?>"><?php echo $statusText; ?></span>
                  </div>
                  
                  <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success">Temiz</span>
                  </div>
                </div>
                
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($room['name']); ?></h5>
                  
                  <div class="row mb-2">
                    <div class="col-6">
                      <small class="text-muted">Kapasite:</small><br>
                      <strong><?php echo htmlspecialchars($room['capacity']); ?> Kişi</strong>
                    </div>
                    <div class="col-6">
                      <small class="text-muted">Oda Tipi:</small><br>
                      <strong><?php echo ucfirst(htmlspecialchars($room['type'])); ?></strong>
                    </div>
                  </div>
                  
                  <div class="mb-3">
                    <small class="text-muted">Fiyat:</small><br>
                    <strong class="text-success">₺<?php echo number_format($room['price'], 2); ?></strong>
                  </div>
                  
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                      <i class="bi bi-image"></i> 0 fotoğraf
                    </small>
                    <div>
                      <button class="btn btn-sm btn-outline-primary" 
                              data-bs-toggle="modal" 
                              data-bs-target="#editRoomModal"
                              data-room="<?php echo htmlspecialchars(json_encode($room)); ?>">
                        <i class="bi bi-pencil"></i> Düzenle
                      </button>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- Yeni Oda Ekleme Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Yeni Oda Ekle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_room_add.php">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Oda Adı</label>
              <input type="text" class="form-control" name="name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Oda Tipi</label>
              <select class="form-select" name="type" required>
                <option value="">Seçiniz</option>
                <option value="standard">Standart</option>
                <option value="deluxe">Deluxe</option>
                <option value="suite">Suite</option>
                <option value="family">Aile</option>
                <option value="economy">Ekonomik</option>
                <option value="premium">Premium</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kapasite</label>
              <input type="number" class="form-control" name="capacity" min="1" value="2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fiyat</label>
              <input type="number" class="form-control" name="price" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Durum</label>
              <select class="form-select" name="status">
                <option value="available">Müsait</option>
                <option value="occupied">Dolu</option>
                <option value="maintenance">Bakım</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Açıklama</label>
              <textarea class="form-control" name="description" rows="3" placeholder="Oda hakkında detaylı bilgi..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Özellikler</label>
              <textarea class="form-control" name="amenities" rows="2" placeholder="Wi-Fi, Klima, Mini Bar, vb."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
          <button type="submit" class="btn btn-primary">Oda Ekle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Oda Düzenleme Modal -->
<div class="modal fade" id="editRoomModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Oda Düzenle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="../../process/process_room_update.php">
        <div class="modal-body">
          <input type="hidden" id="edit_room_id" name="room_id">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Oda Adı</label>
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Oda Tipi</label>
              <select class="form-select" id="edit_type" name="type" required>
                <option value="standard">Standart</option>
                <option value="deluxe">Deluxe</option>
                <option value="suite">Suite</option>
                <option value="family">Aile</option>
                <option value="economy">Ekonomik</option>
                <option value="premium">Premium</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Kapasite</label>
              <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fiyat</label>
              <input type="number" class="form-control" id="edit_price" name="price" step="0.01" min="0" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Durum</label>
              <select class="form-select" id="edit_status" name="status">
                <option value="available">Müsait</option>
                <option value="occupied">Dolu</option>
                <option value="maintenance">Bakım</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Açıklama</label>
              <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Özellikler</label>
              <textarea class="form-control" id="edit_amenities" name="amenities" rows="2"></textarea>
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



<script>
document.addEventListener('DOMContentLoaded', function() {
    // Oda düzenleme modal'ı için
    const editModal = document.getElementById('editRoomModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const roomData = JSON.parse(button.getAttribute('data-room'));
            
            document.getElementById('edit_room_id').value = roomData.id;
            document.getElementById('edit_name').value = roomData.name;
            document.getElementById('edit_type').value = roomData.type;
            document.getElementById('edit_capacity').value = roomData.capacity;
            document.getElementById('edit_price').value = roomData.price;
            document.getElementById('edit_status').value = roomData.status;
            document.getElementById('edit_description').value = roomData.description || '';
            document.getElementById('edit_amenities').value = roomData.amenities || '';
                });
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
