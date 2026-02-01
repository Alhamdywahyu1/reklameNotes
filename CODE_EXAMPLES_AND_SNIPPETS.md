# 💻 CODE EXAMPLES & SNIPPETS REFERENCE

Real-world code snippets untuk copy-paste reference

---

## 🔧 PrintController Examples

### Example 1: Complete printSurat Method
```php
public function printSurat(PermohonanReklame $permohonan): View
{
    // Only operator and admin can print surat
    if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
        abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
    }

    // Only printable if final approval
    if (!$permohonan->isPrintable()) {
        abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
    }

    $approvals = $permohonan->approvalWorkflows()->get();
    $finalApproval = $approvals->where('status_approval', 'Disetujui Kepala Bidang')->first();

    return view('print.surat', compact('permohonan', 'approvals', 'finalApproval'));
}
```

### Example 2: Complete trackPrintSurat Method
```php
public function trackPrintSurat(PermohonanReklame $permohonan): Response
{
    // Only operator and admin can print surat
    if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
        abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
    }

    // Only printable if final approval
    if (!$permohonan->isPrintable()) {
        abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
    }

    // Dispatch event to send notification and email
    SuratDiprintOlehOperator::dispatch($permohonan, auth()->id());

    return response()->json(['message' => 'Surat berhasil dicetak. Notifikasi telah dikirim ke pemohon.']);
}
```

---

## 📧 Listener Examples

### Example 1: Complete Listener Handle
```php
public function handle(SuratDiprintOlehOperator $event): void
{
    $permohonan = $event->permohonan;
    $operator = auth()->user();

    // 1. Create notification for pemohon
    Notification::create([
        'user_id' => $permohonan->user_id,
        'type' => 'SURAT_DIPRINT',
        'title' => 'Surat Persetujuan Siap',
        'message' => "Surat persetujuan reklame Anda ({$permohonan->nomor_registrasi}) telah disiapkan oleh {$operator->name} dan siap untuk diambil.",
        'permohonan_id' => $permohonan->id,
    ]);

    // 2. Send email to pemohon
    Mail::to($permohonan->user->email)->send(
        new SuratDiprintMail($permohonan, $operator->name)
    );

    // 3. Log activity
    \App\Models\ActivityLog::create([
        'user_id' => $operator->id,
        'action' => 'PRINT_SURAT',
        'model_type' => 'PermohonanReklame',
        'model_id' => $permohonan->id,
        'description' => "Mencetak surat persetujuan {$permohonan->nomor_registrasi}",
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
    ]);
}
```

---

## 📧 Mail & Template Examples

### Example 1: Mail Class
```php
class SuratDiprintMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PermohonanReklame $permohonan,
        public string $operatorName
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Surat Persetujuan Reklame Anda Telah Siap - {$this->permohonan->nomor_registrasi}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.surat_diprint',
            with: [
                'permohonan' => $this->permohonan,
                'operatorName' => $this->operatorName,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
```

### Example 2: Email Template
```blade
@component('mail::message')
# Surat Persetujuan Reklame Anda Siap

Halo {{ $permohonan->nama_pemohon }},

Kami dengan gembira memberi tahu bahwa surat persetujuan reklame Anda telah disiapkan oleh **{{ $operatorName }}**.

## Detail Permohonan

- **Nomor Registrasi**: {{ $permohonan->nomor_registrasi }}
- **Jenis Reklame**: {{ $permohonan->jenis_reklame }}
- **Ukuran**: {{ $permohonan->ukuran_reklame }}
- **Jumlah**: {{ $permohonan->jumlah_reklame }} Unit
- **Lokasi**: {{ $permohonan->lokasi_pemasangan }}

## Apa Selanjutnya?

Surat persetujuan Anda siap untuk diambil di kantor kami. Silakan hubungi operator untuk pengambilan surat dengan membawa dokumen identitas Anda.

---

Dengan hormat,  
**Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu**  
Kabupaten Bangkalan

Jl. Kartini No.4, Rw. 03, Keraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119  
Telp. (031) 3095020

@endcomponent
```

---

## 🎨 Frontend Examples

### Example 1: printAndTrack JavaScript
```javascript
function printAndTrack() {
    // 1. Show print dialog
    window.print();
    
    // 2. After 1 second, send tracking request
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
            // 3. Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-4';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Berhasil!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            // Insert into page
            const existingAlert = document.querySelector('.alert-success');
            if (existingAlert) {
                existingAlert.parentNode.insertBefore(alertDiv, existingAlert);
            } else {
                document.body.appendChild(alertDiv);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengirim notifikasi. Silakan hubungi administrator.');
        });
    }, 1000);
}
```

### Example 2: Notification Badge Blade
```blade
<div class="card mb-3 border-left-{{ $notification->type === 'PENGAJUAN_BARU' ? 'warning' : ($notification->type === 'PERMOHONAN_DITOLAK' ? 'danger' : ($notification->type === 'SURAT_DIPRINT' ? 'info' : 'success')) }}" 
     style="border-left: 4px solid {{ $notification->type === 'PENGAJUAN_BARU' ? '#ffc107' : ($notification->type === 'PERMOHONAN_DITOLAK' ? '#dc3545' : ($notification->type === 'SURAT_DIPRINT' ? '#0d6efd' : '#28a745')) }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center mb-2">
                    @if ($notification->type === 'PENGAJUAN_BARU')
                        <span class="badge bg-warning me-2">
                            <i class="bi bi-plus-circle"></i> Pengajuan Baru
                        </span>
                    @elseif ($notification->type === 'PERMOHONAN_DITOLAK')
                        <span class="badge bg-danger me-2">
                            <i class="bi bi-x-circle"></i> Ditolak
                        </span>
                    @elseif ($notification->type === 'SURAT_DIPRINT')
                        <span class="badge bg-info me-2">
                            <i class="bi bi-printer"></i> Surat Siap
                        </span>
                    @else
                        <span class="badge bg-success me-2">
                            <i class="bi bi-check-circle"></i> Status Berubah
                        </span>
                    @endif
                    @if ($notification->isUnread())
                        <span class="badge bg-primary">Baru</span>
                    @endif
                </div>
                <h6 class="card-title mb-1">{{ $notification->title }}</h6>
                <p class="card-text text-muted mb-2">{{ $notification->message }}</p>
                <small class="text-muted d-block mb-3">
                    <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                </small>
                @if ($notification->permohonan_id)
                    <a href="{{ route('permohonan.show', $notification->permohonan) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-arrow-right"></i> Lihat Permohonan
                    </a>
                @endif
            </div>
            <div class="dropdown ms-3">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    @if ($notification->isUnread())
                        <li>
                            <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="bi bi-check"></i> Tandai Terbaca
                                </button>
                            </form>
                        </li>
                    @endif
                    <li>
                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
```

---

## 🔐 Route Examples

### Example 1: Protected Routes
```php
// In routes/web.php
Route::middleware('role:operator,admin')->group(function () {
    Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
    Route::post('print/{permohonan}/track-surat', [PrintController::class, 'trackPrintSurat'])->name('print.track-surat');
});
```

---

## 🧪 Test Examples

### Example 1: Access Control Test
```php
public function test_pemohon_cannot_access_print_surat()
{
    $pemohon = User::factory()->pemohon()->create();
    $permohonan = PermohonanReklame::factory()->approved()->create();
    
    $response = $this->actingAs($pemohon)
                     ->get(route('print.surat', $permohonan));
    
    $response->assertStatus(403);
    $response->assertSee('Hanya Operator');
}

public function test_operator_can_access_print_surat()
{
    $operator = User::factory()->operator()->create();
    $permohonan = PermohonanReklame::factory()->approved()->create();
    
    $response = $this->actingAs($operator)
                     ->get(route('print.surat', $permohonan));
    
    $response->assertStatus(200);
}
```

### Example 2: Notification Test
```php
public function test_notification_created_when_surat_printed()
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

### Example 3: Email Test
```php
public function test_email_sent_when_surat_printed()
{
    Mail::fake();
    
    $operator = User::factory()->operator()->create();
    $permohonan = PermohonanReklame::factory()->approved()->create();
    
    $this->actingAs($operator)
         ->post(route('print.track-surat', $permohonan));
    
    Mail::assertSent(SuratDiprintMail::class, function ($mail) use ($permohonan) {
        return $mail->hasTo($permohonan->user->email);
    });
}
```

---

## 📊 Database Query Examples

### Example 1: Get Recent Print Events
```php
// Get recent print events
$prints = ActivityLog::where('action', 'PRINT_SURAT')
    ->latest()
    ->limit(10)
    ->get();

foreach ($prints as $print) {
    echo $print->user->name . " printed " . $print->description . " at " . $print->created_at;
}
```

### Example 2: Get Unread Notifications for User
```php
// Get unread notifications
$unread = Notification::where('user_id', auth()->id())
    ->whereNull('read_at')
    ->latest()
    ->get();

$count = $unread->count();
echo "You have $count unread notifications";
```

### Example 3: Get Print History for Permohonan
```php
// Get print history
$history = ActivityLog::where('action', 'PRINT_SURAT')
    ->where('model_id', $permohonan->id)
    ->latest()
    ->get();

foreach ($history as $entry) {
    echo "Printed by: " . $entry->user->name . " at " . $entry->created_at;
}
```

---

## 🔍 Debugging Examples

### Example 1: Debug Event Dispatch
```php
// In controller or test
event(new SuratDiprintOlehOperator($permohonan, auth()->id()));
// Then check: storage/logs/laravel.log for event details
```

### Example 2: Debug Listener
```php
// In listener handle method
\Log::info('Listener triggered for print surat', [
    'permohonan_id' => $event->permohonan->id,
    'operator_id' => $event->operatorId,
]);
```

### Example 3: Debug Mail
```php
// Test mail sending
Mail::to('test@example.com')->send(new SuratDiprintMail($permohonan, 'Test Operator'));

// Check logs
tail -f storage/logs/laravel.log | grep -i mail
```

---

## 🛠️ Troubleshooting Code

### Check if Listener Registered
```php
php artisan tinker

$listeners = app('events')->listeners;
$suratDiprintListeners = collect($listeners)
    ->filter(function($items, $event) {
        return str_contains($event, 'SuratDiprint');
    });

$suratDiprintListeners->dump();
```

### Check Notification Queue
```php
php artisan tinker

// If using queue
DB::table('jobs')->get();

// Or check failed jobs
DB::table('failed_jobs')->get();
```

### Verify Routes
```php
php artisan route:list | grep track-surat
php artisan route:list | grep print
```

---

**Copy-paste these snippets for quick reference!** 💻

