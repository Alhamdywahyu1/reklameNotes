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
     * Show print preview.
     */
    public function preview(PermohonanReklame $permohonan): View
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

        return view('print.preview', compact('permohonan', 'approvals'));
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

        $pdf = Pdf::loadView('print.pdf', compact('permohonan', 'approvals'))
            ->setPaper('a4')
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

        // Only printable if final approval
        if (!$permohonan->isPrintable()) {
            abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
        }

        $approvals = $permohonan->approvalWorkflows()->get();
        $finalApproval = $approvals->where('status_approval', 'Disetujui Kepala Bidang')->first();

        return view('print.surat', compact('permohonan', 'approvals', 'finalApproval'));
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

        // Only printable if final approval
        if (!$permohonan->isPrintable()) {
            abort(403, 'Surat hanya dapat dicetak setelah mendapat persetujuan final');
        }

        // Dispatch event to send notification and email
        SuratDiprintOlehOperator::dispatch($permohonan, auth()->id());

        return response()->json(['message' => 'Surat berhasil dicetak. Notifikasi telah dikirim ke pemohon.']);
    }
}
