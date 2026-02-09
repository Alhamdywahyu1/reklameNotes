# Implementasi SURAT PERNYATAAN - Dokumentasi Lengkap

## 📋 Overview
Telah berhasil menambahkan fitur **SURAT PERNYATAAN** (Statement Letter) ke sistem aplikasi izin reklame Kabupaten Bangkalan. Fitur ini memungkinkan pemohon untuk membuat, mengedit, dan menyimpan surat pernyataan sesuai dengan ketentuan yang berlaku.

---

## ✅ File-File yang Dibuat/Dimodifikasi

### 1. **Database Migration**
**File:** `database/migrations/2026_02_02_000001_create_surat_pernyataan_table.php`

**Apa:** Migration untuk membuat tabel `surat_pernyataan` dengan struktur lengkap
- **Kolom utama:**
  - `permohonan_id` (foreign key) - Relasi ke tabel permohonan_reklame
  - `user_id` (foreign key) - Relasi ke tabel users
  - `nama_pemohon`, `pekerjaan`, `alamat_pemohon`, `no_ktp` - Data pemohon
  - `setuju_syarat_1` sampai `setuju_syarat_8` - Checkbox untuk 8 syarat dan ketentuan
  - `status` (enum: draft, submitted, verified, rejected) - Status pernyataan
  - `file_tanda_tangan`, `file_materai` - Dokumen pendukung
  - `tanggal_pernyataan` - Tanggal pernyataan dibuat
  - `submitted_at`, `verified_at` - Audit timestamp

---

### 2. **Model**
**File:** `app/Models/SuratPernyataan.php`

**Fitur:**
- Relasi `belongsTo` dengan `PermohonanReklame` dan `User`
- Method `areAllConditionsAgreed()` - Validasi apakah semua syarat disetujui
- Cast otomatis untuk boolean dan date fields

---

### 3. **Controller**
**File:** `app/Http/Controllers/SuratPernyataanController.php`

**Methods:**
- `create()` - Tampilkan form pembuatan Surat Pernyataan
- `store()` - Simpan/submit surat pernyataan
- `show()` - Lihat detail surat pernyataan
- `edit()` - Edit surat pernyataan
- `update()` - Update surat pernyataan
- `downloadPdf()` - Download surat pernyataan dalam format PDF
- `destroy()` - Hapus surat pernyataan

**Validasi:**
- Semua field data pemohon wajib diisi
- Semua 8 checkbox syarat wajib dicentang (accepted)
- File tanda tangan dan materai: optional, format PDF/JPG/PNG, max 5MB

---

### 4. **Views**

#### a. `resources/views/surat-pernyataan/create.blade.php`
Form untuk membuat/submit Surat Pernyataan dengan:
- Section data pemohon (nama, pekerjaan, alamat, no KTP)
- 8 checkbox syarat dan ketentuan yang harus disetujui
- Upload file tanda tangan dan materai
- Sidebar informasi permohonan

#### b. `resources/views/surat-pernyataan/show.blade.php`
Halaman untuk melihat detail Surat Pernyataan dengan:
- Data pemohon yang sudah diisi
- Daftar syarat yang disetujui
- Link download dokumen pendukung
- Keterangan penolakan (jika ditolak)
- Riwayat audit (dibuat, diupdate, disubmit, diverifikasi)
- Tombol edit dan download PDF

#### c. `resources/views/surat-pernyataan/edit.blade.php`
Form untuk mengedit Surat Pernyataan (sama seperti create, untuk revisi jika ditolak)

#### d. `resources/views/surat-pernyataan/pdf.blade.php`
Template PDF untuk cetak Surat Pernyataan dengan format resmi

---

### 5. **Routes**
**File:** `routes/web.php`

**Routes yang ditambahkan:**
```php
// Surat Pernyataan Routes (untuk Pemohon only)
Route::middleware('role:pemohon')->group(function () {
    Route::get('surat-pernyataan/{permohonan}/create', [SuratPernyataanController::class, 'create'])->name('surat-pernyataan.create');
    Route::post('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'store'])->name('surat-pernyataan.store');
    Route::get('surat-pernyataan/{permohonan}/edit', [SuratPernyataanController::class, 'edit'])->name('surat-pernyataan.edit');
    Route::put('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'update'])->name('surat-pernyataan.update');
    Route::delete('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'destroy'])->name('surat-pernyataan.destroy');
});

// Surat Pernyataan Show & Download - accessible to pemohon and staff
Route::middleware('auth')->group(function () {
    Route::get('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'show'])->name('surat-pernyataan.show');
    Route::get('surat-pernyataan/{permohonan}/download-pdf', [SuratPernyataanController::class, 'downloadPdf'])->name('surat-pernyataan.download-pdf');
});
```

---

### 6. **Model Relation**
**File:** `app/Models/PermohonanReklame.php` (Modified)

**Tambahan:**
```php
public function suratPernyataan()
{
    return $this->hasOne(SuratPernyataan::class, 'permohonan_id');
}
```

---

### 7. **View Integration**
**File:** `resources/views/permohonan/show.blade.php` (Modified)

**Tambahan:**
- Import SuratPernyataanController di routes
- Modifikasi PermohonanReklameController show method untuk pass $suratPernyataan
- Tambahan section "Surat Pernyataan" dengan:
  - Tombol "Buat Surat Pernyataan" (jika belum ada)
  - Tombol "Edit" (jika status draft)
  - Tombol "Lihat" dan "Download PDF"
  - Status badge (draft, submitted, verified, rejected)
  - Informasi semua syarat disetujui

---

## 🔄 Alur Penggunaan

### Untuk Pemohon:
1. **Buat Permohonan** → Halaman detail permohonan ditampilkan
2. **Buat Surat Pernyataan** → Klik tombol "Buat Surat Pernyataan"
3. **Isi Form** → Masukkan data diri, tandatangani checkbox syarat
4. **Upload Dokumen** → Upload bukti tanda tangan dan materai (opsional)
5. **Submit** → Klik "Simpan & Submit"
6. **Tunggu Verifikasi** → Status akan berubah menjadi "submitted"

### Untuk Operator/Staff:
1. **Lihat Permohonan** → Buka halaman detail permohonan
2. **Verifikasi Surat** → Klik "Lihat" atau "Download PDF"
3. **Approve/Reject** → Update status menjadi "verified" atau "rejected"
4. **Berikan Keterangan** → Jika ditolak, isikan keterangan penolakan

---

## 📝 8 Syarat dan Ketentuan yang Harus Disetujui

1. Bahwa saya sanggup menaati segala peraturan/ketentuan yang diterapkan oleh Pemerintah Kabupaten Bangkalan
2. Bahwa saya akan menggunakan izin untuk kepentingan reklame sesuai ketentuan yang berlaku
3. Bahwa saya tidak akan mengubah konstruksi atau memindah lokasi tanpa seizin
4. Bahwa saya tidak akan memindah tangankan surat izin kepada pihak lain tanpa seizin
5. Bahwa saya bertanggung jawab atas konstruksi, kebersihan, ketertiban, dan keindahan reklame
6. Bahwa saya bertanggung jawab atas barang pihak lain dan kecelakaan yang diakibatkan reklame
7. Bahwa saya harus membongkar reklame paling lambat 7 hari setelah masa berlaku berakhir
8. Bahwa saya bersedia reklame dibongkar atau dikenakan sanksi jika tidak mematuhi janji

---

## 🔐 Authorization & Roles

| Role | Akses |
|------|-------|
| **Pemohon** | Create, Edit (draft), View, Download PDF |
| **Operator** | View, Download PDF, Verify/Reject |
| **Kepala Seksi** | View, Download PDF |
| **Kepala Bidang** | View, Download PDF |
| **Admin** | View, Download PDF |

---

## 📂 File Structure

```
app/
├── Models/
│   ├── SuratPernyataan.php (NEW)
│   └── PermohonanReklame.php (MODIFIED)
├── Http/Controllers/
│   ├── SuratPernyataanController.php (NEW)
│   └── PermohonanReklameController.php (MODIFIED)
└── ...

database/
└── migrations/
    └── 2026_02_02_000001_create_surat_pernyataan_table.php (NEW)

resources/views/
└── surat-pernyataan/
    ├── create.blade.php (NEW)
    ├── show.blade.php (NEW)
    ├── edit.blade.php (NEW)
    └── pdf.blade.php (NEW)

routes/
└── web.php (MODIFIED)
```

---

## 🚀 Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

### 3. Test Fitur
- Login sebagai pemohon
- Buat permohonan izin reklame
- Klik "Buat Surat Pernyataan" di halaman detail
- Isi form dan submit

---

## ✨ Fitur Tambahan

### PDF Generation
- Menggunakan barryvdh/laravel-dompdf untuk generate PDF
- Template PDF dengan format resmi dari Pemerintah Kabupaten Bangkalan
- Include: materai box, tempat tanda tangan, dan footer

### File Management
- Secure file naming menggunakan timestamp + unique ID
- Storage path: `surat-pernyataan/tanda-tangan/` dan `surat-pernyataan/materai/`
- Auto-delete file lama saat update/delete

### Validation
- Client-side: HTML5 validation
- Server-side: Laravel validation rules
- Custom error messages dalam Bahasa Indonesia

---

## 📌 Catatan Penting

1. **Database:** Pastikan sudah run migration sebelum testing
2. **File Storage:** Pastikan folder `storage/app/public/surat-pernyataan/` dapat ditulis
3. **DOMPDF:** Memastikan barryvdh/laravel-dompdf sudah terinstall via composer
4. **Disk Configuration:** Gunakan `public` disk untuk file storage agar bisa diakses via URL

---

## 🔜 Future Enhancement

1. Digital signature implementation
2. Integration dengan sistem approval workflow
3. Automated email notification saat status berubah
4. Batch PDF generation untuk multiple submissions
5. Template customization per dinas

---

**Status:** ✅ Implementasi Selesai  
**Tanggal:** 2 Februari 2026  
**Versi:** 1.0
