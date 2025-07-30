-- Otel Rezervasyon Sistemi Veritabanı
-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS otel_rezervasyon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE otel_rezervasyon;

-- Kullanıcılar tablosu
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    newsletter BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Odalar tablosu
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('standard', 'deluxe', 'suite', 'family', 'economy', 'premium') NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    capacity INT NOT NULL,
    amenities TEXT,
    image VARCHAR(255),
    status ENUM('available', 'occupied', 'maintenance') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rezervasyonlar tablosu
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    room_id INT,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    guests INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- Örnek oda verileri
INSERT INTO rooms (name, type, description, price, capacity, amenities) VALUES
('Standart Oda', 'standard', '25m² alanında, şehir manzaralı, konforlu konaklama imkanı.', 500.00, 2, '1 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi'),
('Deluxe Oda', 'deluxe', '35m² alanında, deniz manzaralı, lüks donanımlı oda.', 800.00, 2, '1 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi, Balkon, Mini Bar'),
('Suite', 'suite', '50m² alanında, en üst düzey konfor ve özel hizmetler.', 1200.00, 4, '2 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi, Geniş Balkon, Mini Bar, Oturma Alanı'),
('Aile Odası', 'family', '40m² alanında, geniş aileler için ideal konaklama.', 900.00, 4, '2 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi, Balkon, Çocuk Yatakları'),
('Ekonomik Oda', 'economy', '20m² alanında, bütçe dostu konaklama seçeneği.', 300.00, 2, '1 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi'),
('Premium Suite', 'premium', '70m² alanında, en lüks konaklama deneyimi.', 1500.00, 4, '2 Yatak Odası, Özel Banyo, Klima, Ücretsiz Wi-Fi, Geniş Balkon, Mini Bar, Oturma Alanı, Jakuzi');

-- Admin kullanıcısı oluştur (şifre: admin123)
INSERT INTO users (first_name, last_name, email, phone, password) VALUES
('Admin', 'User', 'admin@otel.com', '+90 555 123 4567', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 