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
        try {
            $permohonan = $event->permohonan;
            // In queued listeners, auth() may not be available — resolve operator by id
            $operator = \App\Models\User::find($event->operatorId);

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
                new SuratDiprintMail($permohonan, $operator?->name ?? 'Operator')
            );

            // Update permohonan status to mark as published/terbit and store timestamp
            try {
                $permohonan->update([
                    'status' => 'Sudah Terbit',
                    'tanggal_terbit' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::warning('Gagal memperbarui status permohonan menjadi Sudah Terbit: ' . $e->getMessage());
            }

            // Log activity
            \App\Models\ActivityLog::create([
                'user_id' => $operator?->id ?? $event->operatorId,
                'action' => 'PRINT_SURAT',
                'model_type' => 'PermohonanReklame',
                'model_id' => $permohonan->id,
                'description' => "Mencetak surat persetujuan {$permohonan->nomor_registrasi}",
                'ip_address' => request()?->ip() ?? null,
                'user_agent' => request()?->userAgent() ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim notifikasi surat diprint: ' . $e->getMessage());
        }
    }
}
