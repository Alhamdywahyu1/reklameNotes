<?php

namespace App\Listeners;

use App\Events\PermohonanDiajukan;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateNotificationPermohonanDiajukan
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
    public function handle(PermohonanDiajukan $event): void
    {
        // Notifikasi ke staff (operator, kepala_seksi, kepala_bidang, admin)
        $staffUsers = User::whereHas('role', function ($query) {
            $query->whereIn('slug', ['operator', 'kepala_seksi', 'kepala_bidang', 'admin']);
        })->get();

        foreach ($staffUsers as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'PENGAJUAN_BARU',
                'title' => 'Pengajuan Reklame Baru',
                'message' => "{$event->permohonan->nama_pemohon} mengajukan permohonan reklame {$event->permohonan->jenis_reklame}",
                'permohonan_id' => $event->permohonan->id,
            ]);
        }
    }
}
