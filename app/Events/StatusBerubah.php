<?php

namespace App\Events;

use App\Models\PermohonanReklame;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StatusBerubah
{
    use Dispatchable, SerializesModels;

    public PermohonanReklame $permohonan;
    public string $statusLama;
    public string $statusBaru;

    /**
     * Create a new event instance.
     */
    public function __construct(PermohonanReklame $permohonan, string $statusLama, string $statusBaru)
    {
        $this->permohonan = $permohonan;
        $this->statusLama = $statusLama;
        $this->statusBaru = $statusBaru;
    }
}
