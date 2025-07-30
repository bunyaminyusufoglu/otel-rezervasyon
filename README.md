# Otel Rezervasyon Sistemi

PHP ile geliştirilen modern otel rezervasyon sistemi.

## 🚀 Özellikler

- **Responsive Tasarım**: Bootstrap 5 ile modern ve mobil uyumlu arayüz
- **Kullanıcı Yönetimi**: Kayıt olma, giriş yapma ve profil yönetimi
- **Rezervasyon Sistemi**: Kolay rezervasyon yapma ve yönetimi
- **Oda Yönetimi**: Farklı oda tipleri ve fiyatlandırma
- **Güvenlik**: SQL injection koruması ve form validation
- **Veritabanı**: MySQL ile güvenli veri saklama

## 📋 Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Web sunucusu (Apache/Nginx)

## 🛠️ Kurulum

1. **Veritabanını oluşturun:**
   ```sql
   source database.sql
   ```

2. **Veritabanı bağlantısını yapılandırın:**
   `includes/config.php` dosyasındaki veritabanı bilgilerini güncelleyin.

3. **Web sunucusunda çalıştırın:**
   Projeyi web sunucunuzun document root klasörüne kopyalayın.

## 📁 Dosya Yapısı

```
otel-rezervasyon/
├── assets/
│   └── css/
│       └── style.css
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── header.php
│   └── footer.php
├── index.php
├── odalar.php
├── rezervasyon.php
├── login.php
├── register.php
├── process_*.php
└── database.sql
```

## 🔧 Yapılandırma

### Veritabanı Ayarları
`includes/config.php` dosyasında:
```php
$host = 'localhost';
$db   = 'otel_rezervasyon';
$user = 'root';
$pass = '';
```

## 👤 Varsayılan Admin Hesabı

- **E-posta**: admin@otel.com
- **Şifre**: admin123

## 🎨 Özelleştirme

CSS stillerini `assets/css/style.css` dosyasından düzenleyebilirsiniz.

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır.
