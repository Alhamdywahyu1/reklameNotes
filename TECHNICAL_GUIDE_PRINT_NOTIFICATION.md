# Panduan Teknis - Print Surat & Notifikasi

## A. Access Control - Pembatasan Akses

### Sebelum Implementasi
```
Halaman Print Surat: Accessible ke Operator, Admin, DAN Pemohon
❌ Masalah: Pemohon bisa print sendiri tanpa oversight
```

### Setelah Implementasi
```
Halaman Print Surat: HANYA Accessible ke Operator & Admin
✅ Pemohon: Akses ditolak (403 Forbidden)
✅ Operator/Admin: Dapat mengakses dan print
```

**Perubahan di Controller:**
```php
// BEFORE (in printSurat method)
if ($permohonan->user_id !== auth()->id()) {
    if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
        abort(403);
    }
}

// AFTER (in printSurat method)
if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
    abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
}
```

---

## B. Event-Driven Architecture

### Flow Diagram
```
[Operator Print Button]
           ↓
[printAndTrack() JS Function]
           ↓
[window.print() - Browser Dialog]
           ↓
[setTimeout - 1 second delay]
           ↓
[POST /print/{id}/track-surat]
           ↓
[PrintController::trackPrintSurat()]
           ↓
[SuratDiprintOlehOperator::dispatch()]
           ↓
[Event Listener Process]
  ├─ Create Notification Record
  ├─ Send Email via SuratDiprintMail
  └─ Log Activity
           ↓
[Pemohon Notified]
```

### Notification Data Structure
```php
Notification {
  user_id: [pemohon_id],           // Penerima notifikasi
  type: 'SURAT_DIPRINT',           // Tipe notifikasi
  title: 'Surat Persetujuan Siap', // Judul
  message: 'Surat persetujuan... siap untuk diambil',
  permohonan_id: [id],             // Link ke permohonan
  read_at: null,                   // Belum dibaca
  created_at: now()
}
```

---

## C. Email Notification Template

### Email yang Dikirim ke Pemohon
```
TO: pemohon@email.com
SUBJECT: Surat Persetujuan Reklame Anda Telah Siap - [NOMOR_REGISTRASI]

BODY:
- Greeting: Halo [nama pemohon]
- Main message: Surat persetujuan sudah disiapkan oleh [operator_name]
- Details table dengan info permohonan:
  * Nomor Registrasi
  * Jenis Reklame
  * Ukuran
  * Jumlah
  * Lokasi
- Next steps: Pengambilan surat di kantor
- Contact info: Alamat dan telepon kantor
```

---

## D. Frontend Interaction (JavaScript)

### printAndTrack() Function
```javascript
function printAndTrack() {
    // 1. Show print dialog
    window.print();
    
    // 2. After 1 second (let user finish printing)
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
            // 3. Show success message to operator
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-4';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Berhasil!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            // Insert at top
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim notifikasi...');
        });
    }, 1000);
}
```

**Mengapa setTimeout(1000)?**
- Memberikan waktu operator untuk confirm print dialog
- Mencegah race condition
- UX: User bisa lihat print dialog sempurna

---

## E. Route Configuration

### Before
```php
// Print surat - accessible to both pemohon and operator
Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
```

### After
```php
// Print surat - accessible to operator and admin only
Route::middleware('role:operator,admin')->group(function () {
    Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
    Route::post('print/{permohonan}/track-surat', [PrintController::class, 'trackPrintSurat'])->name('print.track-surat');
});
```

**Protected by:**
- Role middleware: Only operator & admin
- Authorization check in controller: Additional validation
- CSRF token: Protection against CSRF attacks

---

## F. Notification Display (UI)

### Badge Type: SURAT_DIPRINT
```
Display in notification center with:
├─ Icon: printer icon (bi-printer)
├─ Badge Color: Blue (#0d6efd)
├─ Left Border: Blue (#0d6efd)
├─ Title: "Surat Persetujuan Siap"
├─ Message: Full message from database
├─ Actions:
│  ├─ "Lihat Permohonan" button
│  └─ More actions (Mark read, Delete)
└─ Time: Relative format (e.g., "5 minutes ago")
```

### Notification Types Supported
| Type | Color | Icon | Usage |
|------|-------|------|-------|
| PENGAJUAN_BARU | Warning (Yellow) | plus-circle | New application |
| PERMOHONAN_DITOLAK | Danger (Red) | x-circle | Rejected |
| SURAT_DIPRINT | Info (Blue) | printer | **Surat ready (NEW)** |
| (default) | Success (Green) | check-circle | Status changed |

---

## G. Database Considerations

### Notification Table (existing)
```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,                    -- Pemohon
    type VARCHAR(50),                        -- 'SURAT_DIPRINT'
    title VARCHAR(255),
    message TEXT,
    permohonan_id INT,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (permohonan_id) REFERENCES permohonan_reklame(id),
    INDEX (user_id, read_at)
);
```

### Activity Log Entry (audit trail)
```sql
-- Created by listener in CreateNotificationSuratDiprint
INSERT INTO activity_logs VALUES (
    null,
    [operator_id],
    'PRINT_SURAT',
    'PermohonanReklame',
    [permohonan_id],
    'Mencetak surat persetujuan [nomor_registrasi]',
    '[ip_address]',
    '[user_agent]',
    now(),
    now()
);
```

---

## H. Security Considerations

### 1. Authorization Checks
```
Multiple layers:
- Route middleware (role:operator,admin)
- Controller authorization (hasAnyRole check)
- Notification ownership (user_id validation)
```

### 2. CSRF Protection
```javascript
headers: {
    'X-CSRF-TOKEN': '{{ csrf_token() }}',  // Laravel CSRF token
    'Content-Type': 'application/json',
}
```

### 3. Data Validation
```php
// In trackPrintSurat()
- Check if user is operator/admin
- Check if permohonan is printable (final approval)
- Only then dispatch event
```

### 4. Activity Logging
```php
// For audit trail
ActivityLog::create([
    'user_id' => $operator->id,
    'action' => 'PRINT_SURAT',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    // ... timestamps, IP, user agent
]);
```

---

## I. Error Handling

### Potential Error Scenarios

#### Scenario 1: Pemohon tries to access print page
```
User: Pemohon (role: pemohon)
Action: Navigate to /print/{id}/surat
Result: 
  ✗ PrintController::printSurat() checks role
  ✗ abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan')
Output: 403 Forbidden error page
```

#### Scenario 2: Notifikasi email fails to send
```
Listener catches in try-catch (optional):
- Log error to logs/laravel.log
- Notification still created in DB
- Operator sees success message (surat printed)
- Can retry email later via admin panel
```

#### Scenario 3: Double print tracking
```
Security: Each POST request to track-surat will:
- Validate operator role
- Validate permohonan status
- Create new notification (ok to have multiple if printed twice)
- Log each action separately
Result: Full audit trail, safe to call multiple times
```

---

## J. Testing Guide

### Unit Test Example (Pseudocode)
```php
public function test_only_operator_can_access_print_surat()
{
    $pemohon = User::factory()->pemohon()->create();
    $operator = User::factory()->operator()->create();
    $permohonan = PermohonanReklame::factory()->approved()->create();
    
    // Test pemohon access
    $response = $this->actingAs($pemohon)
                     ->get(route('print.surat', $permohonan));
    $response->assertStatus(403);
    
    // Test operator access
    $response = $this->actingAs($operator)
                     ->get(route('print.surat', $permohonan));
    $response->assertStatus(200);
}

public function test_print_surat_creates_notification()
{
    $operator = User::factory()->operator()->create();
    $permohonan = PermohonanReklame::factory()->approved()->create();
    
    $this->actingAs($operator)
         ->post(route('print.track-surat', $permohonan));
    
    $this->assertDatabaseHas('notifications', [
        'user_id' => $permohonan->user_id,
        'type' => 'SURAT_DIPRINT',
        'permohonan_id' => $permohonan->id,
    ]);
}
```

---

## K. Configuration Checklist

- [ ] `.env` email configuration (MAIL_DRIVER, MAIL_FROM_ADDRESS)
- [ ] Database migration executed
- [ ] AppServiceProvider event registered
- [ ] Route caching cleared (if using route:cache)
- [ ] Assets compiled/published
- [ ] Email templates accessible
- [ ] Activity log table created
- [ ] Notification permissions set correctly

---

## L. Troubleshooting

### Issue: Email not sending
**Solution:**
1. Check `.env` mail configuration
2. Verify MAIL_FROM_ADDRESS is set
3. Check Laravel logs: `storage/logs/laravel.log`
4. Test with: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('test@example.com'))`

### Issue: Operator sees 403 error
**Solution:**
1. Verify operator role in database
2. Check `users.role_id` points to correct role
3. Verify role `slug` is 'operator'
4. Clear browser cache

### Issue: Notification not appearing
**Solution:**
1. Check notifications table for records
2. Verify event listener registered in AppServiceProvider
3. Check if user_id matches pemohon id
4. Verify read_at is null (not marked as read)

### Issue: Print button not working
**Solution:**
1. Check browser console for JavaScript errors
2. Verify CSRF token present in page
3. Check network tab for POST request response
4. Verify route exists: `php artisan route:list | grep track-surat`

---

**End of Technical Guide**
