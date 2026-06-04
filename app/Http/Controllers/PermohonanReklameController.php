<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\PersyaratanDokumen;
use App\Models\ActivityLog;
use App\Events\PermohonanDiajukan;
use App\Services\FileValidationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PermohonanReklameController extends Controller
{
    public function __construct(private FileValidationService $fileValidationService)
    {
    }

    /**
     * Display a listing of user's permohonan.
     */
    public function index(): View
    {
        $permohonan = auth()->user()->permohonanReklame()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('permohonan.index', compact('permohonan'));
    }

    /**
     * Show the form for creating a new permohonan.
     */
    public function create(): View
    {
        return view('permohonan.create');
    }

    /**
     * Store a newly created permohonan in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Cek apakah user sudah memverifikasi email (hanya bila OTP verification diaktifkan)
        if (env('OTP_VERIFICATION_ENABLED', true) && !auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('otp.show')->with('warning', 'Silakan verifikasi email Anda terlebih dahulu sebelum mengajukan permohonan.');
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'alamat_pemohon' => 'required|string',
            'nomor_telepon' => 'required|string|max:15',
            'nik' => 'required|string|regex:/^\d{16}$/',
            'npwp' => 'nullable|string|max:15',
            'jenis_reklame' => 'required|in:Permanen,Non Permanen',
            'ukuran_reklame' => 'required|string|max:255',
            'jumlah_reklame' => 'required|integer|min:1',
            'narasi_reklame' => 'required|string',
            'lokasi_pemasangan' => 'required|string',
            'klasifikasi_lokasi' => 'nullable|string|max:100',
            'keperluan_reklame' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'file_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_desain' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'requirement_files' => 'nullable|array',
            'requirement_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Cek apakah sudah ada 3 permohonan aktif dengan NIK yang sama
        $activeStatuses = ['Draft', 'Diajukan', 'Diverifikasi Operator', 'Disetujui Kepala Seksi'];
        $activePermohonanCount = PermohonanReklame::where('nik', $validated['nik'])
            ->whereIn('status', $activeStatuses)
            ->count();
        
        $maxActivePermohonan = 3; // Maksimal 3 permohonan aktif per NIK
        
        if ($activePermohonanCount >= $maxActivePermohonan) {
            $existingRegistrations = PermohonanReklame::where('nik', $validated['nik'])
                ->whereIn('status', $activeStatuses)
                ->pluck('nomor_registrasi')
                ->implode(', ');
            
            return redirect()->back()
                ->withInput()
                ->with('error', "Anda sudah memiliki {$maxActivePermohonan} permohonan aktif dengan NIK ini ({$existingRegistrations}). Silakan menunggu hingga salah satu permohonan selesai atau ditolak sebelum mengajukan yang baru.");
        }

        // Validate files strictly
        $fileErrors = [];
        foreach (['file_ktp', 'file_npwp', 'file_desain'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $errors = FileValidationService::validateFile($request->file($fileField));
                if (!empty($errors)) {
                    $fileErrors[$fileField] = implode(', ', $errors);
                }
            }
        }

        if (!empty($fileErrors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors($fileErrors);
        }

        $permohonan = new PermohonanReklame($validated);
        $permohonan->user_id = auth()->id();
        $permohonan->nomor_registrasi = $permohonan->generateNomorRegistrasi();
        $permohonan->status = 'Draft';

        // Handle file uploads dengan secure filename
        if ($request->hasFile('file_ktp')) {
            $file = $request->file('file_ktp');
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/ktp', $filename, 'public');
            $permohonan->file_ktp = $path;
        }
        if ($request->hasFile('file_npwp')) {
            $file = $request->file('file_npwp');
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/npwp', $filename, 'public');
            $permohonan->file_npwp = $path;
        }
        if ($request->hasFile('file_desain')) {
            $file = $request->file('file_desain');
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/desain', $filename, 'public');
            $permohonan->file_desain = $path;
        }

        $permohonan->save();

        // Create 9 default document requirements
        $defaultDocuments = [
            ['jenis' => 'Fotocopy KTP berwarna', 'desc' => 'Fotocopy KTP pemohon yang masih berlaku', 'optional' => false],
            ['jenis' => 'Fotocopy NPWP berwarna', 'desc' => 'Fotocopy NPWP pemohon', 'optional' => true],
            ['jenis' => 'Fotocopy Akta Pendirian', 'desc' => 'Akta pendirian perusahaan atau badan usaha', 'optional' => true],
            ['jenis' => 'Fotocopy Retribusi Pajak Reklame', 'desc' => 'Bukti pembayaran retribusi pajak reklame', 'optional' => false],
            ['jenis' => 'Data Isian Pemohon', 'desc' => 'Formulir data isian pemohon yang telah diisi lengkap', 'optional' => false],
            ['jenis' => 'Surat Pernyataan Pertanggungjawaban Konstruksi', 'desc' => 'Surat pernyataan dari pemilik/kuasa tentang konstruksi', 'optional' => false],
            ['jenis' => 'Foto kondisi & visualisasi reklame', 'desc' => 'Dokumentasi foto kondisi lokasi dan desain reklame', 'optional' => false],
            ['jenis' => 'Gambar konstruksi bidang', 'desc' => 'Gambar teknis konstruksi bidang reklame', 'optional' => false],
            ['jenis' => 'Surat Kuasa', 'desc' => 'Surat kuasa jika permohonan diwakilkan (opsional)', 'optional' => true],
        ];

        foreach ($defaultDocuments as $doc) {
            PersyaratanDokumen::create([
                'permohonan_id' => $permohonan->id,
                'jenis_persyaratan' => $doc['jenis'],
                'keterangan' => $doc['desc'],
                'is_optional' => $doc['optional'],
                'is_lengkap' => false,
                'status' => 'Belum Lengkap',
            ]);
        }

        // Handle file uploads for document requirements dari form create
        if ($request->has('requirement_files')) {
            $requirementFiles = $request->file('requirement_files');
            $persyaratanDocs = $permohonan->documentRequirements()->get();
            
            foreach ($requirementFiles as $index => $file) {
                if ($file !== null && $file->isValid() && isset($persyaratanDocs[$index])) {
                    try {
                        $validationErrors = FileValidationService::validateFile($file);
                        if (!empty($validationErrors)) {
                            \Log::warning("File validation failed: " . implode(', ', $validationErrors));
                            continue;
                        }
                        
                        $filePath = $file->store('documents/' . $permohonan->id, 'private');
                        if ($filePath) {
                            $persyaratanDocs[$index]->update([
                                'file_dokumen' => $filePath,
                                'status' => 'Belum Lengkap',
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Log but don't stop the process
                        \Log::warning("Error uploading requirement file: " . $e->getMessage());
                    }
                }
            }
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Membuat permohonan reklame: {$permohonan->nomor_registrasi}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('permohonan.show', $permohonan)
            ->with('success', 'Permohonan berhasil dibuat dengan nomor: ' . $permohonan->nomor_registrasi . '. Silakan lengkapi data yang diperlukan.');
    }

    /**
     * Display the specified permohonan.
     */
    public function show(PermohonanReklame $permohonan): View
    {
        // Check authorization - allow owner or staff
        if ($permohonan->user_id !== auth()->id()) {
            // Jika bukan pemilik, cek apakah staff
            $user = auth()->user();
            if (!$user->relationLoaded('role')) {
                $user->load('role');
            }
            $roleSlug = $user->role?->slug;
            if (!in_array($roleSlug, ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
                abort(403);
            }
        }

        $persyaratan = $permohonan->persyaratanDokumen()->get();
        $approvals = $permohonan->approvalWorkflows()->get();
        $suratPernyataan = $permohonan->suratPernyataan;

        return view('permohonan.show', compact('permohonan', 'persyaratan', 'approvals', 'suratPernyataan'));
    }

    /**
     * Show the form for editing the specified permohonan.
     */
    public function edit(PermohonanReklame $permohonan): View
    {
        // Check authorization
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
        }

        if (!$permohonan->canBeEditedByUser()) {
            abort(403, $permohonan->getEditRestrictionReason() ?? 'Permohonan tidak dapat diedit pada status ini');
        }

        $requirements = $permohonan->documentRequirements()->get();

        return view('permohonan.edit', compact('permohonan', 'requirements'));
    }

    /**
     * Update the specified permohonan in storage.
     */
    public function update(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Check authorization
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit permohonan ini');
        }

        if (!$permohonan->canBeEditedByUser()) {
            return redirect()->route('permohonan.show', $permohonan)
                ->with('error', $permohonan->getEditRestrictionReason() ?? 'Permohonan tidak dapat diedit pada status ini');
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'alamat_pemohon' => 'required|string',
            'nomor_telepon' => 'required|string|max:15',
            'nik' => 'required|string|regex:/^\d{16}$/',
            'npwp' => 'nullable|string|max:15',
            'jenis_reklame' => 'required|in:Permanen,Non Permanen',
            'ukuran_reklame' => 'required|string|max:255',
            'jumlah_reklame' => 'required|integer|min:1',
            'narasi_reklame' => 'required|string',
            'lokasi_pemasangan' => 'required|string',
            'klasifikasi_lokasi' => 'nullable|string|max:100',
            'keperluan_reklame' => 'nullable|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'file_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_npwp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_desain' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'remove_file_ktp' => 'nullable|boolean',
            'remove_file_npwp' => 'nullable|boolean',
            'remove_file_desain' => 'nullable|boolean',
            'documents' => 'nullable|array',
            'documents.*.id' => 'required|exists:persyaratan_dokumen,id',
            'documents.*.file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $oldValues = $permohonan->getAttributes();

        // Validate files strictly
        $fileErrors = [];
        foreach (['file_ktp', 'file_npwp', 'file_desain'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $errors = FileValidationService::validateFile($request->file($fileField));
                if (!empty($errors)) {
                    $fileErrors[$fileField] = implode(', ', $errors);
                }
            }
        }

        if (!empty($fileErrors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors($fileErrors);
        }

        $documentFileErrors = [];
        $documentsInput = $request->input('documents', []);
        $documentFiles = $request->file('documents', []);

        foreach ($documentsInput as $index => $documentInput) {
            $file = $documentFiles[$index]['file'] ?? null;

            if ($file && $file->isValid()) {
                $errors = FileValidationService::validateFile($file);
                if (!empty($errors)) {
                    $requirementName = $permohonan->documentRequirements()->find($documentInput['id'] ?? 0)?->jenis_persyaratan ?? 'Dokumen';
                    $documentFileErrors["documents.$index.file"] = "{$requirementName}: " . implode(', ', $errors);
                }
            }
        }

        if (!empty($documentFileErrors)) {
            return redirect()->back()
                ->withInput()
                ->withErrors($documentFileErrors);
        }

        foreach (['file_ktp', 'file_npwp', 'file_desain'] as $fileField) {
            $removeField = 'remove_' . $fileField;

            if ($request->boolean($removeField) && $permohonan->$fileField) {
                if (Storage::disk('public')->exists($permohonan->$fileField)) {
                    Storage::disk('public')->delete($permohonan->$fileField);
                }

                $validated[$fileField] = null;
            }
        }

        // Handle file uploads dengan secure filename
        if ($request->hasFile('file_ktp')) {
            $file = $request->file('file_ktp');
            if ($permohonan->file_ktp && Storage::disk('public')->exists($permohonan->file_ktp)) {
                Storage::disk('public')->delete($permohonan->file_ktp);
            }
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/ktp', $filename, 'public');
            $validated['file_ktp'] = $path;
        }
        if ($request->hasFile('file_npwp')) {
            $file = $request->file('file_npwp');
            if ($permohonan->file_npwp && Storage::disk('public')->exists($permohonan->file_npwp)) {
                Storage::disk('public')->delete($permohonan->file_npwp);
            }
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/npwp', $filename, 'public');
            $validated['file_npwp'] = $path;
        }
        if ($request->hasFile('file_desain')) {
            $file = $request->file('file_desain');
            if ($permohonan->file_desain && Storage::disk('public')->exists($permohonan->file_desain)) {
                Storage::disk('public')->delete($permohonan->file_desain);
            }
            $filename = FileValidationService::generateSecureFilename($file);
            $path = $file->storeAs('dokumen/desain', $filename, 'public');
            $validated['file_desain'] = $path;
        }

        unset($validated['documents']);
        $permohonan->update($validated);

        foreach ($documentsInput as $index => $documentInput) {
            $requirement = $permohonan->documentRequirements()->find($documentInput['id'] ?? null);
            $file = $documentFiles[$index]['file'] ?? null;

            if (!$requirement || !$file || !$file->isValid()) {
                continue;
            }

            if ($requirement->file_dokumen && Storage::disk('private')->exists($requirement->file_dokumen)) {
                Storage::disk('private')->delete($requirement->file_dokumen);
            }

            $filePath = $file->store('documents/' . $permohonan->id, 'private');

            if ($filePath) {
                $requirement->update([
                    'file_dokumen' => $filePath,
                    'status' => 'Belum Lengkap',
                ]);
            }
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Memperbarui permohonan reklame: {$permohonan->nomor_registrasi}",
            'old_values' => $oldValues,
            'new_values' => $permohonan->getAttributes(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('permohonan.show', $permohonan)
            ->with('success', 'Permohonan berhasil diperbarui');
    }

    /**
     * Submit permohonan (change status to Diajukan).
     * Jika permohonan sebelumnya ditolak, berubah status ke "Revisi Menunggu Verifikasi"
     */
    public function submit(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Check authorization
        if ($permohonan->user_id !== auth()->id()) {
            abort(403);
        }

        // Logic untuk handle Draft dan Revisi
        if ($permohonan->status === 'Draft') {
            $newStatus = 'Diajukan';
        } elseif (str_contains($permohonan->status, 'Ditolak')) {
            // Jika sebelumnya ditolak dan pemohon revisi, tentukan status berdasarkan siapa yang menolak
            // Gunakan method getNextRevisionStatus() untuk routing revisi ke petugas yang tepat
            $newStatus = $permohonan->getNextRevisionStatus();
        } else {
            // Status tidak bisa di-submit jika sudah final (disetujui)
            abort(403, 'Permohonan tidak dapat diajukan pada status: ' . $permohonan->status);
        }

        $permohonan->update([
            'status' => $newStatus,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'SUBMIT',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Mengajukan permohonan reklame: {$permohonan->nomor_registrasi} (Status: {$newStatus})",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Dispatch event untuk notifikasi ke staff
        PermohonanDiajukan::dispatch($permohonan);

        return redirect()->route('permohonan.show', $permohonan)
            ->with('success', 'Permohonan berhasil diajukan');
    }

    /**
     * Delete permohonan (soft delete).
     */
    public function destroy(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Check authorization - only user or admin can delete
        if ($permohonan->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        if ($permohonan->status !== 'Draft') {
            abort(403, 'Hanya permohonan dalam status Draft yang dapat dihapus');
        }

        $permohonan->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Menghapus permohonan reklame: {$permohonan->nomor_registrasi}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus');
    }

    /**
     * Delete expired permohonan from map manually by operator.
     */
    public function destroyExpiredByOperator(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        if (!auth()->user()->hasRole('operator')) {
            abort(403, 'Hanya operator yang dapat menghapus data reklame kedaluwarsa dari peta');
        }

        if (!$permohonan->canBeDeletedByOperator()) {
            abort(403, 'Hanya reklame yang sudah disetujui Kepala Bidang dan masa berlakunya habis yang dapat dihapus manual');
        }

        $permohonan->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE_EXPIRED_REKLAME',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Operator menghapus reklame kedaluwarsa dari peta: {$permohonan->nomor_registrasi}",
            'old_values' => [
                'status' => $permohonan->status,
                'tanggal_berakhir' => optional($permohonan->tanggal_berakhir)->format('Y-m-d'),
                'status_kedaluwarsa' => $permohonan->getStatusKedaluarsa(),
            ],
            'new_values' => ['deleted_at' => now()->toDateTimeString()],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('permohonan.peta')->with('success', 'Data reklame kedaluwarsa berhasil dihapus manual oleh operator');
    }


    /**
     * Download dokumen file.
     */
    public function downloadFile(PermohonanReklame $permohonan, string $fileType): StreamedResponse
    {
        // Check authorization
        if ($permohonan->user_id !== auth()->id()) {
            if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
                abort(403, 'Anda tidak memiliki akses untuk download file ini');
            }
        }

        // Validate file type
        $fileTypes = ['ktp' => 'file_ktp', 'npwp' => 'file_npwp', 'desain' => 'file_desain'];
        if (!array_key_exists($fileType, $fileTypes)) {
            abort(404, 'Tipe file tidak dikenali');
        }

        $fileAttribute = $fileTypes[$fileType];
        if (!$permohonan->$fileAttribute) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = $permohonan->$fileAttribute;
        
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak tersedia di storage');
        }

        return Storage::disk('public')->download($filePath);
    }

    /**
     * Display peta digital (GIS).
     * Only accessible by staff (admin, operator, kepala_seksi, kepala_bidang)
     */
    public function peta(): View
    {
        // Check authorization - only staff can access
        if (!auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman peta reklame');
        }

        $permohonan = PermohonanReklame::whereIn('status', ['Disetujui Kepala Bidang', 'Sudah Terbit'])
            ->where(function ($query) {
                $query->whereNull('status_kedaluwarsa')
                    ->orWhere('status_kedaluwarsa', '!=', 'Dicabut');
            })
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with([
                'user',
                'persyaratanDokumen' => function ($q) {
                    $q->where('jenis_persyaratan', PersyaratanDokumen::JENIS_FOTO_KONDISI_REKLAME);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('permohonan.peta', compact('permohonan'));
    }
}
