<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PermohonanReklame extends Model
{
    use SoftDeletes;

    protected $table = 'permohonan_reklame';

    protected $fillable = [
        'nomor_registrasi',
        'user_id',
        'nama_pemohon',
        'alamat_pemohon',
        'nomor_telepon',
        'nik',
        'npwp',
        'pekerjaan',
        'status_reklame',
        'nama_reklame',
        'alamat_perusahaan',
        'jenis_reklame',
        'ukuran_reklame',
        'jumlah_reklame',
        'jumlah_warna',
        'rata_rata',
        'narasi_reklame',
        'lokasi_pemasangan',
        'latitude',
        'longitude',
        'masa_berlaku',
        'form_step',
        'file_ktp',
        'file_npwp',
        'file_desain',
        'status',
        'keterangan_penolakan',
        'reminder_sent_at',
        'tanggal_berlaku',
        'tanggal_berakhir',
        'status_kedaluwarsa',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'reminder_sent_at' => 'datetime',
            'tanggal_berlaku' => 'date',
            'tanggal_berakhir' => 'date',
            'masa_berlaku' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function persyaratanDokumen(): HasMany
    {
        return $this->hasMany(PersyaratanDokumen::class, 'permohonan_id');
    }

    public function documentRequirements(): HasMany
    {
        return $this->persyaratanDokumen();
    }

    public function approvalWorkflows(): HasMany
    {
        return $this->hasMany(ApprovalWorkflow::class, 'permohonan_id')->orderBy('created_at', 'desc');
    }

    public function suratPernyataan()
    {
        return $this->hasOne(SuratPernyataan::class, 'permohonan_id');
    }

    public function generateNomorRegistrasi(): string
    {
        do {
            $year = date('Y');
            $month = date('m');
            $randomNumber = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $nomorRegistrasi = "RKL-{$year}-{$month}-{$randomNumber}";
        } while (self::where('nomor_registrasi', $nomorRegistrasi)->exists());
        
        return $nomorRegistrasi;
    }

    /**
     * ALTERNATIVE METHOD: Gunakan sequential number untuk jaminan unikitas 100%
     * Format: RKL-YYYY-NNNNNNN (contoh: RKL-2026-0000001)
     * 
     * Keuntungan:
     * - Tidak ada collision risk
     * - Sequential & predictable
     * - Mudah untuk audit
     * 
     * Gunakan dengan: $permohonan->nomor_registrasi = $permohonan->generateNomorRegistrasiSequential();
     */
    public function generateNomorRegistrasiSequential(): string
    {
        $year = date('Y');
        
        // Gunakan database lock untuk mencegah race condition
        $sequence = \DB::table('registrasi_sequences')
            ->where('tahun', $year)
            ->lockForUpdate()
            ->first();
        
        if (!$sequence) {
            // Buat sequence baru untuk tahun ini
            \DB::table('registrasi_sequences')->insert([
                'tahun' => $year,
                'counter' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $counter = 1;
        } else {
            // Increment counter
            $counter = $sequence->counter + 1;
            \DB::table('registrasi_sequences')
                ->where('tahun', $year)
                ->update(['counter' => $counter, 'updated_at' => now()]);
        }
        
        return "RKL-{$year}-" . str_pad($counter, 7, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeColor(): string
    {
        return match ($this->status) {
            'Draft' => 'secondary',
            'Diajukan' => 'info',
            'Diverifikasi Operator' => 'warning',
            'Ditolak Operator', 'Ditolak Kepala Seksi' => 'danger',
            'Disetujui Kepala Seksi' => 'info',
            'Disetujui Kepala Bidang' => 'success',
            default => 'light',
        };
    }

    public function canBeEditedByUser(): bool
    {
        // Pemohon HANYA bisa edit jika status Draft atau Ditolak (apapun)
        // Setelah "Diajukan", data pemohon TERKUNCI sampai permohonan ditolak
        return in_array($this->status, ['Draft', 'Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang']);
    }

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

    public function canBeApprovedByOperator(): bool
    {
        // Operator bisa approve permohonan baru (Diajukan) dan permohonan yang direvisi (Revisi Menunggu Verifikasi)
        return in_array($this->status, ['Diajukan', 'Revisi Menunggu Verifikasi']);
    }

    public function canBeApprovedByKepalaSeksi(): bool
    {
        return $this->status === 'Diverifikasi Operator';
    }

    public function canBeApprovedByKepalaBidang(): bool
    {
        return $this->status === 'Disetujui Kepala Seksi';
    }

    public function allRequirementsComplete(): bool
    {
        return $this->persyaratanDokumen()->where('is_lengkap', false)->count() === 0;
    }

    public function isPrintable(): bool
    {
        return $this->status === 'Disetujui Kepala Bidang';
    }

    public function getStatusKedaluarsa(): string
    {
        if ($this->status_kedaluwarsa === 'Dicabut') {
            return 'Dicabut';
        }

        if (!$this->tanggal_berakhir) {
            return $this->status_kedaluwarsa ?? 'Aktif';
        }

        if (now()->isAfter($this->tanggal_berakhir)) {
            return 'Kedaluwarsa';
        }

        return 'Aktif';
    }

    public function updateStatusKedaluarsa(): void
    {
        $newStatus = $this->getStatusKedaluarsa();
        if ($this->status_kedaluwarsa !== $newStatus) {
            $this->update(['status_kedaluwarsa' => $newStatus]);
        }
    }
}
