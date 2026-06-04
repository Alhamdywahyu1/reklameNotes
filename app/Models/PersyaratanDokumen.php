<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersyaratanDokumen extends Model
{
    use SoftDeletes;

    protected $table = 'persyaratan_dokumen';

    protected $fillable = [
        'permohonan_id',
        'jenis_persyaratan',
        'is_optional',
        'is_lengkap',
        'file_dokumen',
        'keterangan',
        'status',
        'catatan_penolakan',
    ];

    protected function casts(): array
    {
        return [
            'is_optional' => 'boolean',
            'is_lengkap' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanReklame::class, 'permohonan_id');
    }

    public const PERSYARATAN_REQUIRED = [
        'Fotocopy KTP berwarna',
        'Fotocopy NPWP berwarna',
        'Fotocopy Akta Pendirian',
        'Fotocopy Retribusi Pajak Reklame',
        'Data Isian Pemohon',
        'Surat Pernyataan Pertanggungjawaban Konstruksi',
        'Foto kondisi & visualisasi reklame',
        'Gambar konstruksi bidang',
    ];

    /** Jenis persyaratan untuk foto/visualisasi reklame di peta & popup */
    public const JENIS_FOTO_KONDISI_REKLAME = 'Foto kondisi & visualisasi reklame';

    public const PERSYARATAN_OPTIONAL = [
        'Surat Kuasa',
    ];
}
