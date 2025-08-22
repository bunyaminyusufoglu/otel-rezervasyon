# 🏨 Otel Rezervasyon Sistemi

Modern PHP ile geliştirilmiş, kullanıcı dostu otel rezervasyon sistemi.

## ✨ Özellikler

- 🎨 **Responsive Tasarım**: Bootstrap 5 ile modern ve mobil uyumlu arayüz
- 👥 **Kullanıcı Yönetimi**: Kayıt olma, giriş yapma ve profil yönetimi
- 📅 **Rezervasyon Sistemi**: Kolay rezervasyon yapma ve yönetimi
- 🏠 **Oda Yönetimi**: Farklı oda tipleri ve fiyatlandırma
- 🔒 **Güvenlik**: SQL injection koruması ve form validation
- 🗄️ **Veritabanı**: MySQL ile güvenli veri saklama
- 📱 **Admin Paneli**: Kapsamlı yönetim arayüzü

## 🚀 Gereksinimler

- **PHP**: 7.4 veya üzeri
- **MySQL**: 5.7 veya üzeri
- **Web Sunucusu**: Apache/Nginx
- **Tarayıcı**: Modern web tarayıcısı

## 🛠️ Kurulum

### 1. Projeyi İndirin
```bash
git clone https://github.com/kullanici/otel-rezervasyon.git
cd otel-rezervasyon
```

### 2. Veritabanını Oluşturun
```sql
CREATE DATABASE otel_rezervasyon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE otel_rezervasyon;
source database.sql;
```

### 3. Veritabanı Bağlantısını Yapılandırın
`includes/config.php` dosyasındaki veritabanı bilgilerini güncelleyin:
```php
$host = 'localhost';
$db   = 'otel_rezervasyon';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
```

### 4. Web Sunucusunda Çalıştırın
Projeyi web sunucunuzun document root klasörüne kopyalayın ve tarayıcıdan erişin.

## 📁 Proje Yapısı

```
otel-rezervasyon/
├── 📁 assets/
│   ├── 📁 css/
│   │   └── style.css
│   └── 📁 images/
│       ├── room-standard.jpg
│       ├── room-deluxe.jpg
│       ├── room-suite.jpg
│       ├── room-family.jpg
│       ├── room-economy.jpg
│       └── room-premium.jpg
├── 📁 includes/
│   ├── config.php
│   ├── db.php
│   ├── header.php
│   ├── footer.php
│   └── ReservationHelper.php
├── 📁 pages/
│   ├── 📁 admin/
│   │   ├── dashboard.php
│   │   ├── reservations.php
│   │   ├── rooms.php
│   │   └── users.php
│   ├── 📁 auth/
│   │   ├── login.php
│   │   ├── profile.php
│   │   └── register.php
│   ├── 📁 reservation/
│   │   ├── reservation_success.php
│   │   └── rezervasyon.php
│   └── 📁 rooms/
│       └── odalar.php
├── 📁 process/
│   ├── check_availability.php
│   ├── get_room_photos.php
│   ├── process_login.php
│   ├── process_logout.php
│   ├── process_password_change.php
│   ├── process_profile_update.php
│   ├── process_register.php
│   ├── process_reservation_status.php
│   ├── process_reservation_update.php
│   ├── process_reservation.php
│   ├── process_room_add.php
│   ├── process_room_update.php
│   ├── process_user_role.php
│   └── process_user_update.php
├── index.php
├── README.md
└── database.sql
```

## 🔧 Yapılandırma

### Veritabanı Ayarları
`includes/config.php` dosyasında veritabanı bağlantı bilgilerini düzenleyin.

### Oda Fotoğrafları
Oda fotoğraflarını `assets/images/` klasörüne ekleyin:
- `room-standard.jpg` - Standart oda
- `room-deluxe.jpg` - Deluxe oda
- `room-suite.jpg` - Suite oda
- `room-family.jpg` - Aile odası
- `room-economy.jpg` - Ekonomik oda
- `room-premium.jpg` - Premium oda

## 👤 Varsayılan Admin Hesabı

- **E-posta**: `admin@otel.com`
- **Şifre**: `admin123`

## 🎨 Özelleştirme

### CSS Stilleri
CSS stillerini `assets/css/style.css` dosyasından düzenleyebilirsiniz.

### Oda Tipleri
Yeni oda tipleri eklemek için:
1. `pages/admin/rooms.php` dosyasında oda tipi listesine ekleyin
2. Uygun fotoğrafı `assets/images/` klasörüne ekleyin
3. Veritabanında oda tipini kullanın

## 🔒 Güvenlik Özellikleri

- SQL Injection koruması
- XSS koruması
- Session güvenliği
- Form validation
- Kullanıcı yetkilendirme

## 📱 Responsive Tasarım

- Mobil uyumlu arayüz
- Bootstrap 5 framework
- Modern UI/UX tasarım
- Cross-browser uyumluluğu

## 🚀 Geliştirme

### Yeni Özellik Ekleme
1. Gerekli PHP dosyalarını oluşturun
2. Veritabanı şemasını güncelleyin
3. Frontend arayüzünü ekleyin
4. Test edin ve dokümante edin

### Hata Ayıklama
- PHP error reporting'i aktif edin
- Veritabanı loglarını kontrol edin
- Browser console'u inceleyin

## 📝 Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.

## 🤝 Katkıda Bulunma

1. Bu repository'yi fork edin
2. Feature branch oluşturun (`git checkout -b feature/AmazingFeature`)
3. Değişikliklerinizi commit edin (`git commit -m 'Add some AmazingFeature'`)
4. Branch'inizi push edin (`git push origin feature/AmazingFeature`)
5. Pull Request oluşturun

## 📞 İletişim

- **Proje Linki**: [https://github.com/kullanici/otel-rezervasyon](https://github.com/kullanici/otel-rezervasyon)
- **Sorun Bildirimi**: [Issues](https://github.com/kullanici/otel-rezervasyon/issues)

## 🙏 Teşekkürler

Bu projeyi geliştirmemde yardımcı olan herkese teşekkürler!

---

⭐ Bu projeyi beğendiyseniz yıldız vermeyi unutmayın!
