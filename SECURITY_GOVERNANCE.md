# Penguatan Keamanan & Tata Kelola Sistem
## Dokumentasi Fitur Keamanan dan Audit Trail

Tanggal Update: 26 Januari 2026  
Version: 2.0

---

## 1. Pembatasan Edit Data (Data Immutability)

### Prinsip Dasar
Setelah permohonan **diajukan** (status "Diajukan"), pemohon **TIDAK DAPAT LAGI** mengedit data permohonan apapun sampai permohonan tersebut ditolak.

### Status yang Memungkinkan Edit
Pemohon hanya dapat mengedit permohonan dengan status:
- ✅ **Draft** - Permohonan masih dalam mode draft (belum diajukan)
- ✅ **Ditolak Operator** - Permohonan ditolak, pemohon bisa revisi
- ✅ **Ditolak Kepala Seksi** - Permohonan ditolak, pemohon bisa revisi
- ✅ **Ditolak Kepala Bidang** - Permohonan ditolak, pemohon bisa revisi

### Status yang MELARANG Edit
Pemohon **TIDAK DAPAT** mengedit permohonan dengan status:
- ❌ **Diajukan** - Permohonan dalam antrian verifikasi operator
- ❌ **Diverifikasi Operator** - Dalam proses verifikasi dokumen operator
- ❌ **Disetujui Kepala Seksi** - Dalam proses approval kepala seksi
- ❌ **Disetujui Kepala Bidang** - Approval final selesai (data permanen)

### Implementasi Teknis

#### Model Method (PermohonanReklame.php)
```php
public function canBeEditedByUser(): bool
{
    // Pemohon HANYA bisa edit jika status Draft atau Ditolak (apapun)
    return in_array($this->status, [
        'Draft', 
        'Ditolak Operator', 
        'Ditolak Kepala Seksi', 
        'Ditolak Kepala Bidang'
    ]);
}

public function getEditRestrictionReason(): ?string
{
    // Mengembalikan alasan mengapa data tidak bisa diedit
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

#### Controller Enforcement
```php
// File: app/Http/Controllers/PermohonanReklameController.php

public function edit(PermohonanReklame $permohonan): View
{
    // Check authorization
    if ($permohonan->user_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
    }

    // Enforce edit restrictions
    if (!$permohonan->canBeEditedByUser()) {
        abort(403, $permohonan->getEditRestrictionReason());
    }

    return view('permohonan.edit', compact('permohonan'));
}

public function update(Request $request, PermohonanReklame $permohonan): RedirectResponse
{
    // Check authorization
    if ($permohonan->user_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
    }

    // Enforce edit restrictions - return message instead of abort
    if (!$permohonan->canBeEditedByUser()) {
        return redirect()->route('permohonan.show', $permohonan)
            ->with('error', $permohonan->getEditRestrictionReason());
    }

    // Update logic...
}
```

#### UI Warning
Di halaman detail permohonan (`resources/views/permohonan/show.blade.php`):
```blade
@if (auth()->user()->hasRole('pemohon') && $permohonan->user_id === auth()->id())
    @if ($permohonan->canBeEditedByUser())
        <!-- Edit button shown -->
        <a href="{{ route('permohonan.edit', $permohonan) }}" class="btn btn-warning">
            <i class="bi bi-pencil"></i> Edit
        </a>
    @else
        <!-- Lock warning shown -->
        <div class="alert alert-warning d-inline-block">
            <i class="bi bi-lock"></i> <strong>Data Terkunci</strong><br>
            <small>{{ $permohonan->getEditRestrictionReason() }}</small>
        </div>
    @endif
@endif
```

---

## 2. Audit Trail & Activity Logging

### Comprehensive Change Tracking
Semua perubahan data oleh petugas/staff dicatat secara otomatis dalam tabel `activity_logs`.

### Informasi yang Dicatat

#### Untuk Setiap Aktivitas:
| Field | Deskripsi |
|-------|-----------|
| `user_id` | ID petugas yang melakukan perubahan |
| `action` | Jenis aksi (APPROVAL_OPERATOR, APPROVAL_KEPALA_SEKSI, APPROVAL_KEPALA_BIDANG, UPDATE, SUBMIT, DELETE, DOCUMENT_VERIFICATION) |
| `model_type` | Tipe model yang diubah (PermohonanReklame, PersyaratanDokumen) |
| `model_id` | ID record yang diubah |
| `description` | Deskripsi detail perubahan |
| `old_values` | Data sebelum perubahan (JSON) |
| `new_values` | Data setelah perubahan (JSON) |
| `ip_address` | IP address petugas yang melakukan perubahan |
| `user_agent` | User Agent (browser/device info) petugas |
| `created_at` | Waktu perubahan |

### Action Types yang Dilogging

#### 1. APPROVAL_OPERATOR
```php
ActivityLog::create([
    'action' => 'APPROVAL_OPERATOR',
    'description' => "Verifikasi operator permohonan {$permohonan->nomor_registrasi}: Disetujui",
    'old_values' => ['status' => 'Diajukan'],
    'new_values' => ['status' => 'Diverifikasi Operator', 'keputusan' => 'Disetujui'],
    // ... other fields
]);
```

#### 2. APPROVAL_KEPALA_SEKSI
```php
ActivityLog::create([
    'action' => 'APPROVAL_KEPALA_SEKSI',
    'description' => "Approval Kepala Seksi permohonan {$permohonan->nomor_registrasi}: Disetujui",
    'old_values' => ['status' => 'Diverifikasi Operator'],
    'new_values' => ['status' => 'Disetujui Kepala Seksi', 'keputusan' => 'Disetujui'],
    // ... other fields
]);
```

#### 3. APPROVAL_KEPALA_BIDANG (FINAL APPROVAL)
```php
ActivityLog::create([
    'action' => 'APPROVAL_KEPALA_BIDANG',
    'description' => "Final approval Kepala Bidang permohonan {$permohonan->nomor_registrasi}: Disetujui",
    'old_values' => ['status' => 'Disetujui Kepala Seksi'],
    'new_values' => [
        'status' => 'Disetujui Kepala Bidang',
        'keputusan' => 'Disetujui',
        'tanggal_berlaku' => '2026-01-27',
        'tanggal_berakhir' => '2027-01-27'
    ],
    // ... other fields
]);
```

#### 4. DOCUMENT_VERIFICATION (Verifikasi Dokumen)
```php
ActivityLog::create([
    'action' => 'DOCUMENT_VERIFICATION',
    'model_type' => 'PersyaratanDokumen',
    'description' => "Verifikasi dokumen 'Fotocopy KTP berwarna' untuk permohonan RKL-2026-01-12345",
    'old_values' => ['status' => 'Belum Lengkap'],
    'new_values' => ['status' => 'Lengkap'],
    // ... other fields
]);
```

#### 5. UPDATE (Data Pemohon Diupdate)
```php
ActivityLog::create([
    'action' => 'UPDATE',
    'description' => "Memperbarui permohonan reklame: RKL-2026-01-12345",
    'old_values' => ['nama_pemohon' => 'John Doe'],
    'new_values' => ['nama_pemohon' => 'John Doe Updated'],
    // ... other fields
]);
```

### Tampilan Audit Trail
Audit trail ditampilkan di halaman detail permohonan (show.blade.php) dalam widget "Riwayat Perubahan":

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
        @foreach ($activityLogs as $log)
            <div class="timeline-item mb-3 pb-3">
                <p class="small mb-1">
                    <strong>
                        @if ($log->action === 'APPROVAL_OPERATOR')
                            <i class="bi bi-check-circle text-info"></i> Verifikasi Operator
                        @elseif ($log->action === 'APPROVAL_KEPALA_SEKSI')
                            <i class="bi bi-check-circle text-info"></i> Approval Kepala Seksi
                        @elseif ($log->action === 'APPROVAL_KEPALA_BIDANG')
                            <i class="bi bi-check-circle text-success"></i> Approval Kepala Bidang
                        @endif
                    </strong>
                </p>
                <p class="small text-muted mb-1">{{ $log->description }}</p>
                <small class="text-muted d-block">
                    {{ $log->created_at->format('d M Y H:i') }} | IP: {{ $log->ip_address ?? '-' }}
                </small>
            </div>
        @endforeach
    </div>
</div>
```

---

## 3. Sentralisasi Kontrol Akses

### Authorization Checks
Semua operasi sensitif memiliki authorization check berlapis:

#### Layer 1: Ownership Check
```php
if ($permohonan->user_id !== auth()->id()) {
    abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
}
```

#### Layer 2: Role-Based Check
```php
if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
    abort(403, 'Hanya staff yang dapat mengakses halaman ini');
}
```

#### Layer 3: Status-Based Check
```php
if (!$permohonan->canBeEditedByUser()) {
    return redirect()->route('permohonan.show', $permohonan)
        ->with('error', $permohonan->getEditRestrictionReason());
}
```

### Daftar Operasi yang Dilindungi

| Operasi | Authorization Check |
|---------|-------------------|
| Edit Permohonan | Ownership + canBeEditedByUser() |
| Update Permohonan | Ownership + canBeEditedByUser() |
| Submit Permohonan | Ownership + Status validation |
| Delete Permohonan | Ownership + Draft status only |
| Verify (Operator) | hasRole('operator') + canBeApprovedByOperator() |
| Approve (Kepala Seksi) | hasRole('kepala_seksi') + canBeApprovedByKepalaSeksi() |
| Approve (Kepala Bidang) | hasRole('kepala_bidang') + canBeApprovedByKepalaBidang() |
| Update Document Status | hasAnyRole(staff) + Permohonan access |
| Download File | Ownership OR Staff access |

---

## 4. Validasi & Integrity Checks

### Edit Data Validation
```php
$validated = $request->validate([
    'nama_pemohon' => 'required|string|max:255',
    'nik' => 'required|string|regex:/^\d{16}$/',
    'jenis_reklame' => 'required|in:Permanen,Non Permanen',
    'latitude' => 'required|numeric|between:-90,90',
    'longitude' => 'required|numeric|between:-180,180',
    // ... more validations
]);
```

### Masa Berlaku Validation
```php
$validated = $request->validate([
    'tanggal_berlaku' => 'nullable|date|required_if:keputusan,Disetujui',
    'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku|required_if:keputusan,Disetujui',
    // tanggal_berakhir HARUS setelah tanggal_berlaku
]);
```

### File Upload Validation
```php
'file_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
'file_npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
'file_desain' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
// Max size: 5MB, Allowed types: PDF, JPG, PNG
```

---

## 5. Security Best Practices Implemented

### ✅ Completed
- [x] IP address logging untuk setiap perubahan data
- [x] User Agent logging untuk device/browser tracking
- [x] Timestamp accurate untuk setiap aktivitas
- [x] Before/After values logging (old_values & new_values)
- [x] User identification (user_id) untuk accountability
- [x] Status-based data lock after submission
- [x] Role-based access control (RBAC)
- [x] Ownership verification (User ID matching)
- [x] Soft delete untuk data pemohon (tidak ada hard delete)
- [x] Encrypted file storage (private disk)
- [x] Validation layering

### 📋 Rekomendasi Tambahan
1. **Database Backup Regular** - Backup database setiap hari untuk disaster recovery
2. **Audit Trail Export** - Fitur export audit trail untuk compliance reporting
3. **Data Retention Policy** - Tentukan berapa lama audit trail disimpan
4. **Access Log Rotation** - Rotate access logs untuk manajemen storage
5. **Admin Dashboard** - Dashboard khusus admin untuk monitoring aktivitas
6. **Alert System** - Real-time alert jika ada aktivitas mencurigakan
7. **Encryption at Rest** - Encrypt database untuk proteksi data dalam storage

---

## 6. Testing Checklist

### Test Scenarios untuk Edit Restrictions
- [ ] Pemohon dapat edit permohonan status Draft
- [ ] Pemohon TIDAK bisa edit setelah status Diajukan
- [ ] Pemohon TIDAK bisa edit saat Diverifikasi Operator
- [ ] Pemohon TIDAK bisa edit saat Disetujui Kepala Seksi
- [ ] Pemohon TIDAK bisa edit setelah Final Approval
- [ ] Pemohon dapat edit kembali setelah Ditolak
- [ ] Error message yang jelas ditampilkan saat edit ditolak
- [ ] Audit trail mencatat setiap attempt edit

### Test Scenarios untuk Audit Trail
- [ ] Setiap approval dicatat di activity_logs
- [ ] Setiap document verification dicatat
- [ ] IP address dan User Agent tercatat akurat
- [ ] Old values dan new values tercatat sempurna
- [ ] Timeline audit trail menampilkan dengan benar
- [ ] Audit trail sortir by created_at DESC

---

## 7. Database Schema

### activity_logs Table
```sql
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action VARCHAR(255) NOT NULL,
    model_type VARCHAR(255),
    model_id BIGINT UNSIGNED,
    description TEXT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

---

## 8. Troubleshooting

### Issue: Edit button tetap muncul setelah diajukan
**Solution:** Pastikan view menggunakan method `canBeEditedByUser()` untuk conditional rendering

### Issue: Audit trail tidak muncul
**Solution:** Pastikan ActivityLog::create() dipanggil di setiap perubahan data status

### Issue: IP Address tidak tercatat
**Solution:** Gunakan `$request->ip()` untuk mendapatkan IP address klien

---

## 9. Dokumentasi Lengkap Perubahan

### Files yang Dimodifikasi:
1. **app/Models/PermohonanReklame.php**
   - Added `getEditRestrictionReason()` method

2. **app/Http/Controllers/PermohonanReklameController.php**
   - Enhanced edit() authorization
   - Enhanced update() authorization
   - Added better error messages

3. **app/Http/Controllers/ApprovalController.php**
   - Enhanced ActivityLog dengan old_values/new_values untuk semua approvals

4. **app/Http/Controllers/DocumentRequirementController.php**
   - Added document verification logging

5. **resources/views/permohonan/show.blade.php**
   - Added edit restriction warning message
   - Added audit trail / activity log timeline widget

---

**Last Updated:** 26 Januari 2026  
**Version:** 2.0 - Security & Governance Hardening
