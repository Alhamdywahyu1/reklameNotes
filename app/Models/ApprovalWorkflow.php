<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalWorkflow extends Model
{
    use SoftDeletes;

    protected $table = 'approval_workflow';

    protected $fillable = [
        'permohonan_id',
        'user_id',
        'role_id',
        'status_approval',
        'keputusan',
        'keterangan',
        'tanggal_approval',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_approval' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanReklame::class, 'permohonan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getStatusLabel(): string
    {
        return match ($this->keputusan) {
            'Disetujui' => '✓ Disetujui',
            'Ditolak' => '✗ Ditolak',
            default => 'Pending',
        };
    }

    public function getStatusColorClass(): string
    {
        return match ($this->keputusan) {
            'Disetujui' => 'text-success',
            'Ditolak' => 'text-danger',
            default => 'text-warning',
        };
    }
}
