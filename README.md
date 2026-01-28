<<<<<<< HEAD
# reclameNotes
=======
# Sistem Pendaftaran Reklame DPMPTSP

Aplikasi berbasis Laravel 12 untuk mengelola pendaftaran reklame/baliho di DPMPTSP dengan alur perizinan berjenjang dan approval bertahap.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Persyaratan Sistem](#persyaratan-sistem)
- [Instalasi](#instalasi)
- [Penggunaan](#penggunaan)
- [Troubleshooting](#troubleshooting)

## ✨ Fitur Utama

✅ **Autentikasi & RBAC** - 4 role berbeda dengan middleware  
✅ **Pendaftaran Reklame** - Form lengkap dengan nomor registrasi otomatis  
✅ **Dokumen & Persyaratan** - 9 item checklist dengan upload file  
✅ **Workflow Berjenjang** - 7 status dengan urutan tetap  
✅ **Approval 3 Level** - Operator → Kepala Seksi → Kepala Bidang  
✅ **Print PDF** - Format resmi dengan kop surat  
✅ **Dashboard Role-Based** - UI berbeda untuk setiap role  
✅ **Activity Logging** - Audit trail lengkap dengan IP tracking  

## 💻 Persyaratan Sistem

- PHP >= 8.1
- Composer
- SQLite / MySQL
- Laravel 12

## 🚀 Instalasi

```bash
# 1. Install dependencies
composer install

# 2. Setup environment
cp .env.example .env
php artisan key:generate

# 3. Migration & Seeding
php artisan migrate --seed

# 4. Storage link
php artisan storage:link

# 5. Run server
php artisan serve
```

**URL**: http://localhost:8000

## 🔐 Test Accounts

| Role | Email | Password |
|--|--|--|
| Pemohon | pemohon@dpmptsp.local | password123 |
| Operator | operator@dpmptsp.local | password123 |
| Kepala Seksi | kepala.seksi@dpmptsp.local | password123 |
| Kepala Bidang | kepala.bidang@dpmptsp.local | password123 |
| Admin | admin@dpmptsp.local | password123 |

## 📖 Penggunaan

### Alur Pemohon
1. Register → 2. Create Permohonan → 3. Fill Data → 4. Upload Documents → 5. Submit → 6. Track Status

### Alur Operator
1. View Pending → 2. Verify Documents → 3. Check Persyaratan → 4. Decision (Terima/Tolak) → 5. Forward to Kepala Seksi

### Alur Kepala Seksi
1. View Pending → 2. Review Decision → 3. Approve/Reject → 4. Forward to Kepala Bidang

### Alur Kepala Bidang
1. View Final Queue → 2. Final Approval → 3. Enable Print

### Alur Print
1. View Final Approved → 2. Print Preview → 3. Download PDF

## 🛠️ Troubleshooting

**Storage Link Error**
```bash
php artisan storage:link
```

**Database Reset**
```bash
php artisan migrate:refresh --seed
```

**Clear Cache**
```bash
php artisan cache:clear && php artisan config:clear && php artisan view:clear
```

## 📚 Dokumentasi Lengkap

Lihat `DOKUMENTASI.md` untuk ERD, Flowchart, Database Queries, dan Best Practices.

## 👨‍💻 Support

- Email: support@dpmptsp.local
- Telepon: (0274) 123456
- Website: www.dpmptsp.local

---

**Version**: 1.0 | **Status**: Production Ready ✅ | **Last Updated**: 25 Januari 2026

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> 27a3977 (Push folder pertama)
