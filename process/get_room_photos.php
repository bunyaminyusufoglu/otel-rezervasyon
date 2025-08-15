<?php
session_start();
require_once '../includes/db.php';

// Sadece admin erişebilir
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? 'customer') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $room_id = (int)($_GET['room_id'] ?? 0);
    
    if ($room_id <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Geçersiz oda ID']);
        exit();
    }
    
    try {
        // Oda fotoğraflarını getir
        $stmt = $pdo->prepare("
            SELECT id, photo_path, photo_name, is_primary, display_order 
            FROM room_photos 
            WHERE room_id = ? 
            ORDER BY is_primary DESC, display_order ASC
        ");
        $stmt->execute([$room_id]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'photos' => $photos
        ]);
        
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Geçersiz istek metodu']);
}
?>
