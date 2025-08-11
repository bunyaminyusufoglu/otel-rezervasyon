<?php
class ReservationHelper {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Belirli tarih aralığında oda müsaitliğini kontrol eder
     */
    public function checkRoomAvailability($room_type, $checkin_date, $checkout_date, $exclude_reservation_id = null) {
        try {
            // Oda ID'sini al
            $stmt = $this->pdo->prepare("SELECT id FROM rooms WHERE type = ?");
            $stmt->execute([$room_type]);
            $room = $stmt->fetch();
            
            if (!$room) {
                return ['available' => false, 'message' => 'Oda tipi bulunamadı'];
            }
            
            $room_id = $room['id'];
            
            // Tarih çakışması kontrolü
            $sql = "SELECT COUNT(*) as count FROM reservations 
                    WHERE room_id = ? 
                    AND status NOT IN ('cancelled', 'completed')
                    AND (
                        (checkin_date <= ? AND checkout_date > ?) OR
                        (checkin_date < ? AND checkout_date >= ?) OR
                        (checkin_date >= ? AND checkout_date <= ?)
                    )";
            
            $params = [$room_id, $checkout_date, $checkin_date, $checkout_date, $checkin_date, $checkin_date, $checkout_date];
            
            // Belirli rezervasyonu hariç tut (güncelleme durumunda)
            if ($exclude_reservation_id) {
                $sql .= " AND id != ?";
                $params[] = $exclude_reservation_id;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return ['available' => false, 'message' => 'Seçilen tarihlerde oda müsait değil'];
            }
            
            return ['available' => true, 'message' => 'Oda müsait'];
            
        } catch (PDOException $e) {
            return ['available' => false, 'message' => 'Müsaitlik kontrolü sırasında hata: ' . $e->getMessage()];
        }
    }
    
    /**
     * Dinamik fiyat hesaplama (gece sayısına göre)
     */
    public function calculatePrice($room_type, $checkin_date, $checkout_date) {
        try {
            // Oda fiyatını al
            $stmt = $this->pdo->prepare("SELECT price FROM rooms WHERE type = ?");
            $stmt->execute([$room_type]);
            $room = $stmt->fetch();
            
            if (!$room) {
                return ['success' => false, 'message' => 'Oda tipi bulunamadı'];
            }
            
            $base_price = $room['price'];
            
            // Geceleri hesapla
            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime($checkout_date);
            $nights = $checkin->diff($checkout)->days;
            
            if ($nights < 1) {
                return ['success' => false, 'message' => 'Geçersiz tarih aralığı'];
            }
            
            // Hafta sonu ve tatil günleri için ek ücret (opsiyonel)
            $total_price = $base_price * $nights;
            
            // Uzun konaklama indirimi (7+ gece için %10 indirim)
            if ($nights >= 7) {
                $total_price = $total_price * 0.9;
            }
            
            return [
                'success' => true,
                'base_price' => $base_price,
                'nights' => $nights,
                'total_price' => $total_price,
                'discount' => $nights >= 7 ? 10 : 0
            ];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Fiyat hesaplama hatası: ' . $e->getMessage()];
        }
    }
    
    /**
     * Rezervasyon onay e-postası gönder
     */
    public function sendConfirmationEmail($user_email, $user_name, $reservation_data) {
        try {
            $subject = "Rezervasyon Onayı - #" . $reservation_data['id'];
            
            $message = "
            <html>
            <head>
                <title>Rezervasyon Onayı</title>
            </head>
            <body>
                <h2>Rezervasyon Onayı</h2>
                <p>Sayın {$user_name},</p>
                <p>Rezervasyonunuz başarıyla oluşturulmuştur.</p>
                
                <h3>Rezervasyon Detayları:</h3>
                <ul>
                    <li><strong>Rezervasyon No:</strong> #{$reservation_data['id']}</li>
                    <li><strong>Oda Tipi:</strong> {$reservation_data['room_type']}</li>
                    <li><strong>Giriş Tarihi:</strong> " . date('d.m.Y', strtotime($reservation_data['checkin_date'])) . "</li>
                    <li><strong>Çıkış Tarihi:</strong> " . date('d.m.Y', strtotime($reservation_data['checkout_date'])) . "</li>
                    <li><strong>Misafir Sayısı:</strong> {$reservation_data['guests']}</li>
                    <li><strong>Toplam Ücret:</strong> ₺" . number_format($reservation_data['total_price'], 2) . "</li>
                </ul>
                
                <p>Rezervasyonunuz yönetici onayını beklemektedir. Onaylandığında size bilgi verilecektir.</p>
                
                <p>İyi tatiller dileriz!</p>
                <p><strong>Otel Rezervasyon Sistemi</strong></p>
            </body>
            </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@otel.com" . "\r\n";
            
            if (mail($user_email, $subject, $message, $headers)) {
                return ['success' => true, 'message' => 'Onay e-postası gönderildi'];
            } else {
                return ['success' => false, 'message' => 'E-posta gönderilemedi'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'E-posta gönderme hatası: ' . $e->getMessage()];
        }
    }
    
    /**
     * Kullanıcının mevcut rezervasyonlarını kontrol et
     */
    public function checkUserReservations($user_id, $checkin_date, $checkout_date, $exclude_reservation_id = null) {
        try {
            $sql = "SELECT COUNT(*) as count FROM reservations 
                    WHERE user_id = ? 
                    AND status NOT IN ('cancelled', 'completed')
                    AND (
                        (checkin_date <= ? AND checkout_date > ?) OR
                        (checkin_date < ? AND checkout_date >= ?) OR
                        (checkin_date >= ? AND checkout_date <= ?)
                    )";
            
            $params = [$user_id, $checkout_date, $checkin_date, $checkout_date, $checkin_date, $checkin_date, $checkout_date];
            
            if ($exclude_reservation_id) {
                $sql .= " AND id != ?";
                $params[] = $exclude_reservation_id;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['count'];
            
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?> 