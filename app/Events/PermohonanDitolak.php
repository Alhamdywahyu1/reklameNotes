<?php

namespace App\Events;

use App\Models\PermohonanReklame;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermohonanDitolak
{
    use Dispatchable, SerializesModels;

    public PermohonanReklame $permohonan;
    public string $keterangan;
    public string $ditolakOleh; // Operator, Kepala Seksi, Kepala Bidang

    /**
     * Create a new event instance.
     */
    public function __construct(PermohonanReklame $permohonan, string $keterangan, string $ditolakOleh)
    {
        $this->permohonan = $permohonan;
        $this->keterangan = $keterangan;
        $this->ditolakOleh = $ditolakOleh;
    }
}
