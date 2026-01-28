# Installation Guide - Sistem Pendaftaran Reklame

Panduan lengkap instalasi dan konfigurasi Sistem Pendaftaran Reklame DPMPTSP.

## 📋 Prerequisites

Sebelum memulai, pastikan Anda memiliki:

### Required Software
- **PHP** >= 8.1
- **Composer** (PHP Package Manager)
- **Node.js** >= 18 (optional, untuk frontend build)
- **Git**
- **Database**:
  - SQLite (built-in, cocok untuk testing)
  - MySQL >= 8.0 (recommended untuk production)

### System Requirements
- RAM minimum 2GB
- Disk space minimum 1GB
- Apache/Nginx web server (untuk production)
- cURL extension
- OpenSSL extension

### Verifikasi Instalasi

Periksa versi yang terpasang:

```bash
php -v
composer -v
git --version
```

## 🚀 Step-by-Step Installation

### Step 1: Clone Repository

```bash
# Clone dari GitHub (atau gunakan ZIP download)
git clone https://github.com/yourusername/laravel-reklame.git
cd laravel-reklame

# Atau jika sudah di folder, hanya navigasi ke sana
cd c:\Users\Rafael\OneDrive\Desktop\reklame
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (optional, jika menggunakan frontend bundling)
npm install
```

**Output yang diharapkan**: Folder `vendor` dan `node_modules` tercipta, `composer.lock` diupdate.

### Step 3: Setup Environment File

```bash
# Copy file .env dari template
cp .env.example .env

# Atau jika menggunakan Windows:
copy .env.example .env
```

Edit file `.env` sesuai kebutuhan:

```env
APP_NAME="Sistem Reklame DPMPTSP"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=sqlite
# Untuk MySQL, gunakan:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=reklame_db
# DB_USERNAME=root
# DB_PASSWORD=

# Mail Configuration
MAIL_DRIVER=log
```

### Step 4: Generate Application Key

```bash
php artisan key:generate
```

**Output**: `Application key set successfully.`

File `.env` akan terisi otomatis dengan `APP_KEY`.

### Step 5: Create Database

#### Untuk SQLite (Development)
```bash
# Database sudah tercipta otomatis sebagai file
touch database/database.sqlite
```

#### Untuk MySQL (Production)
```sql
CREATE DATABASE reklame_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=reklame_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### Step 6: Run Migrations

```bash
# Jalankan semua migration
php artisan migrate

# Atau jika ingin reset database (development only!)
php artisan migrate:refresh
```

**Output**: Semua table tercipta di database:
- users
- roles
- permohonan_reklame
- persyaratan_dokumen
- approval_workflow
- activity_logs

### Step 7: Seed Database

```bash
# Jalankan seeder untuk create roles dan test users
php artisan db:seed

# Atau seed spesifik:
php artisan db:seed --class=RoleAndUserSeeder
```

**Test Users yang Tercipta**:
- pemohon@dpmptsp.local
- operator@dpmptsp.local
- kepala.seksi@dpmptsp.local
- kepala.bidang@dpmptsp.local
- admin@dpmptsp.local

(Password untuk semua: `password123`)

### Step 8: Create Storage Link

```bash
# Buat symlink untuk public storage
php artisan storage:link
```

Folder `public/storage` akan ter-link ke `storage/app/public`.

### Step 9: Set Permissions (Linux/Mac Only)

```bash
# Set permissions untuk storage dan bootstrap
chmod -R 775 storage bootstrap/cache
```

### Step 10: Start Development Server

```bash
php artisan serve
```

**Output**:
```
Starting Laravel development server: http://127.0.0.1:8000
```

Akses aplikasi di browser: **http://localhost:8000**

---

## ✅ Verifikasi Instalasi

Pastikan semua berjalan dengan baik:

### 1. Check Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPDO()
```

Seharusnya tidak error.

### 2. Check Database Tables
```bash
php artisan tinker
>>> DB::select('SHOW TABLES')
```

Seharusnya muncul semua table yang ada.

### 3. Test Login
Akses http://localhost:8000 dan login dengan:
- Email: `pemohon@dpmptsp.local`
- Password: `password123`

Seharusnya berhasil dan redirect ke dashboard.

### 4. Check Storage
Buat file test di `storage/app/public` dan akses via `http://localhost:8000/storage/`

---

## 🔧 Konfigurasi Lanjutan

### Konfigurasi Mail (Untuk Notifikasi)

Edit `.env`:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@dpmptsp.local
MAIL_FROM_NAME="DPMPTSP"
```

### Konfigurasi File Storage

Edit `config/filesystems.php` untuk mengubah storage disk:
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
]
```

### Konfigurasi Cache

Edit `.env`:
```env
CACHE_DRIVER=file
# Atau gunakan redis:
# CACHE_DRIVER=redis
```

---

## 🌐 Production Setup

### Deployment ke Server

1. **SSH ke Server**
```bash
ssh user@your-server.com
```

2. **Clone Repository**
```bash
git clone https://github.com/yourusername/laravel-reklame.git
cd laravel-reklame
```

3. **Install & Configure**
```bash
composer install --no-dev
npm install
npm run build

cp .env.example .env
# Edit .env dengan production settings
php artisan key:generate
```

4. **Database Setup**
```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

5. **Permissions**
```bash
chown -R www-data:www-data /path/to/laravel-reklame
chmod -R 755 /path/to/laravel-reklame
chmod -R 777 storage bootstrap/cache
```

6. **Web Server Configuration**

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/laravel-reklame/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### Apache
Enable `mod_rewrite`:
```bash
a2enmod rewrite
```

`.htaccess` di folder `public/` sudah ada.

### SSL Configuration

```bash
sudo certbot certonly --webroot -w /path/to/laravel-reklame/public -d your-domain.com
```

Redirect HTTP ke HTTPS di `.env`:
```env
APP_URL=https://your-domain.com
```

---

## 🐛 Troubleshooting

### Error: "No application key has been generated"
```bash
php artisan key:generate
```

### Error: "Failed to connect to database"
Pastikan:
- Database sudah dibuat
- `.env` memiliki konfigurasi DB yang benar
- Database server berjalan

### Error: "Storage link error"
```bash
php artisan storage:link
# Atau hapus dan buat ulang:
rm public/storage
php artisan storage:link
```

### Error: "No such file or directory" (migrations)
```bash
php artisan migrate:refresh --seed
```

### Error: "Composer dependencies out of date"
```bash
composer update
composer dump-autoload
```

### Error: "Permission denied" (Linux)
```bash
chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data .
```

### Port 8000 Already In Use
```bash
# Gunakan port berbeda:
php artisan serve --port=8001
```

---

## 📝 Post-Installation

Setelah instalasi berhasil:

1. **Buat user baru** melalui registrasi online
2. **Upload dokumen** untuk menguji file upload
3. **Test workflow** dengan user berbeda
4. **Backup database** secara berkala
5. **Monitor logs** di `storage/logs/laravel.log`

---

## 🔒 Security Checklist

- [ ] Set `APP_DEBUG=false` di production
- [ ] Set `APP_ENV=production` di production
- [ ] Configure HTTPS/SSL
- [ ] Set strong database passwords
- [ ] Configure firewall rules
- [ ] Regular backup database
- [ ] Update dependencies regularly
- [ ] Set file permissions correctly
- [ ] Configure email notifications
- [ ] Monitor activity logs

---

## 📞 Support

Jika mengalami masalah:

1. Baca dokumentasi di `DOKUMENTASI.md`
2. Check logs: `storage/logs/laravel.log`
3. Hubungi support: support@dpmptsp.local

---

**Last Updated**: 25 Januari 2026
