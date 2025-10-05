<?php
/**
 * CSRF Middleware Sınıfı
 * Otomatik CSRF koruması için middleware
 */
class CSRFMiddleware {
    
    /**
     * Middleware'i çalıştırır
     * @param callable $next Sonraki middleware veya handler
     * @return void
     */
    public static function handle(callable $next) {
        // Sadece POST, PUT, DELETE, PATCH istekleri için CSRF kontrolü
        if (in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            if (!CSRFHelper::validatePostToken()) {
                CSRFHelper::handleValidationFailure();
            }
        }
        
        // Sonraki middleware'i çalıştır
        $next();
    }
    
    /**
     * AJAX istekleri için CSRF token'ı header'a ekler
     * @return void
     */
    public static function addAjaxHeaders() {
        if (!headers_sent()) {
            $token = CSRFHelper::generateToken();
            header('X-CSRF-Token: ' . $token);
        }
    }
    
    /**
     * Form sayfalarında CSRF token'ı otomatik ekler
     * @param string $html Form HTML'i
     * @return string
     */
    public static function addTokenToForms($html) {
        // Form tag'lerini bul ve CSRF token ekle
        $pattern = '/(<form[^>]*method\s*=\s*["\']post["\'][^>]*>)/i';
        $replacement = '$1' . CSRFHelper::getTokenField();
        
        return preg_replace($pattern, $replacement, $html);
    }
    
    /**
     * RESTful API endpoint'leri için API key kontrolü
     * @param string $apiKey
     * @return bool
     */
    public static function validateApiKey($apiKey) {
        // API key validation logic
        $validKeys = [
            'otel-api-2024-key',
            'admin-api-secure-key'
        ];
        
        return in_array($apiKey, $validKeys);
    }
    
    /**
     * Rate limiting kontrolü
     * @param string $ip Client IP
     * @param int $limit Maximum requests per minute
     * @return bool
     */
    public static function checkRateLimit($ip, $limit = 60) {
        $cacheFile = sys_get_temp_dir() . '/rate_limit_' . md5($ip) . '.txt';
        $now = time();
        $minuteAgo = $now - 60;
        
        // Cache dosyasını oku
        $requests = [];
        if (file_exists($cacheFile)) {
            $data = file_get_contents($cacheFile);
            $requests = json_decode($data, true) ?: [];
        }
        
        // Eski istekleri temizle
        $requests = array_filter($requests, function($timestamp) use ($minuteAgo) {
            return $timestamp > $minuteAgo;
        });
        
        // Rate limit kontrolü
        if (count($requests) >= $limit) {
            return false;
        }
        
        // Yeni isteği ekle
        $requests[] = $now;
        file_put_contents($cacheFile, json_encode($requests));
        
        return true;
    }
    
    /**
     * Debug modunda CSRF bilgilerini döndürür
     * @return array
     */
    public static function getDebugInfo() {
        return [
            'csrf_info' => CSRFHelper::getDebugInfo(),
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
}
?>
