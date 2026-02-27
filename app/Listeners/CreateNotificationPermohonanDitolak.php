<?php

namespace App\Listeners;

use App\Events\PermohonanDitolak;
use App\Mail\PermohonanDitolakMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CreateNotificationPermohonanDitolak
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
    public function handle(PermohonanDitolak $event): void
    {
        try {
            // Notifikasi ke pemohon (owner permohonan)
            Notification::create([
                'user_id' => $event->permohonan->user_id,
                'type' => 'PERMOHONAN_DITOLAK',
                'title' => 'Permohonan Ditolak',
                'message' => "Permohonan Anda ditolak oleh {$event->ditolakOleh}. Alasan: {$event->keterangan}",
                'permohonan_id' => $event->permohonan->id,
            ]);

            // Send email notifikasi penolakan
            Mail::to($event->permohonan->user->email)->send(
                new PermohonanDitolakMail($event->permohonan, $event->keterangan)
            );
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim notifikasi penolakan: ' . $e->getMessage());
        }
    }
}
