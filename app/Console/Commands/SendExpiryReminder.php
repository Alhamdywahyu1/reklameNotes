<?php

namespace App\Console\Commands;

use App\Mail\MasaBerlakuReklameReminder;
use App\Models\PermohonanReklame;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class SendExpiryReminder extends Command
{
    protected $signature = 'permohonan:send-expiry-reminder {--days=10 : Jumlah hari sebelum masa berlaku berakhir}';

    protected $description = 'Kirim reminder email masa berlaku reklame yang akan habis';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $sentAtColumn = match ($days) {
            10 => 'expiry_reminder_sent_at',
            3 => 'expiry_reminder_h3_sent_at',
            default => null,
        };

        if ($sentAtColumn === null) {
            $this->error('Nilai --days yang didukung hanya 10 atau 3.');
            return Command::FAILURE;
        }

        $targetDate = Carbon::today()->addDays($days);

        $this->info("Mencari reklame yang masa berlakunya habis dalam {$days} hari ({$targetDate->toDateString()})...");

        $permohonanList = PermohonanReklame::with('user')
            ->where('status', 'Disetujui Kepala Bidang')
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', $targetDate)
            ->whereNull($sentAtColumn)
            ->get();

        if ($permohonanList->isEmpty()) {
            $this->info('Tidak ada data yang memenuhi kriteria reminder masa berlaku.');
            return Command::SUCCESS;
        }

        $successCount = 0;

        foreach ($permohonanList as $permohonan) {
            try {
                if (!$permohonan->user || empty($permohonan->user->email)) {
                    $this->warn("Lewati {$permohonan->nomor_registrasi}: email pemohon tidak ditemukan.");
                    continue;
                }

                Mail::to($permohonan->user->email)
                    ->send(new MasaBerlakuReklameReminder($permohonan, $days));

                $permohonan->update([
                    $sentAtColumn => now(),
                ]);

                $successCount++;
                $this->line("OK - Reminder H-{$days} terkirim: {$permohonan->nomor_registrasi} ({$permohonan->user->email})");
            } catch (\Throwable $e) {
                $this->error("Gagal kirim {$permohonan->nomor_registrasi}: {$e->getMessage()}");
            }
        }

        $this->info("Selesai. {$successCount} email reminder masa berlaku berhasil dikirim.");

        return Command::SUCCESS;
    }
}
