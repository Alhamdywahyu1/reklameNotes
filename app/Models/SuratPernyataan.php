<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPernyataan extends Model
{
    use SoftDeletes;

    protected $table = 'surat_pernyataan';

    protected $fillable = [
        'permohonan_id',
        'user_id',
        'nama_pemohon',
        'pekerjaan',
        'alamat_pemohon',
        'no_ktp',
        'status',
        'setuju_syarat_1',
        'setuju_syarat_2',
        'setuju_syarat_3',
        'setuju_syarat_4',
        'setuju_syarat_5',
        'setuju_syarat_6',
        'setuju_syarat_7',
        'setuju_syarat_8',
        'file_tanda_tangan',
        'file_materai',
        'tanggal_pernyataan',
        'keterangan_penolakan',
        'submitted_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'tanggal_pernyataan' => 'date',
            'setuju_syarat_1' => 'boolean',
            'setuju_syarat_2' => 'boolean',
            'setuju_syarat_3' => 'boolean',
            'setuju_syarat_4' => 'boolean',
            'setuju_syarat_5' => 'boolean',
            'setuju_syarat_6' => 'boolean',
            'setuju_syarat_7' => 'boolean',
            'setuju_syarat_8' => 'boolean',
        ];
    }

    /**
     * Relasi ke PermohonanReklame
     */
    public function permohonanReklame(): BelongsTo
    {
        return $this->belongsTo(PermohonanReklame::class, 'permohonan_id');
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if all conditions are agreed
     */
    public function areAllConditionsAgreed(): bool
    {
        return $this->setuju_syarat_1 && 
               $this->setuju_syarat_2 && 
               $this->setuju_syarat_3 && 
               $this->setuju_syarat_4 && 
               $this->setuju_syarat_5 && 
               $this->setuju_syarat_6 && 
               $this->setuju_syarat_7 && 
               $this->setuju_syarat_8;
    }
}
