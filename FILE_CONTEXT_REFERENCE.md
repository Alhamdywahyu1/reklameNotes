# 🔍 COMPLETE FILE CONTEXT REFERENCE

Quick reference untuk semua file yang dibuat/dimodifikasi dengan konteks lengkap.

---

## 📁 New Files Created

### 1. app/Events/SuratDiprintOlehOperator.php

**Purpose:** Event yang di-dispatch ketika operator mencetak surat

**What's inside:**
- Event class dengan properties: `permohonan`, `operatorId`
- Implements Laravel Event interface
- Used untuk trigger listener

**Key methods:**
- `__construct()` - Initialize event dengan permohonan & operator ID
- `broadcastOn()` - Define broadcast channels (untuk websocket)

**How it's used:**
```php
SuratDiprintOlehOperator::dispatch($permohonan, auth()->id());
```

**Related files:**
- Dispatched in: `PrintController::trackPrintSurat()`
- Listened by: `CreateNotificationSuratDiprint`

---

### 2. app/Listeners/CreateNotificationSuratDiprint.php

**Purpose:** Handle event dan buat notifikasi + send email

**What's inside:**
- Listener class yang implement `ShouldQueue`
- Handle method yang process event

**Key methods:**
- `handle(SuratDiprintOlehOperator $event)` - Main logic

**What it does:**
1. Create Notification record
2. Send email via SuratDiprintMail
3. Log activity untuk audit trail

**Key operations:**
```php
// 1. Create notification
Notification::create([...]);

// 2. Send email
Mail::to($permohonan->user->email)->send(
    new SuratDiprintMail(...)
);

// 3. Log activity
ActivityLog::create([...]);
```

**Related files:**
- Listens to: `SuratDiprintOlehOperator`
- Registered in: `AppServiceProvider`

---

### 3. app/Mail/SuratDiprintMail.php

**Purpose:** Mail class untuk email yang dikirim ke pemohon

**What's inside:**
- Mail class extending Mailable
- Properties: `permohonan`, `operatorName`

**Key methods:**
- `envelope()` - Set subject line
- `content()` - Set view & data
- `attachments()` - Define attachments (none in this case)

**Key data passed:**
```php
// Email properties
$permohonan   // Full permohonan object
$operatorName // Name of operator who printed
```

**Email content:**
- Subject: "Surat Persetujuan Reklame Anda Telah Siap - [NOMOR]"
- View: `emails.surat_diprint`
- Recipient: `$permohonan->user->email`

**Related files:**
- Sent from: `CreateNotificationSuratDiprint`
- Template: `resources/views/emails/surat_diprint.blade.php`

---

### 4. resources/views/emails/surat_diprint.blade.php

**Purpose:** Email template untuk surat print notification

**What's inside:**
- Mail component template
- Professional HTML email layout
- Permohonan details

**Key sections:**
1. Header dengan greeting
2. Main message
3. Permohonan details table
4. Next steps instructions
5. Contact information
6. Professional signature

**Variables available:**
- `$permohonan` - Full object dengan semua details
- `$operatorName` - Nama operator yang print

**Email styling:**
- Using Laravel Mail components
- Responsive design
- Professional formatting

**Related files:**
- Rendered by: `SuratDiprintMail`
- Sent by: `CreateNotificationSuratDiprint`

---

## 📝 Modified Files

### 1. app/Http/Controllers/PrintController.php

**Changes made:**

#### Import added:
```php
use App\Events\SuratDiprintOlehOperator;
```

#### Method modified: `printSurat()`
**Before:**
- Allowed pemohon dan operator

**After:**
- Only operator dan admin
- Stricter authorization check
- Return 403 for others

```php
// NEW CODE:
if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
    abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
}
```

#### Method added: `trackPrintSurat()`
**Purpose:** Handle tracking print action

**Parameters:**
- `PermohonanReklame $permohonan` - The permohonan being printed

**Logic:**
1. Check authorization (operator/admin only)
2. Check if printable (isPrintable() method)
3. Dispatch event
4. Return JSON response

**Returns:**
```json
{
  "message": "Surat berhasil dicetak. Notifikasi telah dikirim ke pemohon."
}
```

**Location in file:** End of class

---

### 2. app/Providers/AppServiceProvider.php

**Changes made:**

#### Imports added:
```php
use App\Events\SuratDiprintOlehOperator;
use App\Listeners\CreateNotificationSuratDiprint;
use Illuminate\Support\Facades\Event;
```

#### In `boot()` method added:
```php
Event::listen(
    SuratDiprintOlehOperator::class,
    CreateNotificationSuratDiprint::class,
);
```

**Purpose:** Register event listener

**What it does:**
- Tells Laravel: when SuratDiprintOlehOperator event fires, run CreateNotificationSuratDiprint listener
- Enables automatic notification when print surat is triggered

**Location:** Inside `boot()` method

---

### 3. resources/views/print/surat.blade.php

**Changes made:**

#### Button changed:
**Before:**
```html
<button onclick="window.print()">Cetak Surat</button>
```

**After:**
```html
<button onclick="printAndTrack()">Cetak Surat</button>
```

#### JavaScript function added:
```javascript
function printAndTrack() {
    // 1. Show print dialog
    window.print();
    
    // 2. Wait 1 second for print dialog to close
    setTimeout(() => {
        // 3. Send tracking request
        fetch('{{ route("print.track-surat", $permohonan) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            // 4. Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-4';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Berhasil!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            // Insert into DOM
        })
        .catch(error => {
            alert('Terjadi kesalahan saat mengirim notifikasi...');
        });
    }, 1000);
}
```

**Purpose:**
- Track print action
- Send backend notification
- Show feedback to operator

**Location:** End of file before `@endsection`

---

### 4. resources/views/notifications/index.blade.php

**Changes made:**

#### Badge styling updated:
**Before:**
```blade
$notification->type === 'PENGAJUAN_BARU' ? 'warning' : 
($notification->type === 'PERMOHONAN_DITOLAK' ? 'danger' : 'success')
```

**After:**
```blade
$notification->type === 'PENGAJUAN_BARU' ? 'warning' : 
($notification->type === 'PERMOHONAN_DITOLAK' ? 'danger' : 
($notification->type === 'SURAT_DIPRINT' ? 'info' : 'success'))
```

#### Border color styling updated:
**Before:**
```blade
'#ffc107' : ($notification->type === 'PERMOHONAN_DITOLAK' ? '#dc3545' : '#28a745')
```

**After:**
```blade
'#ffc107' : ($notification->type === 'PERMOHONAN_DITOLAK' ? '#dc3545' : 
($notification->type === 'SURAT_DIPRINT' ? '#0d6efd' : '#28a745'))
```

#### Badge HTML added:
**New condition for SURAT_DIPRINT:**
```html
@elseif ($notification->type === 'SURAT_DIPRINT')
    <span class="badge bg-info me-2">
        <i class="bi bi-printer"></i> Surat Siap
    </span>
```

#### CSS added:
```css
.border-left-info {
    border-left: 4px solid #0d6efd !important;
}
```

**Purpose:**
- Display new notification type
- Show with blue color & printer icon
- Visually distinct from other types

**Location:** Lines with badge styling and CSS section

---

### 5. routes/web.php

**Changes made:**

#### Print surat route changed:

**Before:**
```php
Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
```

**After:**
```php
Route::middleware('role:operator,admin')->group(function () {
    Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
    Route::post('print/{permohonan}/track-surat', [PrintController::class, 'trackPrintSurat'])->name('print.track-surat');
});
```

**Changes:**
1. Wrapped in middleware group with `role:operator,admin`
2. Added new POST route untuk tracking

**Purpose:**
- Restrict access to operator/admin only
- Add endpoint untuk tracking print

**Location:** Around line 93-96 in routes/web.php

---

## 🔄 Data Flow Between Files

### Flow Diagram:

```
1. User clicks print button
   └─→ surat.blade.php (printAndTrack function)

2. printAndTrack() called
   ├─→ window.print() shown
   └─→ setTimeout(() => fetch(...))

3. POST /print/{id}/track-surat
   └─→ PrintController::trackPrintSurat()

4. Controller dispatch event
   └─→ SuratDiprintOlehOperator::dispatch()

5. Event fired
   └─→ Listener registered in AppServiceProvider

6. CreateNotificationSuratDiprint listener
   ├─→ Create notification record
   ├─→ Send email
   ├─→ Log activity
   └─→ Return

7. Email sent
   └─→ SuratDiprintMail uses surat_diprint.blade.php

8. Notification visible
   └─→ notifications/index.blade.php displays it
```

---

## 📊 File Dependencies

```
PrintController.php
├─ Imports: SuratDiprintOlehOperator
├─ Uses: PermohonanReklame model
└─ Dispatches: SuratDiprintOlehOperator event

SuratDiprintOlehOperator.php
└─ Listened by: CreateNotificationSuratDiprint

CreateNotificationSuratDiprint.php
├─ Listens to: SuratDiprintOlehOperator
├─ Uses: Notification model
├─ Uses: ActivityLog model
└─ Sends: SuratDiprintMail

SuratDiprintMail.php
└─ Renders: surat_diprint.blade.php

AppServiceProvider.php
├─ Registers: SuratDiprintOlehOperator listener
└─ Enables: CreateNotificationSuratDiprint

routes/web.php
├─ Protects: print.surat route
└─ Creates: print.track-surat route

surat.blade.php
├─ Contains: printAndTrack() JS function
└─ Calls: POST /print/{id}/track-surat

notifications/index.blade.php
└─ Displays: SURAT_DIPRINT notifications
```

---

## 🎯 Key Takeaways

### File Relationships:
- PrintController triggers everything
- Event is central hub
- Listener handles side effects
- Mail sends notification
- Blade renders UI

### Data Flow:
1. User action → Controller
2. Controller → Event dispatch
3. Event → Listener
4. Listener → Multiple actions (DB, Email, Log)
5. Result → UI update

### Important Connections:
- **Event ↔ Listener**: Registered in AppServiceProvider
- **Mail ↔ Template**: Referenced in Mail class
- **Route ↔ Controller**: Middleware protects route
- **JavaScript ↔ Route**: fetch() calls backend route
- **Notification ↔ View**: Blade template displays records

---

**End of File Context Reference**

Gunakan dokumen ini untuk quick reference saat developing atau debugging!

