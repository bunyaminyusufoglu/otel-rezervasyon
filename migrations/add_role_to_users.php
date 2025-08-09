<?php
require_once __DIR__ . '/../includes/db.php';

try {
    // Sütun var mı kontrol et
    $checkStmt = $pdo->prepare("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'role'");
    $checkStmt->execute();
    $exists = (int)$checkStmt->fetchColumn() > 0;

    if (!$exists) {
        // role sütununu ekle
        $pdo->exec("ALTER TABLE users ADD COLUMN role ENUM('admin','customer') NOT NULL DEFAULT 'customer' AFTER password");
        echo "role sütunu eklendi.<br>";
    } else {
        echo "role sütunu zaten mevcut.<br>";
    }

    // Admin kullanıcısını admin rolüne ata (varsayılan e-posta)
    $updateAdmin = $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = 'admin@otel.com'");
    $updateAdmin->execute();
    echo "admin@otel.com kullanıcısı admin olarak ayarlandı.<br>";

    // Null/boş role'leri customer yap
    $pdo->exec("UPDATE users SET role = 'customer' WHERE (role IS NULL OR role = '')");
    echo "Diğer kullanıcıların rolü 'customer' olarak ayarlandı.<br>";

    echo "Migration tamamlandı.";
} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}