# Implementasi Fitur Print Surat Persetujuan dan Notifikasi Pemohon

## Ringkasan Perubahan

Telah diimplementasikan fitur lengkap untuk:
1. **Batasi akses cetak surat** - Hanya operator yang dapat mencetak surat persetujuan
2. **Tracking print surat** - Ketika surat dicetak, sistem akan mengirim notifikasi email ke pemohon
3. **Sistem pesan/notifikasi** - Pemohon dapat melihat pesan di inbox dengan fitur messaging

---

## Detail Perubahan File

### 1. Controller: PrintController
**File:** `app/Http/Controllers/PrintController.php`

**Perubahan:**
- Import `SuratDiprintOlehOperator` event
- Update method `printSurat()` - Batasi akses hanya ke operator dan admin
  - Removed: Pemohon tidak lagi bisa mengakses halaman print surat
  - Hanya operator dan admin yang dapat mengakses
  
- Tambah method `trackPrintSurat()` - Untuk tracking ketika surat dicetak
  - Menerima POST request
  - Dispatch event `SuratDiprintOlehOperator`
  - Return JSON response dengan pesan sukses

**Kode relevan:**
```php
public function printSurat(PermohonanReklame $permohonan): View
{
    // Only operator and admin can print surat
    if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
        abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
    }
    // ... rest of code
}

public function trackPrintSurat(PermohonanReklame $permohonan): Response
{
    // Validate access & dispatch event
    SuratDiprintOlehOperator::dispatch($permohonan, auth()->id());
    return response()->json(['message' => 'Surat berhasil dicetak...']);
}
```

---

### 2. Event: SuratDiprintOlehOperator
**File:** `app/Events/SuratDiprintOlehOperator.php`

**Tujuan:** Event yang di-dispatch ketika operator mencetak surat

**Properti:**
- `permohonan` - Object PermohonanReklame
- `operatorId` - ID operator yang mencetak

---

### 3. Listener: CreateNotificationSuratDiprint
**File:** `app/Listeners/CreateNotificationSuratDiprint.php`

**Tujuan:** Handle event dan buat notifikasi

**Aksi yang dilakukan:**
1. Buat record Notification di database:
   - Type: `SURAT_DIPRINT`
   - Title: "Surat Persetujuan Siap"
   - Message: Info bahwa surat sudah siap
   - Recipient: Pemohon (user yang membuat permohonan)

2. Kirim email ke pemohon menggunakan `SuratDiprintMail`

3. Log activity untuk audit trail

---

### 4. Mail: SuratDiprintMail
**File:** `app/Mail/SuratDiprintMail.php`

**Tujuan:** Email template untuk notifikasi print surat

**Konten email:**
- Subject: "Surat Persetujuan Reklame Anda Telah Siap - [Nomor Registrasi]"
- Detail permohonan (nomor registrasi, jenis, ukuran, dll)
- Instruksi untuk pengambilan surat
- Info kontak kantor

---

### 5. Email View: resources/views/emails/surat_diprint.blade.php
**Tujuan:** Template email yang dikirim ke pemohon

---

### 6. View: resources/views/print/surat.blade.php

**Perubahan:**
- Update tombol "Cetak Surat" - Sebelumnya langsung print, sekarang trigger fungsi `printAndTrack()`
- Tambah script JavaScript `printAndTrack()`:
  - Trigger window.print()
  - Send POST request ke `print.track-surat` route
  - Show success message
  - Trigger email notifikasi ke pemohon

**Kode JavaScript:**
```javascript
function printAndTrack() {
    window.print();
    setTimeout(() => {
        fetch('{{ route("print.track-surat", $permohonan) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-4';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Berhasil!</strong> ${data.message}
            `;
            // Insert alert
        });
    }, 1000);
}
```

---

### 7. Notification View: resources/views/notifications/index.blade.php

**Perubahan:**
- Tambah support untuk tipe notifikasi `SURAT_DIPRINT`
- Badge biru dengan icon printer
- Update color logic untuk menampilkan warna sesuai tipe
- Add `.border-left-info` CSS class

**Tipe notifikasi yang didukung:**
- `PENGAJUAN_BARU` - Badge warning (kuning)
- `PERMOHONAN_DITOLAK` - Badge danger (merah)
- `SURAT_DIPRINT` - Badge info (biru) - **NEW**
- Default - Badge success (hijau)

---

### 8. Routes: routes/web.php

**Perubahan:**
- Ubah route `print.surat` untuk membatasi hanya operator dan admin
- Tambah route baru `print.track-surat` untuk tracking print

**Sebelum:**
```php
Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
```

**Sesudah:**
```php
Route::middleware('role:operator,admin')->group(function () {
    Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
    Route::post('print/{permohonan}/track-surat', [PrintController::class, 'trackPrintSurat'])->name('print.track-surat');
});
```

---

### 9. Provider: app/Providers/AppServiceProvider.php

**Perubahan:**
- Register event listener untuk `SuratDiprintOlehOperator`
- Event listener akan otomatis handle ketika event di-dispatch

**Kode:**
```php
public function boot(): void
{
    Event::listen(
        SuratDiprintOlehOperator::class,
        CreateNotificationSuratDiprint::class,
    );
}
```

---

## Alur Kerja Sistem

### Skenario: Operator Mencetak Surat

1. **Operator membuka halaman surat persetujuan**
   - URL: `/print/{permohonan}/surat`
   - Hanya operator/admin yang dapat mengakses
   - Pemohon akan mendapat error 403

2. **Operator klik tombol "Cetak Surat"**
   - Function `printAndTrack()` dipanggil
   - Browser print dialog muncul

3. **Setelah selesai print**
   - Script mengirim POST request ke `/print/{permohonan}/track-surat`
   - Event `SuratDiprintOlehOperator` di-dispatch
   - Listener memproses event:
     - Buat Notification record
     - Kirim email ke pemohon
     - Log activity

4. **Pemohon menerima notifikasi**
   - Email masuk ke inbox
   - Notification muncul di sistem inbox pemohon
   - Pemohon dapat melihat di halaman `/notifications`

5. **Pemohon melihat pesan**
   - Buka menu Notifikasi/Pesan
   - Lihat notifikasi "Surat Persetujuan Siap"
   - Dapat klik "Lihat Permohonan" untuk detail
   - Dapat mark as read atau delete

---

## Database Schema

**Notification Table (sudah ada):**
```
- id
- user_id (pemohon)
- type (SURAT_DIPRINT)
- title (Surat Persetujuan Siap)
- message (detail message)
- permohonan_id
- read_at
- created_at
- updated_at
```

---

## Fitur Tambahan

### Notification Center Features:
- ✅ View semua notifikasi dengan pagination
- ✅ Mark sebagai read/unread
- ✅ Mark semua sebagai read
- ✅ Delete notifikasi
- ✅ Lihat detail permohonan dari notifikasi
- ✅ Filter berdasarkan tipe notifikasi
- ✅ Waktu notifikasi (relative time format)

### Security:
- ✅ Authorization check - Hanya operator yang bisa print surat
- ✅ User ownership check - Notifikasi hanya untuk pemohon yang sesuai
- ✅ CSRF protection pada form submission
- ✅ Activity logging untuk audit trail

---

## Testing Checklist

- [ ] Login sebagai Operator
- [ ] Buka halaman print surat persetujuan
- [ ] Klik tombol "Cetak Surat"
- [ ] Verify print dialog muncul
- [ ] Verify success message muncul
- [ ] Login sebagai Pemohon
- [ ] Check email untuk notifikasi
- [ ] Buka halaman Notifikasi
- [ ] Verify notifikasi "Surat Persetujuan Siap" muncul
- [ ] Klik "Lihat Permohonan" untuk verify link kerja
- [ ] Test mark as read
- [ ] Test delete notifikasi
- [ ] Verify pemohon tidak bisa akses `/print/{permohonan}/surat` (403 error)

---

## Catatan Implementasi

1. **Email Configuration**: Pastikan `.env` sudah dikonfigurasi untuk email:
   - `MAIL_DRIVER`
   - `MAIL_FROM_ADDRESS`
   - `MAIL_FROM_NAME`

2. **Queue Jobs**: Listener menggunakan `ShouldQueue` jika diperlukan async processing

3. **Timezone**: Pastikan timezone sudah benar di `config/app.php`

4. **Asset Publishing**: Pastikan assets sudah di-compile (jika ada CSS baru)

---

## Catatan untuk User

### Bagi Operator:
- Akses ke halaman surat hanya untuk operator
- Ketika cetak surat, pemohon otomatis dapat notifikasi
- Jangan khawatir tentang mengirim notifikasi manual

### Bagi Pemohon:
- Surat persetujuan hanya dapat dicetak oleh operator
- Akan menerima email dan notifikasi ketika surat sudah siap
- Dapat melihat semua notifikasi di halaman Pesan/Notifikasi
- Gunakan halaman notifikasi untuk tracking status permohonan

---

## File yang Dibuat/Diubah

### Dibuat:
1. `app/Events/SuratDiprintOlehOperator.php`
2. `app/Listeners/CreateNotificationSuratDiprint.php`
3. `app/Mail/SuratDiprintMail.php`
4. `resources/views/emails/surat_diprint.blade.php`

### Diubah:
1. `app/Http/Controllers/PrintController.php`
2. `app/Providers/AppServiceProvider.php`
3. `resources/views/print/surat.blade.php`
4. `resources/views/notifications/index.blade.php`
5. `routes/web.php`

---

**Total Files Modified:** 9 files
**Total New Files:** 4 files
