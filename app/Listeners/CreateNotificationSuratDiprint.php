<?php

namespace App\Listeners;

use App\Events\SuratDiprintOlehOperator;
use App\Mail\SuratDiprintMail;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class CreateNotificationSuratDiprint implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SuratDiprintOlehOperator $event): void
    {
        $permohonan = $event->permohonan;
        $operator = auth()->user();

        // Create notification for pemohon
        Notification::create([
            'user_id' => $permohonan->user_id,
            'type' => 'SURAT_DIPRINT',
            'title' => 'Surat Persetujuan Siap',
            'message' => "Surat persetujuan reklame Anda ({$permohonan->nomor_registrasi}) telah disiapkan oleh {$operator->name} dan siap untuk diambil.",
            'permohonan_id' => $permohonan->id,
        ]);

        // Send email to pemohon
        Mail::to($permohonan->user->email)->send(
            new SuratDiprintMail($permohonan, $operator->name)
        );

        // Log activity
        \App\Models\ActivityLog::create([
            'user_id' => $operator->id,
            'action' => 'PRINT_SURAT',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Mencetak surat persetujuan {$permohonan->nomor_registrasi}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
