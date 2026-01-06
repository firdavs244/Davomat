# 🎓 Kollej Davomat Tizimi (College Attendance System)

Modern va professional davomat tizimi Laravel, Docker, Dark/Light theme va real-time statistika bilan.

## ✨ Asosiy Xususiyatlar

-   🎨 **Dark/Light Theme** - To'liq mavzu o'zgartirish
-   📊 **Real-time Statistika** - Kunlik, haftalik, oylik va yillik
-   📱 **Responsive Design** - Barcha qurilmalarda ishlaydi
-   🐳 **Docker Support** - Bir klik bilan ishga tushirish
-   🔒 **HTTPS** - Xavfsiz ulanish
-   📈 **Excel Export** - Professional formatda hisobotlar
-   🎯 **Rol tizimi** - Admin va Xodim rollari

## 🚀 Tezkor Ishga Tushirish

### Talablar

-   ✅ Docker Desktop (yoki Docker Engine + Docker Compose)
-   ✅ Windows 10/11, Linux yoki macOS

### 1. Loyihani yuklab olish

```bash
git clone https://github.com/your-repo/davomat.git
cd davomat
```

### 2. Docker bilan ishga tushirish

#### Windows

```powershell
.\docker-setup.ps1
```

#### Linux/Mac

```bash
chmod +x docker-setup.sh
./docker-setup.sh
```

### 3. Brauzerda ochish

-   **HTTP**: http://localhost
-   **HTTPS**: https://localhost ⭐ (tavsiya etiladi)

> **Eslatma**: HTTPS da brauzer warning ko'rsatadi - bu normal, "Advanced" > "Proceed to localhost" bosing.

## 🔧 Qo'lda O'rnatish (Docker'siz)

### Talablar

-   PHP 8.2+
-   MySQL 8.0+
-   Composer
-   Node.js & NPM

### O'rnatish

```bash
# 1. Bog'liqliklarni o'rnatish
composer install
npm install && npm run build

# 2. .env faylini sozlash
cp .env.example .env
php artisan key:generate

# 3. Ma'lumotlar bazasini sozlash
php artisan migrate --seed

# 4. Serverni ishga tushirish
php artisan serve
```

## 📦 Docker Komandalar

```bash
# Containerlarni ko'rish
docker-compose ps

# Loglarni ko'rish
docker-compose logs -f
docker-compose logs -f nginx  # Faqat nginx

# Containerlarni to'xtatish
docker-compose down

# Qayta ishga tushirish
docker-compose restart

# PHP containerga kirish
docker-compose exec php sh

# Artisan komandalar
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed
docker-compose exec php php artisan cache:clear

# MySQL ga kirish
docker-compose exec mysql mysql -u davomat_user -p
# Parol: secret123

# Hammasini tozalash va qayta boshlash
docker-compose down -v
docker-compose build --no-cache
docker-compose up -d
```

## 🎯 Foydalanish

### 1. Login

Default admin credentials (`.env` va seeders'da):

-   Email: `admin@davomat.uz`
-   Parol: `password`

### 2. Dashboard

-   **Period tanlash**: Bugun, Hafta, Oy, Yil
-   **Real-time statistika**: Avtomatik yangilanadi
-   **Trend grafigi**: Oxirgi 7 kunlik davomat
-   **Guruhlar statistikasi**: Har bir guruh uchun foiz
-   **Top yo'qliklar**: Eng ko'p yo'q bo'lgan talabalar

### 3. Davomat Olish

1. **Davomat Olish** tugmasini bosing
2. Sana, Para va Guruhni tanlang
3. Har bir talaba uchun "Bor" yoki "Yo'q" belgilang
4. **Davomatni Saqlash** bosing

### 4. Excel Export

1. **Export** bo'limiga o'ting
2. Guruh va Davrni tanlang (kunlik, haftalik, oylik, yillik)
3. **Excel yuklab olish** tugmasini bosing
4. Professional formatda Excel fayl yuklab olinadi

## 🏗️ Loyiha Tuzilmasi

```
davomat/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php  # Statistika
│   │   ├── DavomatController.php    # Davomat boshqaruvi
│   │   ├── GuruhController.php      # Guruhlar
│   │   └── ...
│   ├── Models/
│   │   ├── Davomat.php
│   │   ├── Guruh.php
│   │   └── Talaba.php
├── resources/views/
│   ├── layouts/app.blade.php        # Asosiy layout (theme)
│   ├── dashboard.blade.php          # Dashboard sahifa
│   ├── davomat/                     # Davomat sahifalari
│   ├── guruhlar/                    # Guruhlar CRUD
│   └── ...
├── docker/
│   ├── nginx/                       # Nginx config va SSL
│   ├── php/                         # PHP Dockerfile
│   └── mysql/                       # MySQL init
├── docker-compose.yml               # Docker orchestration
└── docker-setup.ps1/sh              # Setup skriptlar
```

## 🎨 Theme Tizimi

Loyihada CSS variables ishlatilgan - dark/light theme oson o'zgaradi:

```css
:root {
    --primary: #0891b2;
    --background: #f8fafc;
    --foreground: #0f172a;
    --card: #ffffff;
    --border: #e2e8f0;
}

.dark {
    --primary: #22d3ee;
    --background: #0f172a;
    --foreground: #f1f5f9;
    --card: #1e293b;
    --border: #334155;
}
```

Theme localStorage'da saqlanadi - avtomatik restore bo'ladi.

## 🔒 HTTPS Sozlash

### Development (Self-signed)

Setup skript avtomatik generatsiya qiladi. Browser warning normal.

### Production (Let's Encrypt)

```bash
# Certbot o'rnatish va SSL olish
docker run -it --rm \
  -v ./docker/nginx/ssl:/etc/letsencrypt \
  certbot/certbot certonly --standalone \
  -d yourdomain.com

# Nginx config yangilash
# docker/nginx/default.conf da:
ssl_certificate /etc/nginx/ssl/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/nginx/ssl/live/yourdomain.com/privkey.pem;
```

## 🐛 Troubleshooting

### Port band bo'lsa

`docker-compose.yml` da portlarni o'zgartiring:

```yaml
nginx:
    ports:
        - "8080:80" # HTTP
        - "8443:443" # HTTPS
```

### Database connection refused

MySQL to'liq ishga tushishini kuting (15 sekund):

```bash
docker-compose logs mysql
```

### Permission errors

```bash
docker-compose exec php chown -R www-data:www-data storage bootstrap/cache
docker-compose exec php chmod -R 775 storage bootstrap/cache
```

### Composer errors

Agar `composer.json` topilmasa:

```bash
# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## 📊 API Endpoints (AJAX)

Dashboard dinamik statistika uchun:

```
GET /dashboard/stats/{period}
```

Parametrlar:

-   `daily` - Bugungi statistika
-   `weekly` - Haftalik statistika
-   `monthly` - Oylik statistika
-   `yearly` - Yillik statistika

Response:

```json
{
  "period": "Bu hafta",
  "davomat_olingan": 150,
  "bor": 120,
  "yoq": 30,
  "foiz": 80.0,
  "guruhlar": [...],
  "top_yoqlar": [...]
}
```

## 🤝 Contributing

Pull requestlar qabul qilinadi! Katta o'zgarishlar uchun oldin issue oching.

## 📝 License

MIT License - batafsil [LICENSE](LICENSE) faylida.

## 👨‍💻 Muallif

-   **Firdavs** - Initial work

## 🙏 Minnatdorchilik

-   Laravel Framework
-   Tailwind CSS
-   Alpine.js
-   Lucide Icons
-   Chart.js

---

⭐ Agar loyiha foydali bo'lsa, star bering!
