<?php
require_once '../includes/session.php';
require_once '../includes/db.php';
require_once '../includes/ReservationHelper.php';
require_once '../includes/CSRFHelper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // AJAX istekleri için CSRF token doğrulama
    $token = $_POST['csrf_token'] ?? '';
    if (!CSRFHelper::validateToken($token)) {
        echo json_encode(['success' => false, 'message' => 'CSRF token validation failed']);
        exit();
    }
    $room_type = $_POST['room_type'] ?? '';
    $checkin_date = $_POST['checkin_date'] ?? '';
    $checkout_date = $_POST['checkout_date'] ?? '';
    
    if (empty($room_type) || empty($checkin_date) || empty($checkout_date)) {
        echo json_encode(['success' => false, 'message' => 'Tüm alanlar gerekli']);
        exit();
    }
    
    try {
        $reservationHelper = new ReservationHelper($pdo);
        
        // Oda müsaitlik kontrolü
        $availability = $reservationHelper->checkRoomAvailability($room_type, $checkin_date, $checkout_date);
        
        // Fiyat hesaplama
        $price_calculation = $reservationHelper->calculatePrice($room_type, $checkin_date, $checkout_date);
        
        if ($availability['available'] && $price_calculation['success']) {
            echo json_encode([
                'success' => true,
                'available' => true,
                'message' => 'Oda müsait',
                'price_info' => $price_calculation
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'available' => false,
                'message' => $availability['message'],
                'price_info' => $price_calculation
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Kontrol sırasında hata: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
}
?> 