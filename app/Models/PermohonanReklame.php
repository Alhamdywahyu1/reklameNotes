<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Role;

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
        'klasifikasi_lokasi',
        'keperluan_reklame',
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
        'rejected_by_role_id',
        'rejected_by_user_id',
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

    public function rejectedByRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'rejected_by_role_id');
    }

    public function rejectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
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
            'Revisi Menunggu Operator', 'Revisi Menunggu Kepala Seksi' => 'warning',
            'Diverifikasi Operator' => 'warning',
            'Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang' => 'danger',
            'Disetujui Kepala Seksi' => 'info',
            'Disetujui Kepala Bidang' => 'success',
            default => 'light',
        };
    }

    public function canBeEditedByUser(): bool
    {
        // Pemohon HANYA bisa edit jika status Draft atau Ditolak (apapun)
        // Include Revisi Menunggu status jika sedang revisi
        return in_array($this->status, [
            'Draft',
            'Ditolak Operator',
            'Ditolak Kepala Seksi',
            'Ditolak Kepala Bidang',
            'Revisi Menunggu Operator',
            'Revisi Menunggu Kepala Seksi',
        ]);
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
        // Operator bisa approve permohonan baru (Diajukan) dan permohonan yang direvisi dari operator (Revisi Menunggu Operator)
        return in_array($this->status, ['Diajukan', 'Revisi Menunggu Operator']);
    }

    public function canBeApprovedByKepalaSeksi(): bool
    {
        // Kepala Seksi bisa approve dari operator dan revisi dari kepala seksi
        return in_array($this->status, ['Diverifikasi Operator', 'Revisi Menunggu Kepala Seksi']);
    }

    public function canBeApprovedByKepalaBidang(): bool
    {
        // Kepala bidang bisa approve dari kepala seksi dan revisi dari kepala bidang
        return in_array($this->status, ['Disetujui Kepala Seksi', 'Revisi Menunggu Kepala Bidang']);
    }

    /**
     * Tentukan status revisi berdasarkan siapa yang menolak
     */
    public function getNextRevisionStatus(): string
    {
        // Jika ada rejected_by_role_id, tentukan status berdasarkan role yang menolak
        if ($this->rejected_by_role_id) {
            // Cek role slug
            $rejectedRole = Role::find($this->rejected_by_role_id);
            
            if ($rejectedRole) {
                if ($rejectedRole->slug === 'operator') {
                    return 'Revisi Menunggu Operator';
                } elseif ($rejectedRole->slug === 'kepala_seksi') {
                    return 'Revisi Menunggu Kepala Seksi';
                } elseif ($rejectedRole->slug === 'kepala_bidang') {
                    return 'Revisi Menunggu Kepala Bidang';
                } elseif ($rejectedRole->slug === 'admin') {
                    return 'Revisi Menunggu Admin';
                }
            }
        }
        
        // Default fallback (jarang terjadi)
        return 'Revisi Menunggu Verifikasi';
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
