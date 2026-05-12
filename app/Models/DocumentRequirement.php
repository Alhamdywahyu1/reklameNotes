<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori_reklame',
        'jenis_dokumen',
        'deskripsi',
        'wajib',
    ];

    protected $casts = [
        'wajib' => 'boolean',
    ];

    public function permohonan()
    {
        return $this->hasMany(PermohonanReklame::class);
    }
}
