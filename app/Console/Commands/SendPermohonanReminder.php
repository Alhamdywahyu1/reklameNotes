<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PermohonanReklame;
use App\Models\User;
use App\Mail\PermohonanReminder;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendPermohonanReminder extends Command
{
    protected $signature = 'permohonan:send-reminder';

    protected $description = 'Kirim reminder email untuk permohonan yang menunggu verifikasi lebih dari 7 hari';

    public function handle()
    {
        $this->info('Memulai pengiriman reminder permohonan...');

        try {
            // Cari permohonan yang menunggu verifikasi lebih dari 7 hari
            $sevenDaysAgo = Carbon::now()->subDays(7);

            $pendingPermohonan = PermohonanReklame::where(function ($query) {
                $query->where('status', 'Diajukan')
                    ->orWhere('status', 'Revisi Menunggu Verifikasi');
            })
            ->where('created_at', '<=', $sevenDaysAgo)
            ->where('reminder_sent_at', null) // Belum pernah dikirim reminder
            ->orWhere('reminder_sent_at', '<=', Carbon::now()->subDays(3)) // Atau sudah 3 hari sejak reminder terakhir
            ->get();

            if ($pendingPermohonan->isEmpty()) {
                $this->info('Tidak ada permohonan yang memerlukan reminder.');
                return Command::SUCCESS;
            }

            $sentCount = 0;

            foreach ($pendingPermohonan as $permohonan) {
                try {
                    // Ambil operator/kepala seksi untuk permohonan ini
                    $operators = User::where('role_id', function ($query) {
                        $query->select('id')
                            ->from('roles')
                            ->whereIn('name', ['operator', 'kepala_seksi']);
                    })->where('is_active', true)->get();

                    // Jika tidak ada operator, kirim ke admin
                    if ($operators->isEmpty()) {
                        $operators = User::where('role_id', function ($query) {
                            $query->select('id')
                                ->from('roles')
                                ->where('name', 'admin');
                        })->where('is_active', true)->get();
                    }

                    // Kirim email ke setiap operator
                    foreach ($operators as $operator) {
                        Mail::to($operator->email)->send(new PermohonanReminder($permohonan));
                    }

                    // Update reminder_sent_at
                    $permohonan->update([
                        'reminder_sent_at' => Carbon::now()
                    ]);

                    $sentCount++;
                    $this->line("✓ Reminder dikirim untuk permohonan: {$permohonan->nomor_registrasi}");

                } catch (\Exception $e) {
                    $this->error("✗ Gagal mengirim reminder untuk {$permohonan->nomor_registrasi}: {$e->getMessage()}");
                }
            }

            $this->info("Pengiriman reminder selesai. {$sentCount} permohonan berhasil mengirim reminder.");
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}
