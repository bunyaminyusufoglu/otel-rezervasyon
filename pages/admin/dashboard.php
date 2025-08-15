<?php
include '../../includes/header.php';
require_once '../../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    $_SESSION['error'] = 'Bu sayfaya erişim yetkiniz yok.';
    header('Location: ../../index.php');
    exit();
}

// İstatistikleri getir
try {
    // Toplam rezervasyon sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations");
    $totalReservations = $stmt->fetch()['total'];
    
    // Bugünkü rezervasyon sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as today FROM reservations WHERE DATE(created_at) = CURDATE()");
    $todayReservations = $stmt->fetch()['today'];
    
    // Bekleyen rezervasyon sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM reservations WHERE status = 'pending'");
    $pendingReservations = $stmt->fetch()['pending'];
    
    // Toplam kullanıcı sayısı
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // Bu ay gelir
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) as monthly_income FROM reservations WHERE status IN ('confirmed', 'completed') AND MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
    $monthlyIncome = $stmt->fetch()['monthly_income'];
    
    // Toplam gelir
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_price), 0) as total_income FROM reservations WHERE status IN ('confirmed', 'completed')");
    $totalIncome = $stmt->fetch()['total_income'];
    
    // Son 7 gün rezervasyon trendi
    $stmt = $pdo->query("
        SELECT DATE(created_at) as date, COUNT(*) as count 
        FROM reservations 
        WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at)
        ORDER BY date
    ");
    $weeklyTrend = $stmt->fetchAll();
    
    // Son rezervasyonlar
    $stmt = $pdo->query("
        SELECT r.*, u.first_name, u.last_name, rm.name as room_name
        FROM reservations r
        LEFT JOIN users u ON r.user_id = u.id
        LEFT JOIN rooms rm ON r.room_id = rm.id
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $recentReservations = $stmt->fetchAll();
    
    // Son kullanıcılar
    $stmt = $pdo->query("
        SELECT * FROM users 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $recentUsers = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Veritabanı hatası: ' . $e->getMessage();
}
?>

<section class="py-4">
  <div class="container">

    <!-- İstatistik Kartları -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-primary shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Toplam Rezervasyon</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalReservations); ?></div>
              </div>
              <div class="col-auto">
                <i class="bi bi-calendar-check fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-warning shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Bekleyen Rezervasyon</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($pendingReservations); ?></div>
              </div>
              <div class="col-auto">
                <i class="bi bi-clock fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-success shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Bu Ay Gelir</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">₺<?php echo number_format($monthlyIncome, 2); ?></div>
              </div>
              <div class="col-auto">
                <i class="bi bi-currency-dollar fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-left-info shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Toplam Kullanıcı</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($totalUsers); ?></div>
              </div>
              <div class="col-auto">
                <i class="bi bi-people fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Hızlı İşlemler -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-lightning"></i> Hızlı İşlemler</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-3 mb-3">
                <a href="reservations.php" class="btn btn-outline-primary btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <i class="bi bi-calendar-check display-6 mb-2"></i>
                  <span>Rezervasyon Yönetimi</span>
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="users.php" class="btn btn-outline-success btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <i class="bi bi-people display-6 mb-2"></i>
                  <span>Kullanıcı Yönetimi</span>
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="rooms.php" class="btn btn-outline-info btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <i class="bi bi-door-open display-6 mb-2"></i>
                  <span>Oda Yönetimi</span>
                </a>
              </div>
              <div class="col-md-3 mb-3">
                <a href="#" class="btn btn-outline-warning btn-lg w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                  <i class="bi bi-graph-up display-6 mb-2"></i>
                  <span>Raporlar</span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Son Rezervasyonlar ve Kullanıcılar -->
    <div class="row">
      <div class="col-lg-8 mb-4">
        <div class="card shadow">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Son Rezervasyonlar</h5>
          </div>
          <div class="card-body">
            <?php if (empty($recentReservations)): ?>
              <p class="text-muted mb-0">Henüz rezervasyon bulunmuyor.</p>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Kullanıcı</th>
                      <th>Oda</th>
                      <th>Tarih</th>
                      <th>Durum</th>
                      <th>Tutar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recentReservations as $res): ?>
                    <tr>
                      <td>
                        <strong><?php echo htmlspecialchars(($res['first_name'] ?? 'Misafir') . ' ' . ($res['last_name'] ?? '')); ?></strong>
                      </td>
                      <td><?php echo htmlspecialchars($res['room_name'] ?? '-'); ?></td>
                      <td>
                        <small><?php echo date('d.m.Y', strtotime($res['checkin_date'])); ?> - <?php echo date('d.m.Y', strtotime($res['checkout_date'])); ?></small>
                      </td>
                      <td>
                        <?php
                          $badge = 'secondary'; $text = $res['status'];
                          if ($res['status'] === 'pending') {$badge = 'warning'; $text = 'Beklemede';}
                          elseif ($res['status'] === 'confirmed') {$badge = 'success'; $text = 'Onaylandı';}
                          elseif ($res['status'] === 'cancelled') {$badge = 'danger'; $text = 'İptal';}
                          elseif ($res['status'] === 'completed') {$badge = 'info'; $text = 'Tamamlandı';}
                        ?>
                        <span class="badge bg-<?php echo $badge; ?>"><?php echo $text; ?></span>
                      </td>
                      <td><strong>₺<?php echo number_format($res['total_price'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <div class="text-center mt-3">
                <a href="reservations.php" class="btn btn-outline-info">Tümünü Görüntüle</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-lg-4 mb-4">
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-person-plus"></i> Son Kayıt Olan Kullanıcılar</h5>
          </div>
          <div class="card-body">
            <?php if (empty($recentUsers)): ?>
              <p class="text-muted mb-0">Henüz kullanıcı bulunmuyor.</p>
            <?php else: ?>
              <?php foreach ($recentUsers as $user): ?>
              <div class="d-flex align-items-center mb-3">
                <div class="flex-shrink-0">
                  <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-person text-white"></i>
                  </div>
                </div>
                <div class="flex-grow-1 ms-3">
                  <h6 class="mb-0"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h6>
                  <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                  <br>
                  <small class="text-muted"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></small>
                </div>
                <div class="flex-shrink-0">
                  <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                    <?php echo $user['role'] === 'admin' ? 'Admin' : 'Müşteri'; ?>
                  </span>
                </div>
              </div>
              <?php endforeach; ?>
              <div class="text-center mt-3">
                <a href="users.php" class="btn btn-outline-success">Tümünü Görüntüle</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Haftalık Trend Grafiği -->
    <div class="row">
      <div class="col-12">
        <div class="card shadow">
          <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Son 7 Gün Rezervasyon Trendi</h5>
          </div>
          <div class="card-body">
            <canvas id="weeklyTrendChart" width="400" height="100"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Chart.js kütüphanesi -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Haftalık trend grafiği
    const ctx = document.getElementById('weeklyTrendChart').getContext('2d');
    
    // Veri hazırlama
    const dates = <?php echo json_encode(array_column($weeklyTrend, 'date')); ?>;
    const counts = <?php echo json_encode(array_column($weeklyTrend, 'count')); ?>;
    
    // Tarih formatını düzenle
    const formattedDates = dates.map(date => {
        const d = new Date(date);
        return d.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit' });
    });
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: formattedDates,
            datasets: [{
                label: 'Rezervasyon Sayısı',
                data: counts,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Günlük Rezervasyon Sayıları'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<?php include '../../includes/footer.php'; ?>
