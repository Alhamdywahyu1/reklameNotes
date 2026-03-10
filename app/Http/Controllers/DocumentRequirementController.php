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

        // Debug: Log all files received
        \Log::info('Documents received:', [
            'has_documents' => $request->has('documents'),
            'all_files' => $request->allFiles(),
            'documents_data' => $request->input('documents'),
        ]);

        $validated = $request->validate([
            'documents' => 'required|array',
            'documents.*.file' => 'nullable|file|max:5120|mimes:pdf,jpg,jpeg,png',
            'documents.*.id' => 'required|exists:persyaratan_dokumen,id',
        ]);

        $uploadedCount = 0;
        $errors = [];

        // Get files separately since they might not be in validated array
        $files = $request->file('documents', []);

        foreach ($validated['documents'] as $index => $doc) {
            $requirement = DocumentRequirement::find($doc['id']);

            // Cek otorisasi
            if (!$requirement || $requirement->permohonan_id !== $permohonan->id) {
                continue;
            }

            // Check if file exists for this index
            $file = $files[$index]['file'] ?? null;
            
            if ($file && $file->isValid()) {
                try {
                    // Validasi file
                    $validationErrors = $this->fileValidationService::validateFile($file);
                    if (!empty($validationErrors)) {
                        $errors[] = "{$requirement->jenis_persyaratan}: " . implode(', ', $validationErrors);
                        continue;
                    }
                    
                    // Hapus file lama jika ada
                    if ($requirement->file_dokumen && Storage::disk('private')->exists($requirement->file_dokumen)) {
                        Storage::disk('private')->delete($requirement->file_dokumen);
                    }

                    $filePath = $file->store('documents/' . $permohonan->id, 'private');
                    
                    if ($filePath) {
                        $requirement->update([
                            'file_dokumen' => $filePath,
                            'status' => 'Belum Lengkap',
                        ]);
                        $uploadedCount++;
                        
                        \Log::info("File uploaded successfully", [
                            'requirement_id' => $requirement->id,
                            'file_path' => $filePath
                        ]);
                    } else {
                        $errors[] = "Gagal menyimpan file untuk {$requirement->jenis_persyaratan}";
                    }
                } catch (\Exception $e) {
                    \Log::error("File upload error: " . $e->getMessage());
                    $errors[] = "Error uploading {$requirement->jenis_persyaratan}: " . $e->getMessage();
                }
            }
        }

        if (!empty($errors)) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode(', ', $errors));
        }

        $message = $uploadedCount > 0 
            ? "{$uploadedCount} dokumen berhasil diunggah"
            : 'Tidak ada dokumen yang diupload. Pastikan Anda memilih file untuk diupload.';

        return redirect()->back()->with($uploadedCount > 0 ? 'success' : 'warning', $message);
    }

    /**
     * View untuk petugas mengecek persyaratan dokumen.
     */
    public function viewForStaff(PermohonanReklame $permohonan): View
    {
        // Ensure user and role are loaded
        $user = auth()->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // Cek otorisasi hanya untuk staff
        if (!$user->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
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
        // Cek otorisasi - pemohon atau semua staff
        $permohonan = $requirement->permohonan;
        $isOwner = $permohonan->user_id === auth()->id();
        $isStaff = auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']);
        
        if (!$isOwner && !$isStaff) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (!$requirement->file_dokumen) {
            abort(404, 'File belum diupload');
        }
        
        if (!Storage::disk('private')->exists($requirement->file_dokumen)) {
            abort(404, 'File tidak ditemukan di server');
        }

        return Storage::disk('private')->download($requirement->file_dokumen);
    }

    /**
     * Preview file dokumen (untuk gambar).
     */
    public function preview(DocumentRequirement $requirement)
    {
        // Cek otorisasi - pemohon atau semua staff
        $permohonan = $requirement->permohonan;
        $isOwner = $permohonan->user_id === auth()->id();
        $isStaff = auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']);
        
        if (!$isOwner && !$isStaff) {
            abort(403, 'Anda tidak memiliki akses');
        }

        if (!$requirement->file_dokumen) {
            abort(404, 'File belum diupload');
        }
        
        if (!Storage::disk('private')->exists($requirement->file_dokumen)) {
            abort(404, 'File tidak ditemukan di server');
        }

        $filePath = $requirement->file_dokumen;
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Hanya izinkan preview untuk file gambar
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            abort(400, 'Preview hanya tersedia untuk file gambar');
        }

        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
        ];

        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

        return response(Storage::disk('private')->get($filePath))
            ->header('Content-Type', $mimeType)
            ->header('Cache-Control', 'private, max-age=3600');
    }

    /**
     * Perbarui status persyaratan dokumen (Petugas).
     */
    public function updateStatus(Request $request, DocumentRequirement $requirement): RedirectResponse
    {
        // Cek otorisasi hanya untuk staff
        if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
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
     * Menyetujui semua dokumen sekaligus untuk operator
     */
    public function approveAllDocuments(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Cek otorisasi hanya untuk operator
        if (!auth()->user()->hasRole('operator')) {
            abort(403, 'Hanya operator yang dapat menyetujui semua dokumen sekaligus');
        }

        $requirements = $permohonan->documentRequirements()->get();

        if ($requirements->isEmpty()) {
            return redirect()->back()
                ->with('warning', 'Tidak ada dokumen untuk disetujui');
        }

        // Hitung berapa banyak dokumen yang sudah lengkap
        $notApprovedCount = 0;
        $approvedCount = 0;

        foreach ($requirements as $requirement) {
            $oldStatus = $requirement->status;
            
            if ($requirement->status !== 'Lengkap') {
                // Hanya update yang belum approved
                $requirement->update([
                    'status' => 'Lengkap',
                    'catatan_penolakan' => null,
                ]);

                // Log document approval
                ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'DOCUMENT_VERIFICATION_BULK',
                    'model_type' => 'PersyaratanDokumen',
                    'model_id' => $requirement->id,
                    'description' => "Menyetujui dokumen '{$requirement->jenis_persyaratan}' (batch approve) untuk permohonan {$permohonan->nomor_registrasi}",
                    'old_values' => ['status' => $oldStatus],
                    'new_values' => ['status' => 'Lengkap', 'catatan_penolakan' => null],
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);

                $notApprovedCount++;
            } else {
                $approvedCount++;
            }
        }

        $message = $notApprovedCount > 0 
            ? "{$notApprovedCount} dokumen berhasil disetujui ({$approvedCount} sudah disetujui sebelumnya)"
            : "Semua dokumen sudah disetujui sebelumnya";

        return redirect()->back()
            ->with('success', $message);
    }

    /**
     * Hapus persyaratan dokumen.
     */
    public function destroy(DocumentRequirement $requirement): RedirectResponse
    {
        $permohonan = $requirement->permohonan;

        // Cek otorisasi
        $isOwner = $permohonan->user_id === auth()->id();
        $isStaff = auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']);
        
        if (!$isOwner && !$isStaff) {
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
