<?php

namespace App\Console\Commands;

use App\Mail\PermohonanDisetujuiMail;
use App\Mail\PermohonanDitolakMail;
use App\Models\PermohonanReklame;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailNotification extends Command
{
    protected $signature = 'test:email {type=approval : approval or rejection}';
    protected $description = 'Test email notification system';

    public function handle()
    {
        $type = $this->argument('type');
        $permohonan = PermohonanReklame::with('user')->first();

        if (!$permohonan) {
            $this->error('❌ Tidak ada permohonan ditemukan. Buat permohonan dulu!');
            return;
        }

        if (!$permohonan->user) {
            $this->error('❌ Permohonan tidak memiliki user!');
            return;
        }

        try {
            if ($type === 'approval') {
                $this->info('📧 Mengirim email approval test...');
                Mail::to($permohonan->user->email)->send(
                    new PermohonanDisetujuiMail($permohonan)
                );
                $this->info('✅ Email approval berhasil dikirim ke: ' . $permohonan->user->email);
            } else {
                $this->info('📧 Mengirim email rejection test...');
                Mail::to($permohonan->user->email)->send(
                    new PermohonanDitolakMail($permohonan, 'Dokumen tidak lengkap')
                );
                $this->info('✅ Email rejection berhasil dikirim ke: ' . $permohonan->user->email);
            }
        } catch (\Exception $e) {
            $this->error('❌ Error saat mengirim email:');
            $this->error($e->getMessage());
        }
    }
}
