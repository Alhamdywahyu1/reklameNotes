# Sistem Pendaftaran Reklame - DPMPTSP
## Dokumentasi Lengkap

---

## 1. GAMBARAN UMUM SISTEM

### 1.1 Tujuan Sistem
Aplikasi berbasis web untuk mengelola pendaftaran dan pencatatan reklame/baliho pada DPMPTSP dengan alur perizinan berjenjang, pemeriksaan dokumen, approval bertahap, dan print out resmi.

### 1.2 Fitur Utama
- **Autentikasi & RBAC** - 4 role berbeda dengan akses terbatas
- **Form Pendaftaran Reklame** - Data pemohon dan data reklame lengkap
- **Dokumen & Persyaratan** - Checklist 9 dokumen dengan upload file
- **Workflow Status Bertahap** - 7 status dengan urutan tetap
- **Approval Berjenjang** - 3 level approval dengan audit trail
- **Print Out Dokumen Resmi** - PDF dengan format departemen
- **Dashboard & Statistik** - Berbeda untuk setiap role
- **Activity Logging** - Pencatatan semua aktivitas pengguna

---

## 2. STRUKTUR DATABASE & ERD

### 2.1 Tabel-Tabel Utama

```
DATABASE SCHEMA:
├── users
├── roles
├── permohonan_reklame
├── persyaratan_dokumen
├── approval_workflow
└── activity_logs
```

### 2.2 Entity Relationship Diagram (ERD)

```
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  ┌──────────────┐         ┌──────────────┐                    │
│  │   USERS      │◄────────┤   ROLES      │                    │
│  ├──────────────┤         ├──────────────┤                    │
│  │ id (PK)      │         │ id (PK)      │                    │
│  │ name         │         │ name         │                    │
│  │ email        │         │ slug         │                    │
│  │ password     │         │ description  │                    │
│  │ nik          │         │ created_at   │                    │
│  │ phone        │         │ updated_at   │                    │
│  │ address      │         │ deleted_at   │                    │
│  │ role_id (FK) │         └──────────────┘                    │
│  │ is_active    │                                              │
│  │ last_login   │                                              │
│  │ created_at   │                                              │
│  │ updated_at   │                                              │
│  │ deleted_at   │                                              │
│  └──────┬───────┘                                              │
│         │                                                      │
│         │ 1:M                                                  │
│         └──────────────────────┐                              │
│                                │                              │
│  ┌──────────────────────────────▼────────────────────────┐   │
│  │         PERMOHONAN_REKLAME                            │   │
│  ├───────────────────────────────────────────────────────┤   │
│  │ id (PK)                                               │   │
│  │ nomor_registrasi (UNIQUE)                             │   │
│  │ user_id (FK -> users)                                 │   │
│  │ nama_pemohon                                          │   │
│  │ alamat_pemohon                                        │   │
│  │ nomor_telepon                                         │   │
│  │ nik                                                   │   │
│  │ npwp                                                  │   │
│  │ jenis_reklame (Permanen/Non Permanen)                 │   │
│  │ ukuran_reklame                                        │   │
│  │ jumlah_reklame                                        │   │
│  │ narasi_reklame                                        │   │
│  │ lokasi_pemasangan                                     │   │
│  │ file_ktp, file_npwp, file_desain                      │   │
│  │ status (enum: Draft, Diajukan, ...)                   │   │
│  │ keterangan_penolakan                                  │   │
│  │ created_at, updated_at, deleted_at                    │   │
│  └──────────────┬──────────────────────────────────────┬─┘   │
│                 │ 1:M                                  │      │
│         ┌───────▼──────────┐            ┌──────────────▼───┐  │
│         │ PERSYARATAN_     │            │ APPROVAL_        │  │
│         │ DOKUMEN          │            │ WORKFLOW         │  │
│         ├──────────────────┤            ├──────────────────┤  │
│         │ id (PK)          │            │ id (PK)          │  │
│         │ permohonan_id(FK)│            │ permohonan_id(FK)│  │
│         │ jenis_persyaratan│            │ user_id (FK)     │  │
│         │ is_lengkap       │            │ role_id (FK)     │  │
│         │ file_dokumen     │            │ status_approval  │  │
│         │ keterangan       │            │ keputusan        │  │
│         │ created_at       │            │ keterangan       │  │
│         │ updated_at       │            │ tanggal_approval │  │
│         │ deleted_at       │            │ created_at       │  │
│         └──────────────────┘            └──────────────────┘  │
│                                                                 │
│  ┌────────────────────────────────────────────────────────┐   │
│  │              ACTIVITY_LOGS                             │   │
│  ├────────────────────────────────────────────────────────┤   │
│  │ id (PK)                                                │   │
│  │ user_id (FK -> users)                                  │   │
│  │ action (CREATE, UPDATE, DELETE, SUBMIT, APPROVAL...)  │   │
│  │ model_type, model_id                                   │   │
│  │ description                                            │   │
│  │ old_values, new_values (JSON)                          │   │
│  │ ip_address, user_agent                                 │   │
│  │ created_at                                             │   │
│  └────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. ROLE DAN PERMISSION

### 3.1 Daftar Role

| No | Role | Slug | Deskripsi |
|--|--|--|--|
| 1 | Pengguna (Pemohon) | `pemohon` | Mengajukan permohonan reklame |
| 2 | Operator | `operator` | Verifikasi dokumen & approval tahap 1 |
| 3 | Kepala Seksi | `kepala_seksi` | Approval tahap 2 |
| 4 | Kepala Bidang | `kepala_bidang` | Final approval (tahap 3) |
| 5 | Admin | `admin` | Manajemen sistem |

### 3.2 Permission Matrix

| Fitur | Pemohon | Operator | Kepala Seksi | Kepala Bidang | Admin |
|--|--|--|--|--|--|
| **Permohonan** |
| Buat Permohonan | ✓ | ✗ | ✗ | ✗ | ✗ |
| Edit Permohonan (Draft/Ditolak) | ✓ | ✗ | ✗ | ✗ | ✗ |
| Lihat Permohonan Sendiri | ✓ | ✓* | ✓* | ✓* | ✓ |
| Ajukan Permohonan | ✓ | ✗ | ✗ | ✗ | ✗ |
| **Approval** |
| Verifikasi Operator | ✗ | ✓ | ✗ | ✗ | ✗ |
| Approve Kepala Seksi | ✗ | ✗ | ✓ | ✗ | ✗ |
| Approve Kepala Bidang | ✗ | ✗ | ✗ | ✓ | ✗ |
| **Print** |
| Cetak Dokumen | ✗ | ✓ | ✗ | ✗ | ✓ |
| **Dashboard** |
| Lihat Dashboard | ✓ | ✓ | ✓ | ✓ | ✓ |

*Hanya yang dalam workflow mereka

---

## 4. WORKFLOW STATUS PERMOHONAN

### 4.1 Status Flow Chart

```
                    ┌─────────────┐
                    │   DRAFT     │
                    └──────┬──────┘
                           │
                    (User Submit)
                           │
                    ┌──────▼──────────┐
                    │   DIAJUKAN      │
                    └──────┬──────────┘
                           │
           ┌───────────────┴────────────────┐
           │                                │
        (Operator)                       (Operator)
     (Diterima)                         (Ditolak)
           │                                │
   ┌───────▼──────────┐           ┌────────▼────────┐
   │ DIVERIFIKASI     │           │ DITOLAK OPERATOR│
   │   OPERATOR       │           │                 │
   └───────┬──────────┘           └────────┬────────┘
           │                                │
           │                    (User Revise & Resubmit)
           │                                │
        (Kepala Seksi)              ┌───────▼──────┐
     ┌────┴────────────┐            │  DIAJUKAN    │
     │                 │            └──────┬───────┘
  (Approve)        (Reject)                 │
     │                 │            (Ulang dari awal)
   ┌─▼────────────────▼──────────┐
   │ DISETUJUI KEPALA SEKSI       │
   │ atau                         │
   │ DITOLAK KEPALA SEKSI         │
   └─┬────────────────┬───────────┘
     │                │
     │         (User Revise)
     │                │
     │            ┌───▼──────┐
     │            │DIAJUKAN  │
     │            └──────────┘
     │
  (Kepala Bidang)
     │
  ┌──▼──────────────────┐
  │ DISETUJUI KEPALA    │
  │ BIDANG (FINAL)      │
  └─────────────────────┘
     │
  (Printable)
```

### 4.2 Deskripsi Status

| Status | Deskripsi |
|--|--|
| **Draft** | Permohonan baru, belum diajukan |
| **Diajukan** | Permohonan sudah dikirim ke Operator |
| **Diverifikasi Operator** | Operator selesai verifikasi, lolos |
| **Ditolak Operator** | Operator menolak, kembali ke Draft |
| **Disetujui Kepala Seksi** | Kepala Seksi setuju, melanjut ke Kepala Bidang |
| **Ditolak Kepala Seksi** | Kepala Seksi menolak, kembali ke Draft |
| **Disetujui Kepala Bidang** | Final approval, dokumen siap cetak |

---

## 5. STRUKTUR FOLDER APLIKASI

```
laravel-reklame/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   ├── RegisteredUserController.php
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   └── VerifyEmailController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── PermohonanReklameController.php
│   │   │   ├── ApprovalController.php
│   │   │   └── PrintController.php
│   │   └── Middleware/
│   │       └── CheckRole.php
│   └── Models/
│       ├── User.php
│       ├── Role.php
│       ├── PermohonanReklame.php
│       ├── PersyaratanDokumen.php
│       ├── ApprovalWorkflow.php
│       └── ActivityLog.php
├── database/
│   ├── migrations/
│   │   ├── 2026_01_25_000001_create_roles_table.php
│   │   ├── 2026_01_25_000002_modify_users_table.php
│   │   ├── 2026_01_25_000003_create_permohonan_reklame_table.php
│   │   ├── 2026_01_25_000004_create_persyaratan_dokumen_table.php
│   │   ├── 2026_01_25_000005_create_approval_workflow_table.php
│   │   └── 2026_01_25_000006_create_activity_logs_table.php
│   └── seeders/
│       ├── RoleAndUserSeeder.php
│       └── DatabaseSeeder.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   ├── login.blade.php
│   │   ├── register.blade.php
│   │   ├── forgot-password.blade.php
│   │   └── reset-password.blade.php
│   ├── dashboard/
│   │   ├── pemohon.blade.php
│   │   ├── operator.blade.php
│   │   ├── kepala-seksi.blade.php
│   │   ├── kepala-bidang.blade.php
│   │   └── admin.blade.php
│   ├── permohonan/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── approval/
│   │   ├── dashboard.blade.php
│   │   ├── verify-operator.blade.php
│   │   ├── approve-kepala-seksi.blade.php
│   │   └── approve-kepala-bidang.blade.php
│   ├── print/
│   │   ├── preview.blade.php
│   │   └── pdf.blade.php
│   └── welcome.blade.php
└── routes/
    └── web.php
```

---

## 6. FITUR-FITUR UTAMA

### 6.1 Authentikasi & Registrasi
- Register sebagai Pemohon dengan validasi NIK
- Login dengan email & password
- Password reset via email
- Email verification
- Session management
- Last login tracking

### 6.2 Form Permohonan Reklame
Data yang dikumpulkan:
1. **Data Pemohon:**
   - Nama Pemohon
   - Alamat Pemohon
   - Nomor Telepon
   - NIK (16 digit, unik)
   - NPWP (opsional)

2. **Data Reklame:**
   - Jenis Reklame (Permanen/Non Permanen)
   - Ukuran Reklame
   - Jumlah Reklame
   - Narasi/Deskripsi Reklame
   - Lokasi Pemasangan

3. **Dokumen:**
   - Scan KTP (PDF/JPG/PNG, max 5MB)
   - Scan NPWP (PDF/JPG/PNG, max 5MB)
   - Desain Reklame (PDF/JPG/PNG, max 5MB)

**Nomor Registrasi Format:** RKL-YYYY-MM-XXXXX

### 6.3 Dokumen & Persyaratan Checklist

| No | Persyaratan | Tipe |
|--|--|--|
| 1 | Fotocopy KTP berwarna | Required |
| 2 | Fotocopy NPWP berwarna | Required |
| 3 | Fotocopy Akta Pendirian | Required |
| 4 | Fotocopy Retribusi Pajak Reklame | Required |
| 5 | Data Isian Pemohon | Required |
| 6 | Surat Pernyataan Pertanggungjawaban Konstruksi | Required |
| 7 | Foto kondisi & visualisasi reklame | Required |
| 8 | Gambar konstruksi bidang | Required |
| 9 | Surat Kuasa | Optional |

Operator dapat:
- Menandai persyaratan sebagai lengkap/tidak lengkap
- Menambahkan file dokumen
- Menambahkan keterangan

### 6.4 Workflow Approval
- **Operator Verification**: Verifikasi dokumen, marking checklist
- **Kepala Seksi Approval**: Review keputusan Operator
- **Kepala Bidang Approval**: Final approval
- Setiap approval menyimpan: status, tanggal, jam, keterangan, user

### 6.5 Print Out Dokumen
- **Kondisi**: Hanya jika status = "Disetujui Kepala Bidang"
- **User**: Hanya Operator & Admin
- **Format PDF** dengan:
  - Kop DPMPTSP
  - Nomor registrasi & tanggal
  - Data pemohon lengkap
  - Checklist persyaratan
  - Riwayat approval
  - Tanda tangan digital

### 6.6 Dashboard & Statistik
Berbeda untuk setiap role:
- **Pemohon**: Total, draft, pending, disetujui, ditolak
- **Operator**: Total pending, diverifikasi, ditolak
- **Kepala Seksi**: Total pending, disetujui, ditolak
- **Kepala Bidang**: Total pending, final approved
- **Admin**: Total users, permohonan, statistik, activity log

### 6.7 Activity Logging & Audit Trail
Mencatat setiap aksi:
- User (who)
- Action (create, update, delete, submit, approval, print)
- Model & ID
- Deskripsi
- Old values & new values (JSON)
- IP address
- User agent
- Timestamp

---

## 7. KEAMANAN

### 7.1 Implementasi
- ✓ CSRF Protection (built-in Laravel)
- ✓ Server-side Validation
- ✓ Role-Based Middleware
- ✓ Authorization Checks
- ✓ Soft Delete (data tidak terhapus)
- ✓ Password Hashing (bcrypt)
- ✓ Secure File Upload (validated mime types)
- ✓ Activity Logging
- ✓ Input Sanitization

### 7.2 Flow Keamanan

```
Request → CSRF Token Check → Auth Check → Role Middleware 
  → Authorization Check → Validation → Processing → Logging
```

---

## 8. TEKNOLOGI YANG DIGUNAKAN

- **Framework**: Laravel 12
- **Database**: SQLite / MySQL
- **Frontend**: Bootstrap 5, Blade Templating
- **PDF Generation**: DomPDF
- **Authentication**: Laravel Auth (built-in)
- **Authorization**: Custom Middleware & Policies

---

## 9. INSTALASI & SETUP

### 9.1 Instalasi

```bash
# Clone repository
git clone [repo-url]
cd laravel-reklame

# Install dependencies
composer install

# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate

# Setup database
php artisan migrate --seed

# Link storage
php artisan storage:link

# Run server
php artisan serve
```

### 9.2 Test Account

| Role | Email | Password |
|--|--|--|
| Pemohon | pemohon@dpmptsp.local | password123 |
| Operator | operator@dpmptsp.local | password123 |
| Kepala Seksi | kepala.seksi@dpmptsp.local | password123 |
| Kepala Bidang | kepala.bidang@dpmptsp.local | password123 |
| Admin | admin@dpmptsp.local | password123 |

---

## 10. API ENDPOINTS

### 10.1 Auth Routes
```
POST   /login              - Login
POST   /register           - Register
POST   /logout             - Logout
GET    /forgot-password    - Forgot password form
POST   /forgot-password    - Send reset email
GET    /reset-password/:token - Reset form
POST   /reset-password     - Update password
```

### 10.2 Permohonan Routes
```
GET    /dashboard                           - Dashboard
GET    /permohonan                          - List permohonan
GET    /permohonan/create                   - Create form
POST   /permohonan                          - Store permohonan
GET    /permohonan/:id                      - Show detail
GET    /permohonan/:id/edit                 - Edit form
PUT    /permohonan/:id                      - Update permohonan
POST   /permohonan/:id/submit               - Submit permohonan
DELETE /permohonan/:id                      - Delete permohonan
```

### 10.3 Approval Routes
```
GET    /approval/dashboard                  - Approval dashboard
GET    /approval/:id/verify                 - Verify operator form
POST   /approval/:id/verify                 - Store verification
GET    /approval/:id/approve-seksi          - Approve seksi form
POST   /approval/:id/approve-seksi          - Store approval
GET    /approval/:id/approve-bidang         - Approve bidang form
POST   /approval/:id/approve-bidang         - Store final approval
```

### 10.4 Print Routes
```
GET    /print/:id/preview                   - Print preview
GET    /print/:id/pdf                       - Download PDF
```

---

## 11. DATABASE QUERIES CONTOH

### 11.1 Permohonan Pending untuk Operator
```sql
SELECT * FROM permohonan_reklame 
WHERE status = 'Diajukan' 
ORDER BY created_at ASC;
```

### 11.2 Approval History
```sql
SELECT aw.*, u.name, r.name as role_name
FROM approval_workflow aw
JOIN users u ON aw.user_id = u.id
JOIN roles r ON aw.role_id = r.id
WHERE aw.permohonan_id = ? 
ORDER BY aw.created_at DESC;
```

### 11.3 Activity Audit Trail
```sql
SELECT * FROM activity_logs 
WHERE model_type = 'PermohonanReklame' 
AND model_id = ? 
ORDER BY created_at DESC;
```

---

## 12. BEST PRACTICES YANG DIIMPLEMENTASIKAN

✓ **Clean Architecture**
- Separation of concerns (Models, Controllers, Services)
- Single responsibility principle
- DRY (Don't Repeat Yourself)

✓ **SOLID Principles**
- Dependency injection
- Interface segregation
- Open/closed principle

✓ **Laravel Best Practices**
- Middleware untuk authorization
- Model relationships
- Query optimization dengan eager loading
- Blade template inheritance
- Form request validation
- Exception handling

✓ **Security**
- Input validation
- CSRF protection
- SQL injection prevention (prepared statements)
- XSS protection (blade escaping)
- Secure password hashing
- Role-based access control

✓ **Database**
- Foreign key constraints
- Proper indexing
- Soft deletes for audit
- Transaction support
- Migration versioning

---

## 13. TROUBLESHOOTING

### 13.1 Storage Link Error
```bash
php artisan storage:link
```

### 13.2 Database Reset
```bash
php artisan migrate:refresh --seed
```

### 13.3 Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## 14. MAINTENANCE & MONITORING

### 14.1 Activity Log Cleanup
```bash
# Buat command untuk cleanup
php artisan make:command CleanupActivityLogs
```

### 14.2 Database Backup
```bash
# Backup database berkala
sqlite3 database/database.sqlite ".backup database_backup.sqlite"
```

### 14.3 Log Monitoring
```bash
# Monitor aplikasi logs
tail -f storage/logs/laravel.log
```

---

## CATATAN AKHIR

Sistem ini dibangun dengan standar enterprise dan best practices untuk e-Government. Semua komponen dirancang untuk:
- Keamanan tingkat tinggi
- Skalabilitas
- Maintainability
- User-friendly interface
- Audit trail lengkap
- Compliance dengan SOP pemerintahan

Untuk pertanyaan atau pengembangan lebih lanjut, dokumentasi ini dapat diperluas sesuai kebutuhan.

---

**Versi**: 1.0
**Tanggal**: 25 Januari 2026
**Status**: Production Ready
