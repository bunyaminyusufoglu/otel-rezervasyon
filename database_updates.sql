-- Oda Yönetimi Sistemi Veritabanı Güncellemeleri
-- Bu dosyayı mevcut veritabanınıza çalıştırın

USE otel_rezervasyon;

-- 1. Odalar tablosunu güncelle
ALTER TABLE rooms 
ADD COLUMN floor_number INT DEFAULT 1 AFTER capacity,
ADD COLUMN room_number VARCHAR(10) AFTER floor_number,
ADD COLUMN size_m2 INT AFTER room_number,
ADD COLUMN view_type ENUM('city', 'sea', 'mountain', 'garden', 'pool') DEFAULT 'city' AFTER size_m2,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER view_type,
ADD COLUMN base_price DECIMAL(10,2) AFTER price,
ADD COLUMN cleaning_status ENUM('clean', 'dirty', 'cleaning') DEFAULT 'clean' AFTER status,
ADD COLUMN notes TEXT AFTER cleaning_status;

-- 2. Oda fotoğrafları tablosu
CREATE TABLE room_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    photo_name VARCHAR(100),
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- 3. Sezonsal fiyatlandırma tablosu
CREATE TABLE seasonal_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    season_name VARCHAR(50) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price_multiplier DECIMAL(3,2) DEFAULT 1.00,
    additional_fee DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
);

-- 4. Oda durumu geçmişi tablosu
CREATE TABLE room_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    old_status ENUM('available', 'occupied', 'maintenance', 'cleaning', 'reserved') NOT NULL,
    new_status ENUM('available', 'occupied', 'maintenance', 'cleaning', 'reserved') NOT NULL,
    changed_by INT,
    change_reason TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- 5. Mevcut odalara yeni alanları ekle
UPDATE rooms SET 
    floor_number = 1,
    room_number = CONCAT('R', id),
    size_m2 = CASE 
        WHEN type = 'economy' THEN 20
        WHEN type = 'standard' THEN 25
        WHEN type = 'deluxe' THEN 35
        WHEN type = 'family' THEN 40
        WHEN type = 'suite' THEN 50
        WHEN type = 'premium' THEN 70
        ELSE 25
    END,
    base_price = price,
    cleaning_status = 'clean'
WHERE floor_number IS NULL;

-- 6. Örnek sezonsal fiyatlandırma verileri
INSERT INTO seasonal_pricing (room_id, season_name, start_date, end_date, price_multiplier, additional_fee) VALUES
(1, 'Yaz Sezonu', '2024-06-01', '2024-08-31', 1.25, 50.00),
(1, 'Kış Sezonu', '2024-12-01', '2025-02-28', 0.90, 0.00),
(2, 'Yaz Sezonu', '2024-06-01', '2024-08-31', 1.30, 75.00),
(2, 'Kış Sezonu', '2024-12-01', '2025-02-28', 0.85, 0.00),
(3, 'Yaz Sezonu', '2024-06-01', '2024-08-31', 1.35, 100.00),
(3, 'Kış Sezonu', '2024-12-01', '2025-02-28', 0.80, 0.00);

-- 7. Örnek oda fotoğrafları (placeholder)
INSERT INTO room_photos (room_id, photo_path, photo_name, is_primary, display_order) VALUES
(1, 'assets/images/rooms/standard-1.jpg', 'Standart Oda Ana Görünüm', TRUE, 1),
(1, 'assets/images/rooms/standard-2.jpg', 'Standart Oda Banyo', FALSE, 2),
(2, 'assets/images/rooms/deluxe-1.jpg', 'Deluxe Oda Ana Görünüm', TRUE, 1),
(2, 'assets/images/rooms/deluxe-2.jpg', 'Deluxe Oda Balkon', FALSE, 2),
(3, 'assets/images/rooms/suite-1.jpg', 'Suite Ana Görünüm', TRUE, 1),
(3, 'assets/images/rooms/suite-2.jpg', 'Suite Oturma Alanı', FALSE, 2);

-- 8. İndeksler ekle (performans için)
CREATE INDEX idx_rooms_status ON rooms(status, cleaning_status);
CREATE INDEX idx_rooms_type ON rooms(type, is_active);
CREATE INDEX idx_rooms_price ON rooms(base_price, price);
CREATE INDEX idx_seasonal_pricing_dates ON seasonal_pricing(start_date, end_date);
CREATE INDEX idx_room_photos_room ON room_photos(room_id, is_primary);

-- 9. Güncellenmiş oda durumu enum'ı
-- Not: Bu güncelleme için önce mevcut verileri yedekleyin
-- ALTER TABLE rooms MODIFY COLUMN status ENUM('available', 'occupied', 'maintenance', 'cleaning', 'reserved') DEFAULT 'available';

-- 10. Güncellenmiş oda tipi enum'ı (opsiyonel)
-- ALTER TABLE rooms MODIFY COLUMN type ENUM('standard', 'deluxe', 'suite', 'family', 'economy', 'premium', 'villa', 'apartment') NOT NULL;

-- Güncelleme tamamlandı mesajı
SELECT 'Oda Yönetimi Sistemi veritabanı güncellemeleri tamamlandı!' as message;
