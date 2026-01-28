# Sistem Pendaftaran Reklame - User Management & Email Reminder

## Features Implemented

### 1. User Management UI (Admin Panel)

Admin dapat mengelola users melalui interface yang user-friendly:

#### Routes:
- `GET /admin/users` - Daftar semua users dengan pagination
- `GET /admin/users/create` - Form untuk buat user baru
- `POST /admin/users` - Simpan user baru
- `GET /admin/users/{user}/edit` - Form untuk edit user
- `PUT /admin/users/{user}` - Update user
- `DELETE /admin/users/{user}` - Soft delete user
- `POST /admin/users/{user}/restore` - Restore user yang terhapus

#### Features:
✅ Daftar users dengan pagination (15 per halaman)
✅ Edit user (nama, email, password, role)
✅ Hapus user (soft delete)
✅ Restore user yang terhapus
✅ Role assignment (Pemohon, Operator, Kepala Seksi, Kepala Bidang, Admin)
✅ Aktivasi/Deaktivasi user
✅ Authorization checks (hanya admin yang bisa akses)
✅ Activity logging untuk audit trail
✅ Proteksi super-admin dan current user

#### Menu Integration:
Sidebar menu terintegrasi di `resources/views/layouts/app.blade.php`:
- Hanya visible untuk admin role
- Link ke `/admin/users`
- Active state indicator

### 2. Email Reminder System

Sistem otomatis untuk mengirim reminder email ke staff tentang permohonan yang menunggu verifikasi.

#### Fitur:
✅ Reminder dikirim untuk permohonan > 7 hari
✅ Hanya dikirim 1x per 3 hari untuk permohonan yang sama
✅ Email dikirim ke operator & kepala seksi
✅ Support untuk "Diajukan" dan "Revisi Menunggu Verifikasi" status
✅ Automatic scheduling setiap hari jam 08:00
✅ Click-to-action button untuk langsung verifikasi

#### Installation Steps:

1. **Run Migration:**
```bash
php artisan migrate
```
Ini akan menambahkan kolom `reminder_sent_at` ke tabel `permohonan_reklame`.

2. **Manual Testing:**
```bash
# Test command langsung
php artisan permohonan:send-reminder
```

3. **Setup Scheduler (Production):**

Tambahkan cron job ke server untuk menjalankan Laravel scheduler:

```bash
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

Atau di cPanel:
1. Buka Cron Jobs
2. Tambah command: `php /path/to/app/artisan schedule:run`
3. Set waktu: Setiap menit (Common Settings: "Every minute")

4. **Config Email:**
Pastikan `.env` sudah configured:
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="DPMPTSP"
```

#### How It Works:

1. Command `permohonan:send-reminder` berjalan setiap hari jam 08:00
2. Query permohonan dengan status "Diajukan" atau "Revisi Menunggu Verifikasi"
3. Filter yang dibuat > 7 hari lalu
4. Hanya kirim jika belum pernah kirim reminder atau sudah 3 hari sejak reminder terakhir
5. Email dikirim ke semua operator dan kepala seksi yang aktif
6. Update `reminder_sent_at` timestamp setelah berhasil kirim

#### Email Template:
File: `resources/views/emails/reminder-pending-permohonan.blade.php`

Berisi:
- Nomor registrasi
- Nama pemohon
- Jenis reklame
- Lokasi
- Status
- Berapa hari menunggu
- Link button untuk lihat detail

#### Database Changes:

New column di `permohonan_reklame` table:
```
reminder_sent_at: timestamp (nullable)
```

Updated fillable di `PermohonanReklame` model:
```php
'reminder_sent_at',
```

#### Manual Sending (If needed):

```php
// Di tinker atau command
use App\Models\PermohonanReklame;
use App\Mail\PermohonanReminder;
use Illuminate\Support\Facades\Mail;

$permohonan = PermohonanReklame::find(1);
Mail::to('operator@example.com')->send(new PermohonanReminder($permohonan));
```

#### Monitoring:

Untuk cek jika command sudah berjalan:
1. Check `reminder_sent_at` field di database untuk permohonan
2. Cek email inbox di operator accounts
3. Check server logs untuk scheduling errors

#### Log File:
Errors biasanya log ke: `storage/logs/laravel.log`

---

## Testing Steps:

### User Management:
1. Login sebagai admin
2. Klik "Manajemen User" di sidebar
3. Create, Edit, Delete users
4. Test soft delete dan restore

### Email Reminder:
1. Create permohonan dengan created_at lebih dari 7 hari lalu (untuk testing):
   ```php
   // Di tinker
   use App\Models\PermohonanReklame;
   $p = PermohonanReklame::find(1);
   $p->created_at = now()->subDays(8);
   $p->save();
   ```

2. Run command:
   ```bash
   php artisan permohonan:send-reminder
   ```

3. Cek email inbox - seharusnya menerima reminder

## Architecture:

### Files Created/Modified:

**New Files:**
- `app/Http/Controllers/Admin/UserManagementController.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/edit.blade.php`
- `app/Mail/PermohonanReminder.php`
- `resources/views/emails/reminder-pending-permohonan.blade.php`
- `app/Console/Commands/SendPermohonanReminder.php`
- `app/Console/Kernel.php`
- `database/migrations/2026_01_25_120001_add_reminder_sent_at_to_permohonan_reklame.php`

**Modified Files:**
- `routes/web.php` - Added admin user management routes
- `resources/views/layouts/app.blade.php` - Added menu link
- `app/Models/PermohonanReklame.php` - Added reminder_sent_at to fillable & casts

## Security Notes:

✅ User Management:
- Authorization check untuk admin-only access
- Super-admin (id=1) tidak bisa didelete
- Current user tidak bisa menghapus dirinya sendiri
- Password di-hash saat disimpan

✅ Email Reminder:
- Hanya kirim ke operator yang active
- Email validation built-in
- Error handling untuk failed sends
- Prevents duplicate sends dengan timestamp tracking

## Future Enhancements:

- [ ] Bulk user operations (import/export)
- [ ] User activity log view
- [ ] Reminder frequency customization
- [ ] Email template customization via admin panel
- [ ] Reminder statistics dashboard
- [ ] Auto-escalation untuk old pending permohonan
