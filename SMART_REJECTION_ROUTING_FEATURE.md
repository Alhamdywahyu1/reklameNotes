# Smart Rejection Routing Feature

## Deskripsi
Fitur ini memastikan bahwa ketika suatu permohonan ditolak oleh petugas tertentu (Operator atau Kepala Seksi), dan pemohon melakukan revisi, dokumen akan **kembali ke petugas yang menolaknya**, bukan dimulai dari tahap awal.

## Workflow

### Scenario 1: Ditolak oleh Operator
```
1. Pemohon mengajukan permohonan → Status: "Diajukan"
2. Operator mevalidasi dan MENOLAK → Status: "Ditolak Operator"
   (Simpan: rejected_by_role_id = operator_role_id)
3. Pemohon revisi dan submit kembali → Status: "Revisi Menunggu Operator"
4. Operator KEMBALI review (hanya operator yang bisa) → Approve/Reject
```

### Scenario 2: Ditolak oleh Kepala Seksi
```
1. Dokumen lolos operator → Status: "Diverifikasi Operator"
2. Kepala Seksi review dan MENOLAK → Status: "Ditolak Kepala Seksi"
   (Simpan: rejected_by_role_id = kepala_seksi_role_id)
3. Pemohon revisi dan submit kembali → Status: "Revisi Menunggu Kepala Seksi"
4. Kepala Seksi LANGSUNG review (skip operator) → Approve/Reject
```

## Database Changes

### Kolom Baru di `permohonan_reklame`
- `rejected_by_role_id` - Foreign key ke roles table
- `rejected_by_user_id` - Foreign key ke users table

### Status Enum Baru
- `Revisi Menunggu Operator` - Permohonan dalam revisi, menunggu review operator
- `Revisi Menunggu Kepala Seksi` - Permohonan dalam revisi, menunggu review kepala seksi

## Code Implementation

### 1. ApprovalController
Saat rejection, simpan info petugas yang menolak:
```php
if ($validated['keputusan'] === 'Ditolak') {
    $permohonan->rejected_by_role_id = auth()->user()->role_id;
    $permohonan->rejected_by_user_id = auth()->id();
}
```

### 2. PermohonanReklameController
Method submit() menggunakan getNextRevisionStatus():
```php
$newStatus = $permohonan->getNextRevisionStatus();
```

### 3. PermohonanReklame Model
Method getNextRevisionStatus() menentukan status berdasarkan role yang menolak:
```php
public function getNextRevisionStatus(): string
{
    if ($this->rejected_by_role_id) {
        $rejectedRole = Role::find($this->rejected_by_role_id);
        if ($rejectedRole->slug === 'operator') {
            return 'Revisi Menunggu Operator';
        } elseif ($rejectedRole->slug === 'kepala_seksi') {
            return 'Revisi Menunggu Kepala Seksi';
        }
    }
    return 'Revisi Menunggu Verifikasi';
}
```

### 4. Updated canBeApprovedBy() Methods
```php
// Operator dapat approve status Diajukan dan Revisi Menunggu Operator
public function canBeApprovedByOperator(): bool
{
    return in_array($this->status, ['Diajukan', 'Revisi Menunggu Operator']);
}

// Kepala Seksi dapat approve status Diverifikasi Operator dan Revisi Menunggu Kepala Seksi
public function canBeApprovedByKepalaSeksi(): bool
{
    return in_array($this->status, ['Diverifikasi Operator', 'Revisi Menunggu Kepala Seksi']);
}
```

## User Experience

### Untuk Pemohon
- Lihat status "Revisi Menunggu Operator" atau "Revisi Menunggu Kepala Seksi"
- Tahu persis siapa yang menolak dan harus review revisi

### Untuk Operator
- Lihat dashboard dengan kategori "Revisi Menunggu Operator" sebagai prioritas
- Hanya review permohonan yang sebelumnya dia tolak

### Untuk Kepala Seksi  
- Lihat dashboard dengan kategori "Revisi Menunggu Kepala Seksi"
- Skip tahap operator, langsung ke review

## View Updates

- `approval/dashboard.blade.php` - Menampilkan "Revisi dari [Role Name]" untuk status revisi
- `approval/status.blade.php` - Badge dengan informasi "Revisi → Operator" atau "Revisi → Kepala Seksi"

## Benefit

✅ **Efisiensi** - Revisi tidak perlu melalui semua tahap lagi
✅ **Akuntabilitas** - Jelas siapa yang menolak dan bertanggung jawab
✅ **User Experience** - Pemohon tahu status dan siapa yang harus review
✅ **Audit Trail** - Tercatat role yang menolak di database
