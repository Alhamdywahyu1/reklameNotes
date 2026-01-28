<?php

namespace App\Listeners;

use App\Events\StatusBerubah;
use App\Mail\PermohonanDisetujuiMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CreateNotificationStatusBerubah
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StatusBerubah $event): void
    {
        // Notifikasi ke pemohon (owner permohonan)
        Notification::create([
            'user_id' => $event->permohonan->user_id,
            'type' => 'STATUS_BERUBAH',
            'title' => 'Status Permohonan Berubah',
            'message' => "Status permohonan Anda berubah dari {$event->statusLama} menjadi {$event->statusBaru}",
            'permohonan_id' => $event->permohonan->id,
        ]);

        // Send email jika status Final Approved (Disetujui Kepala Bidang)
        if ($event->statusBaru === 'Disetujui Kepala Bidang') {
            Mail::to($event->permohonan->user->email)->send(
                new PermohonanDisetujuiMail($event->permohonan)
            );
        }
    }
}
