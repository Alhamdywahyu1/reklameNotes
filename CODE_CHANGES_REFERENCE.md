# Quick Reference: Security Implementation Code Changes

## 1. Model Changes (PermohonanReklame.php)

### Added Methods:
```php
public function getEditRestrictionReason(): ?string
{
    if ($this->canBeEditedByUser()) {
        return null;
    }

    return match ($this->status) {
        'Diajukan' => 'Permohonan sudah diajukan. Data tidak dapat diubah sampai permohonan ditolak atau selesai.',
        'Diverifikasi Operator' => 'Permohonan sedang diverifikasi operator. Data terkunci untuk perubahan.',
        'Disetujui Kepala Seksi' => 'Permohonan sedang dalam tahap approval. Data terkunci untuk perubahan.',
        'Disetujui Kepala Bidang' => 'Permohonan telah disetujui FINAL. Data tidak dapat diubah.',
        default => 'Permohonan tidak dapat diubah pada status saat ini.',
    };
}
```

---

## 2. Controller Changes (PermohonanReklameController.php)

### Edit Method - BEFORE:
```php
public function edit(PermohonanReklame $permohonan): View
{
    if ($permohonan->user_id !== auth()->id()) {
        abort(403);
    }

    if (!$permohonan->canBeEditedByUser()) {
        abort(403, 'Permohonan tidak dapat diedit pada status ini');
    }

    return view('permohonan.edit', compact('permohonan'));
}
```

### Edit Method - AFTER:
```php
public function edit(PermohonanReklame $permohonan): View
{
    if ($permohonan->user_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
    }

    if (!$permohonan->canBeEditedByUser()) {
        abort(403, $permohonan->getEditRestrictionReason() ?? 'Permohonan tidak dapat diedit pada status ini');
    }

    return view('permohonan.edit', compact('permohonan'));
}
```

### Update Method - BEFORE:
```php
public function update(Request $request, PermohonanReklame $permohonan): RedirectResponse
{
    if ($permohonan->user_id !== auth()->id()) {
        abort(403);
    }

    if (!$permohonan->canBeEditedByUser()) {
        abort(403, 'Permohonan tidak dapat diedit pada status ini');
    }
    // ... update logic
}
```

### Update Method - AFTER:
```php
public function update(Request $request, PermohonanReklame $permohonan): RedirectResponse
{
    if ($permohonan->user_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
    }

    if (!$permohonan->canBeEditedByUser()) {
        return redirect()->route('permohonan.show', $permohonan)
            ->with('error', $permohonan->getEditRestrictionReason() ?? 'Permohonan tidak dapat diedit pada status ini');
    }
    // ... update logic
}
```

---

## 3. Approval Controller Changes (ApprovalController.php)

### APPROVAL_OPERATOR - BEFORE:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_OPERATOR',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Verifikasi operator permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

### APPROVAL_OPERATOR - AFTER:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_OPERATOR',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Verifikasi operator permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'old_values' => ['status' => $oldStatus],
    'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan']],
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

### APPROVAL_KEPALA_SEKSI - BEFORE:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_KEPALA_SEKSI',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Approval Kepala Seksi permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

### APPROVAL_KEPALA_SEKSI - AFTER:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_KEPALA_SEKSI',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Approval Kepala Seksi permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'old_values' => ['status' => $oldStatus],
    'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan']],
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

### APPROVAL_KEPALA_BIDANG - BEFORE:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_KEPALA_BIDANG',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Final approval Kepala Bidang permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

### APPROVAL_KEPALA_BIDANG - AFTER:
```php
ActivityLog::create([
    'user_id' => auth()->id(),
    'action' => 'APPROVAL_KEPALA_BIDANG',
    'model_type' => 'PermohonanReklame',
    'model_id' => $permohonan->id,
    'description' => "Final approval Kepala Bidang permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
    'old_values' => ['status' => $oldStatus],
    'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan'], 'tanggal_berlaku' => $validated['tanggal_berlaku'] ?? null, 'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null],
    'ip_address' => $request->ip(),
    'user_agent' => $request->userAgent(),
    'created_at' => now(),
]);
```

---

## 4. Document Requirement Controller Changes

### Added Import:
```php
use App\Models\ActivityLog;
```

### updateStatus Method - BEFORE:
```php
public function updateStatus(Request $request, DocumentRequirement $requirement): RedirectResponse
{
    if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
        abort(403, 'Hanya staff yang dapat mengakses halaman ini');
    }

    $validated = $request->validate([
        'status' => 'required|in:Belum Lengkap,Lengkap,Ditolak',
        'catatan_penolakan' => 'nullable|string|max:500',
    ]);

    $requirement->update([
        'status' => $validated['status'],
        'catatan_penolakan' => $validated['status'] === 'Ditolak' ? $validated['catatan_penolakan'] : null,
    ]);

    return redirect()->back()
        ->with('success', 'Status persyaratan dokumen berhasil diperbarui');
}
```

### updateStatus Method - AFTER:
```php
public function updateStatus(Request $request, DocumentRequirement $requirement): RedirectResponse
{
    if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
        abort(403, 'Hanya staff yang dapat mengakses halaman ini');
    }

    $validated = $request->validate([
        'status' => 'required|in:Belum Lengkap,Lengkap,Ditolak',
        'catatan_penolakan' => 'nullable|string|max:500',
    ]);

    $oldStatus = $requirement->status;
    
    $requirement->update([
        'status' => $validated['status'],
        'catatan_penolakan' => $validated['status'] === 'Ditolak' ? $validated['catatan_penolakan'] : null,
    ]);

    // Log document status change
    ActivityLog::create([
        'user_id' => auth()->id(),
        'action' => 'DOCUMENT_VERIFICATION',
        'model_type' => 'PersyaratanDokumen',
        'model_id' => $requirement->id,
        'description' => "Verifikasi dokumen '{$requirement->jenis_persyaratan}' untuk permohonan {$requirement->permohonan->nomor_registrasi}",
        'old_values' => ['status' => $oldStatus],
        'new_values' => ['status' => $validated['status'], 'catatan_penolakan' => $validated['catatan_penolakan'] ?? null],
        'ip_address' => $request->ip(),
        'user_agent' => $request->userAgent(),
        'created_at' => now(),
    ]);

    return redirect()->back()
        ->with('success', 'Status persyaratan dokumen berhasil diperbarui');
}
```

---

## 5. View Changes (show.blade.php)

### Added Edit Restriction Warning:
```blade
@if (auth()->user()->hasRole('pemohon') && $permohonan->user_id === auth()->id())
    @if ($permohonan->canBeEditedByUser())
        <div>
            <!-- Edit buttons -->
            <a href="{{ route('permohonan.edit', $permohonan) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <!-- ... submit/revise buttons -->
        </div>
    @else
        <div class="alert alert-warning d-inline-block" role="alert" style="max-width: 400px;">
            <i class="bi bi-lock"></i> <strong>Data Terkunci</strong><br>
            <small>{{ $permohonan->getEditRestrictionReason() }}</small>
        </div>
    @endif
@endif
```

### Added Audit Trail Section:
```blade
<!-- Audit Trail / Activity Log -->
<div class="card mt-3">
    <div class="card-body">
        <h5><i class="bi bi-clock-history"></i> Riwayat Perubahan</h5>
        <hr>
        @php
            $activityLogs = \App\Models\ActivityLog::where('model_type', 'PermohonanReklame')
                ->where('model_id', $permohonan->id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        @endphp
        @if ($activityLogs->count() > 0)
            <div class="timeline-simple">
                @foreach ($activityLogs as $log)
                    <div class="timeline-item mb-3 pb-3" style="border-bottom: 1px solid #eee;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div style="flex: 1;">
                                <p class="small mb-1">
                                    <strong>
                                        @if ($log->action === 'APPROVAL_OPERATOR')
                                            <i class="bi bi-check-circle text-info"></i> Verifikasi Operator
                                        @elseif ($log->action === 'APPROVAL_KEPALA_SEKSI')
                                            <i class="bi bi-check-circle text-info"></i> Approval Kepala Seksi
                                        @elseif ($log->action === 'APPROVAL_KEPALA_BIDANG')
                                            <i class="bi bi-check-circle text-success"></i> Approval Kepala Bidang
                                        @elseif ($log->action === 'UPDATE')
                                            <i class="bi bi-pencil text-warning"></i> Update Data
                                        @elseif ($log->action === 'SUBMIT')
                                            <i class="bi bi-arrow-up text-primary"></i> Pengajuan
                                        @else
                                            <i class="bi bi-activity"></i> {{ $log->action }}
                                        @endif
                                    </strong>
                                </p>
                                <p class="small text-muted mb-1">{{ $log->description }}</p>
                                @if ($log->new_values && isset($log->new_values['keputusan']))
                                    <p class="small mb-1">
                                        <strong>Keputusan:</strong> 
                                        <span class="badge" style="background-color: {{ $log->new_values['keputusan'] === 'Disetujui' ? '#28a745' : '#dc3545' }}">
                                            {{ $log->new_values['keputusan'] }}
                                        </span>
                                    </p>
                                @endif
                                <small class="text-muted d-block">
                                    {{ $log->created_at->format('d M Y H:i') }} | IP: {{ $log->ip_address ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted small">Belum ada riwayat perubahan</p>
        @endif
    </div>
</div>
```

---

## Summary of Changes

| Component | Type | Change |
|-----------|------|--------|
| Model | Add Method | `getEditRestrictionReason()` |
| Controller | Enhance | Better error messages & reason |
| Controller | Enhance | Add old_values/new_values to ActivityLog |
| Controller | Enhance | Document verification logging |
| View | Add | Edit restriction warning |
| View | Add | Audit trail timeline |

**Total Files Modified:** 5  
**New Methods Added:** 2  
**New Documentation Files:** 2  
**Total Lines Added:** ~150 lines of code + ~200 lines of docs
