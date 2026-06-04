<?php

namespace App\Http\Controllers;

use App\Events\SuratDiprintOlehOperator;
use App\Models\PermohonanReklame;
use App\Models\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PrintController extends Controller
{
    /**
     * Show list of approved permohonan ready to print.
     */
    public function readyList(): View
    {
        if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
            abort(403, 'Hanya Operator yang dapat mengakses daftar siap cetak');
        }

        $approvedPermohonan = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        $printedPermohonan = PermohonanReklame::where('status', 'Sudah Terbit')
            ->with('user')
            ->orderByDesc('tanggal_terbit')
            ->limit(10)
            ->get();

        return view('print.ready', compact('approvedPermohonan', 'printedPermohonan'));
    }

    /**
     * Show print preview.
     */
    public function preview(PermohonanReklame $permohonan): View
    {
        // Only operator and admin can print
        if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
            abort(403, 'Hanya Operator yang dapat mencetak dokumen');
        }

        // Allow access if approved OR already printed (for re-evaluation)
        if (!in_array($permohonan->status, ['Disetujui Kepala Bidang', 'Sudah Terbit'])) {
            abort(403, 'Dokumen hanya dapat dicetak setelah mendapat persetujuan final');
        }

        $approvals = $permohonan->approvalWorkflows()->get();
        $nomorNaskah = $this->buildNomorNaskah($permohonan, $approvals->first()?->tanggal_approval);

        return view('print.preview', compact('permohonan', 'approvals', 'nomorNaskah'));
    }

    /**
     * Generate PDF print out.
     */
    public function generatePdf(PermohonanReklame $permohonan): Response
    {
        // Only operator and admin can print
        if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
            abort(403, 'Hanya Operator yang dapat mencetak dokumen');
        }

        // Only printable if final approval
        if (!$permohonan->isPrintable()) {
            abort(403, 'Dokumen hanya dapat dicetak setelah mendapat persetujuan final');
        }

        $approvals = $permohonan->approvalWorkflows()->get();
        $nomorNaskah = $this->buildNomorNaskah($permohonan, $approvals->first()?->tanggal_approval);

        $f4Paper = [0, 0, 595.28, 935.43];

        $pdf = Pdf::loadView('print.pdf', compact('permohonan', 'approvals', 'nomorNaskah'))
            ->setPaper($f4Paper)
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'PRINT',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Mencetak dokumen permohonan {$permohonan->nomor_registrasi}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);

        return $pdf->download("Permohonan_Reklame_{$permohonan->nomor_registrasi}.pdf");
    }

    /**
     * Print surat persetujuan reklame (called after final approval).
     * Only operator can access this
     */
    public function printSurat(PermohonanReklame $permohonan): View
    {
        // Only operator and admin can print surat
        if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
            abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
        }

        // Allow access if approved OR already printed (for re-evaluation / reprint)
        if (!in_array($permohonan->status, ['Disetujui Kepala Bidang', 'Sudah Terbit'])) {
            abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
        }

        $approvals = $permohonan->approvalWorkflows()->get();
        $finalApproval = $approvals->where('status_approval', 'Disetujui Kepala Bidang')->first();
        $nomorNaskah = $this->buildNomorNaskah($permohonan, $finalApproval?->tanggal_approval);

        $sudahTerbit = $permohonan->status === 'Sudah Terbit';

        return view('print.surat', compact('permohonan', 'approvals', 'finalApproval', 'nomorNaskah', 'sudahTerbit'));
    }

    /**
     * Track print surat action and send notification
     */
    public function trackPrintSurat(PermohonanReklame $permohonan): Response
    {
        // Only operator and admin can print surat
        if (!auth()->user()->hasAnyRole(['operator', 'admin'])) {
            abort(403, 'Hanya Operator yang dapat mencetak surat persetujuan');
        }

        // Allow tracking for approved OR already printed (reprint scenario)
        if (!in_array($permohonan->status, ['Disetujui Kepala Bidang', 'Sudah Terbit'])) {
            abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
        }

        $isReprint = $permohonan->status === 'Sudah Terbit';

        // Update status (idempotent – jika sudah Sudah Terbit, update tanggal_terbit saja)
        $permohonan->update([
            'status' => 'Sudah Terbit',
            'tanggal_terbit' => now(),
        ]);

        // Hanya kirim notifikasi pada print pertama, bukan reprint
        if (!$isReprint) {
            SuratDiprintOlehOperator::dispatch($permohonan, auth()->id());
        }

        $message = $isReprint
            ? 'Surat berhasil dicetak ulang.'
            : 'Surat berhasil dicetak. Notifikasi telah dikirim ke pemohon.';

        return response()->json(['message' => $message]);
    }

    private function buildNomorNaskah(PermohonanReklame $permohonan, ?string $tanggalApproval): string
    {
        $tanggal = \Carbon\Carbon::parse($tanggalApproval ?? now());

        return sprintf(
            '500.16.7.4/%s/433.114/%s/%s',
            str_pad((string) $permohonan->id, 4, '0', STR_PAD_LEFT),
            $tanggal->format('m'),
            $tanggal->format('Y')
        );
    }
}
