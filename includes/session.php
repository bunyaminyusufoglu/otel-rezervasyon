<?php
/**
 * Session Güvenlik Yapılandırması
 * Bu dosya session başlatılmadan ÖNCE include edilmelidir
 */

// Session güvenlik ayarları (session başlamadan önce yapılmalı)
if (session_status() === PHP_SESSION_NONE) {
    // Güvenlik ayarları
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.use_strict_mode', 1);
    
    // SameSite cookie (PHP 7.3+)
    if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
        ini_set('session.cookie_samesite', 'Strict');
    }
    
    // Session güvenlik parametreleri
    ini_set('session.cookie_lifetime', 0); // Session cookie browser kapanınca silinsin
    ini_set('session.gc_maxlifetime', 3600); // 1 saat
    
    // Session ID güvenliği
    ini_set('session.entropy_length', 32);
    ini_set('session.hash_function', 'sha256');
    
    // Session başlat
    session_start();
    
    // Session ID'yi yenile (güvenlik için)
    if (!isset($_SESSION['session_regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['session_regenerated'] = true;
        $_SESSION['session_started'] = time();
    }
    
    // Session timeout kontrolü (1 saat)
    if (isset($_SESSION['session_started']) && (time() - $_SESSION['session_started']) > 3600) {
        session_destroy();
        session_start();
        session_regenerate_id(true);
        $_SESSION['session_started'] = time();
    }
}
?>
