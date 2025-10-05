<?php
/**
 * CSRF Token Helper Sınıfı
 * Cross-Site Request Forgery (CSRF) koruması için
 */
class CSRFHelper {
    
    /**
     * CSRF token oluşturur veya mevcut token'ı döndürür
     * @return string
     */
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            // Her seferinde yeni token oluştur - güvenlik için
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Yeni CSRF token oluşturur (mevcut token'ı değiştirir)
     * @return string
     */
    public static function createNewToken() {
        // Mevcut token'ı koru, yeni oluşturma
        return self::generateToken();
    }
    
    /**
     * CSRF token'ı doğrular
     * @param string $token
     * @return bool
     */
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Timing attack koruması için hash_equals kullan
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * CSRF token'ı form için gizli input olarak döndürür
     * @return string
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    /**
     * CSRF token'ı AJAX istekleri için döndürür
     * @return array
     */
    public static function getTokenForAjax() {
        return [
            'csrf_token' => self::generateToken(),
            'csrf_name' => 'csrf_token'
        ];
    }
    
    /**
     * POST verilerinden CSRF token'ı doğrular
     * @return bool
     */
    public static function validatePostToken() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true; // GET istekleri için CSRF kontrolü gerekmez
        }
        
        $token = $_POST['csrf_token'] ?? '';
        return self::validateToken($token);
    }
    
    /**
     * CSRF doğrulama başarısız olduğunda hata döndürür
     * @return void
     */
    public static function handleValidationFailure() {
        $_SESSION['error'] = 'Güvenlik hatası: Geçersiz istek. Lütfen sayfayı yenileyip tekrar deneyin.';
        
        // Referer kontrolü
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (!empty($referer) && parse_url($referer, PHP_URL_HOST) === $_SERVER['HTTP_HOST']) {
            header('Location: ' . $referer);
        } else {
            header('Location: ' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']));
        }
        exit();
    }
    
    /**
     * CSRF token'ı yeniler (logout, login gibi kritik işlemler için)
     * @return void
     */
    public static function regenerateToken() {
        unset($_SESSION['csrf_token']);
        self::generateToken();
    }
    
    /**
     * Debug modunda token bilgilerini döndürür
     * @return array
     */
    public static function getDebugInfo() {
        return [
            'has_token' => isset($_SESSION['csrf_token']),
            'token_length' => isset($_SESSION['csrf_token']) ? strlen($_SESSION['csrf_token']) : 0,
            'session_id' => session_id(),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'session_status' => session_status(),
            'session_name' => session_name()
        ];
    }
    
    /**
     * Session güvenlik ayarlarını yapılandırır
     * @deprecated Bu fonksiyon artık gerekli değil - session.php kullanın
     * @return void
     */
    public static function configureSecureSession() {
        // Bu fonksiyon artık kullanılmıyor - session.php dosyası kullanılıyor
        // Session güvenlik ayarları session.php'de yapılıyor
        return;
    }
}
?>
