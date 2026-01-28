<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\PersyaratanDokumen as DocumentRequirement;
use App\Models\ActivityLog;
use App\Services\FileValidationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class DocumentRequirementController extends Controller
{
    public function __construct(private FileValidationService $fileValidationService)
    {
    }

    /**
     * Show form untuk input persyaratan dokumen (Pemohon).
     */
    public function createForPemohon(PermohonanReklame $permohonan): View
    {
        // Cek otorisasi
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini');
        }

        $requirements = $permohonan->documentRequirements()->get();

        return view('document-requirements.create-pemohon', compact('permohonan', 'requirements'));
    }

    /**
     * Simpan multiple dokumen dari pemohon.
     */
    public function storeMultiple(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke permohonan ini');
        }

        $validated = $request->validate([
            'documents' => 'required|array',
            'documents.*.file' => 'nullable|file|max:5120',
            'documents.*.id' => 'required|exists:persyaratan_dokumen,id',
        ]);

        $uploadedCount = 0;

        foreach ($validated['documents'] as $doc) {
            $requirement = DocumentRequirement::find($doc['id']);

            // Cek otorisasi
            if ($requirement->permohonan_id !== $permohonan->id) {
                continue;
            }

            if (isset($doc['file'])) {
                try {
                    // Validasi file
                    $this->fileValidationService->validateFile($doc['file']);
                    
                    // Hapus file lama jika ada
                    if ($requirement->file_dokumen && Storage::disk('private')->exists($requirement->file_dokumen)) {
                        Storage::disk('private')->delete($requirement->file_dokumen);
                    }

                    $filePath = $doc['file']->store('documents/' . $permohonan->id, 'private');
                    $requirement->update([
                        'file_dokumen' => $filePath,
                        'status' => 'Belum Lengkap',
                    ]);

                    $uploadedCount++;
                } catch (\Exception $e) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Error uploading ' . $requirement->jenis_persyaratan . ': ' . $e->getMessage());
                }
            }
        }

        $message = $uploadedCount > 0 
            ? "{$uploadedCount} dokumen berhasil diunggah"
            : 'Tidak ada dokumen yang diupload';

        return redirect()->back()->with('success', $message);
    }

    /**
     * View untuk petugas mengecek persyaratan dokumen.
     */
    public function viewForStaff(PermohonanReklame $permohonan): View
    {
        // Cek otorisasi hanya untuk staff
        if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Hanya staff yang dapat mengakses halaman ini');
        }

        $requirements = $permohonan->documentRequirements()->get();

        return view('document-requirements.check-staff', compact('permohonan', 'requirements'));
    }

    /**
     * Download file dokumen.
     */
    public function download(DocumentRequirement $requirement)
    {
        // Cek otorisasi
        $permohonan = $requirement->permohonan;
        if ($permohonan->user_id !== auth()->id() && !auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (!$requirement->file_dokumen || !Storage::disk('private')->exists($requirement->file_dokumen)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('private')->download($requirement->file_dokumen);
    }

    /**
     * Perbarui status persyaratan dokumen (Petugas).
     */
    public function updateStatus(Request $request, DocumentRequirement $requirement): RedirectResponse
    {
        // Cek otorisasi hanya untuk staff
        if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Hanya staff yang dapat mengakses halaman ini');
        }

        $validated = $request->validate([
            'status' => 'required|in:Belum Lengkap,Lengkap,Ditolak',
            'catatan_penolakan' => 'nullable|string|max:500',
        ]);

        $oldStatus = $requirement->status;
        
        $requirement->update([
            'status' => $validated['status'],
            'catatan_penolakan' => $validated['status'] === 'Ditolak' ? $validated['catatan_penolakan'] : null,
        ]);

        // Log document status change
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DOCUMENT_VERIFICATION',
            'model_type' => 'PersyaratanDokumen',
            'model_id' => $requirement->id,
            'description' => "Verifikasi dokumen '{$requirement->jenis_persyaratan}' untuk permohonan {$requirement->permohonan->nomor_registrasi}",
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $validated['status'], 'catatan_penolakan' => $validated['catatan_penolakan'] ?? null],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Status persyaratan dokumen berhasil diperbarui');
    }

    /**
     * Hapus persyaratan dokumen.
     */
    public function destroy(DocumentRequirement $requirement): RedirectResponse
    {
        $permohonan = $requirement->permohonan;

        // Cek otorisasi
        if ($permohonan->user_id !== auth()->id() && !auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Anda tidak memiliki akses');
        }

        // Hapus file jika ada
        if ($requirement->file_dokumen && Storage::disk('private')->exists($requirement->file_dokumen)) {
            Storage::disk('private')->delete($requirement->file_dokumen);
        }

        $requirement->delete();

        return redirect()->back()
            ->with('success', 'Persyaratan dokumen berhasil dihapus');
    }
}
