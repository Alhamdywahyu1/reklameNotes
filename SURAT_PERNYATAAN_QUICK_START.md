# 🚀 Quick Start - Fitur Surat Pernyataan

## Langkah-Langkah Setup

### 1️⃣ Run Migration
```bash
php artisan migrate
```

### 2️⃣ Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

### 3️⃣ Testing

#### Sebagai Pemohon:
1. Login dengan akun pemohon
2. Masuk ke halaman **Permohonan Saya**
3. Buat permohonan reklame baru
4. Di halaman detail permohonan, cari section **"Surat Pernyataan"**
5. Klik tombol **"Buat Surat Pernyataan"**
6. Isi semua field yang diperlukan:
   - ✅ Nama pemohon
   - ✅ Pekerjaan
   - ✅ Alamat lengkap
   - ✅ No KTP
   - ✅ Tandatangani semua 8 checkbox syarat
   - ✅ Pilih tanggal pernyataan
   - ⭕ Upload bukti tanda tangan (opsional)
   - ⭕ Upload bukti materai Rp 10.000 (opsional)
7. Klik **"Simpan & Submit"**

---

## 📋 Form Structure

### Data Pemohon
```
Nama            : [Input Text]
Pekerjaan       : [Input Text]
Alamat          : [Textarea]
No KTP          : [Input Text]
```

### Syarat & Ketentuan (8 Item)
Semua harus dicentang sebelum submit:
- [ ] Syarat 1 - Menaati peraturan
- [ ] Syarat 2 - Untuk kepentingan reklame saja
- [ ] Syarat 3 - Tidak mengubah konstruksi
- [ ] Syarat 4 - Tidak memindahkan izin
- [ ] Syarat 5 - Bertanggung jawab konstruksi
- [ ] Syarat 6 - Bertanggung jawab kecelakaan
- [ ] Syarat 7 - Bongkar reklame max 7 hari
- [ ] Syarat 8 - Terima sanksi jika melanggar

### Dokumen
```
Tanggal Pernyataan           : [Date Picker]
Upload Tanda Tangan         : [File Upload - PDF/JPG/PNG]
Upload Materai Rp 10.000    : [File Upload - PDF/JPG/PNG]
```

---

## 🔗 Route Map

```
GET     /surat-pernyataan/{permohonan}/create      → Tampil Form
POST    /surat-pernyataan/{permohonan}             → Submit Form
GET     /surat-pernyataan/{permohonan}             → Lihat Detail
GET     /surat-pernyataan/{permohonan}/edit        → Edit Form
PUT     /surat-pernyataan/{permohonan}             → Update
DELETE  /surat-pernyataan/{permohonan}             → Hapus
GET     /surat-pernyataan/{permohonan}/download-pdf → Download PDF
```

---

## 📊 Status Flow

```
[Draft] → [Submitted] → [Verified] ✅
          ↓
        [Rejected] → (Edit) → [Submitted]
```

---

## 💾 Database Structure

### Table: surat_pernyataan
```sql
id                  : INT (PK)
permohonan_id       : INT (FK) - Unique
user_id             : INT (FK)
nama_pemohon        : VARCHAR(255)
pekerjaan           : VARCHAR(255)
alamat_pemohon      : TEXT
no_ktp              : VARCHAR(20)
status              : ENUM(draft, submitted, verified, rejected)
setuju_syarat_1-8   : BOOLEAN
file_tanda_tangan   : VARCHAR(255)
file_materai        : VARCHAR(255)
tanggal_pernyataan  : DATE
keterangan_penolakan: TEXT
submitted_at        : TIMESTAMP
verified_at         : TIMESTAMP
created_at          : TIMESTAMP
updated_at          : TIMESTAMP
deleted_at          : TIMESTAMP (SoftDelete)
```

---

## 🎨 UI Components

### Section di Halaman Permohonan Detail
```html
<!-- Surat Pernyataan Section -->
<div class="card mb-3">
  <div class="card-header">
    <h5>📄 Surat Pernyataan</h5>
    <button>Buat/Edit/Lihat</button>
  </div>
  <div class="card-body">
    Status: [Badge]
    Semua Syarat: [Badge]
    Disubmit: [Timestamp]
    [View Detail] [Download PDF]
  </div>
</div>
```

---

## 🔐 Permission Matrix

| Action | Pemohon | Operator | Kepala Seksi | Kepala Bidang | Admin |
|--------|---------|----------|--------------|---------------|-------|
| Create | ✅ | ❌ | ❌ | ❌ | ❌ |
| Edit (Draft) | ✅ | ❌ | ❌ | ❌ | ❌ |
| View | ✅ | ✅ | ✅ | ✅ | ✅ |
| Download PDF | ✅ | ✅ | ✅ | ✅ | ✅ |
| Delete | ✅ | ❌ | ❌ | ❌ | ❌ |
| Verify | ❌ | ✅ | ✅ | ✅ | ✅ |
| Reject | ❌ | ✅ | ✅ | ✅ | ✅ |

---

## 📧 Validation Messages

### Client Side (HTML5)
- Required field validation
- Date picker validation
- File type validation

### Server Side (Laravel)
- All fields required
- All 8 checkboxes must be accepted
- File: PDF, JPG, PNG only
- File size: Max 5MB
- Custom error messages in Indonesian

---

## 🐛 Troubleshooting

### Migration Error
```
Error: Table 'surat_pernyataan' already exists

Solution: Check if table already exists, or run:
php artisan migrate:refresh
```

### File Upload Error
```
Error: Storage path not writable

Solution: 
chmod -R 775 storage/app/public
php artisan storage:link
```

### PDF Generation Error
```
Error: Class 'PDF' not found

Solution:
composer require barryvdh/laravel-dompdf
php artisan vendor:publish
```

---

## ✅ Checklist

- [ ] Migration sudah dijalankan
- [ ] Cache sudah dihapus
- [ ] Routes sudah ter-update
- [ ] Storage folder sudah ter-create
- [ ] Testing sebagai pemohon berjalan
- [ ] Testing sebagai operator berjalan
- [ ] PDF generation berjalan normal
- [ ] File upload berjalan normal

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Cek file **SURAT_PERNYATAAN_DOCUMENTATION.md** untuk detail lengkap
2. Cek logs di `storage/logs/laravel.log`
3. Jalankan `php artisan tinker` untuk debug database

---

**Last Updated:** 2 Februari 2026  
**Version:** 1.0  
**Status:** ✅ Ready to Deploy
