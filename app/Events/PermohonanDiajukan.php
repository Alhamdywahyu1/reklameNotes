<?php

namespace App\Events;

use App\Models\PermohonanReklame;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PermohonanDiajukan
{
    use Dispatchable, SerializesModels;

    public PermohonanReklame $permohonan;

    /**
     * Create a new event instance.
     */
    public function __construct(PermohonanReklame $permohonan)
    {
        $this->permohonan = $permohonan;
    }
}
