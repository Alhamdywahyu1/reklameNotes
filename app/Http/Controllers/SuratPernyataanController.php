<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\SuratPernyataan;
use App\Services\FileValidationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class SuratPernyataanController extends Controller
{
    public function __construct(private FileValidationService $fileValidationService)
    {
    }

    /**
     * Show form untuk membuat/edit Surat Pernyataan
     */
    public function create(PermohonanReklame $permohonan): View
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if Surat Pernyataan already exists
        $suratPernyataan = $permohonan->suratPernyataan;
        
        // Pre-fill data dari permohonan
        if (!$suratPernyataan) {
            $suratPernyataan = new SuratPernyataan([
                'nama_pemohon' => $permohonan->nama_pemohon,
                'alamat_pemohon' => $permohonan->alamat_pemohon,
                'no_ktp' => $permohonan->nik,
            ]);
        }

        return view('surat-pernyataan.create', compact('permohonan', 'suratPernyataan'));
    }

    /**
     * Alias untuk create - digunakan untuk form step 3
     */
    public function editStep3(PermohonanReklame $permohonan): View
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if form_step is minimum 2
        if ($permohonan->form_step < 2) {
            abort(403, 'Anda harus menyelesaikan step 2 terlebih dahulu');
        }

        // Check if Surat Pernyataan already exists
        $suratPernyataan = $permohonan->suratPernyataan;
        
        // Pre-fill data dari permohonan
        if (!$suratPernyataan) {
            $suratPernyataan = new SuratPernyataan([
                'nama_pemohon' => $permohonan->nama_pemohon,
                'alamat_pemohon' => $permohonan->alamat_pemohon,
                'no_ktp' => $permohonan->nik,
            ]);
        }

        return view('surat-pernyataan.create', compact('permohonan', 'suratPernyataan'));
    }

    /**
     * Store Surat Pernyataan
     */
    public function store(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'alamat_pemohon' => 'required|string',
            'no_ktp' => 'required|string|max:20',
            'setuju_syarat_1' => 'accepted',
            'setuju_syarat_2' => 'accepted',
            'setuju_syarat_3' => 'accepted',
            'setuju_syarat_4' => 'accepted',
            'setuju_syarat_5' => 'accepted',
            'setuju_syarat_6' => 'accepted',
            'setuju_syarat_7' => 'accepted',
            'setuju_syarat_8' => 'accepted',
            'tanggal_pernyataan' => 'required|date',
            'file_tanda_tangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_materai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'setuju_syarat_1.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_2.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_3.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_4.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_5.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_6.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_7.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
            'setuju_syarat_8.accepted' => 'Anda harus menyetujui semua syarat dan ketentuan',
        ]);

        // Handle file uploads
        if ($request->hasFile('file_tanda_tangan')) {
            $validated['file_tanda_tangan'] = $this->storeFile(
                $request->file('file_tanda_tangan'),
                'surat-pernyataan/tanda-tangan',
                $permohonan->id
            );
        }

        if ($request->hasFile('file_materai')) {
            $validated['file_materai'] = $this->storeFile(
                $request->file('file_materai'),
                'surat-pernyataan/materai',
                $permohonan->id
            );
        }

        // Create or update Surat Pernyataan
        $suratPernyataan = SuratPernyataan::updateOrCreate(
            ['permohonan_id' => $permohonan->id],
            array_merge($validated, [
                'user_id' => auth()->id(),
                'status' => 'submitted',
                'submitted_at' => now(),
            ])
        );

        return redirect()
            ->route('permohonan.show', $permohonan)
            ->with('success', 'Surat Pernyataan berhasil disimpan');
    }

    /**
     * Show Surat Pernyataan
     */
    public function show(PermohonanReklame $permohonan): View
    {
        $suratPernyataan = $permohonan->suratPernyataan;

        if (!$suratPernyataan) {
            abort(404, 'Surat Pernyataan tidak ditemukan');
        }

        // Check authorization
        if (auth()->id() !== $permohonan->user_id && auth()->user()->role !== 'operator' && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        return view('surat-pernyataan.show', compact('permohonan', 'suratPernyataan'));
    }

    /**
     * Edit Surat Pernyataan
     */
    public function edit(PermohonanReklame $permohonan): View
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $suratPernyataan = $permohonan->suratPernyataan;

        if (!$suratPernyataan) {
            abort(404, 'Surat Pernyataan tidak ditemukan');
        }

        return view('surat-pernyataan.edit', compact('permohonan', 'suratPernyataan'));
    }

    /**
     * Update Surat Pernyataan
     */
    public function update(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $suratPernyataan = $permohonan->suratPernyataan;

        if (!$suratPernyataan) {
            abort(404, 'Surat Pernyataan tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'alamat_pemohon' => 'required|string',
            'no_ktp' => 'required|string|max:20',
            'setuju_syarat_1' => 'accepted',
            'setuju_syarat_2' => 'accepted',
            'setuju_syarat_3' => 'accepted',
            'setuju_syarat_4' => 'accepted',
            'setuju_syarat_5' => 'accepted',
            'setuju_syarat_6' => 'accepted',
            'setuju_syarat_7' => 'accepted',
            'setuju_syarat_8' => 'accepted',
            'tanggal_pernyataan' => 'required|date',
            'file_tanda_tangan' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'file_materai' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file uploads
        if ($request->hasFile('file_tanda_tangan')) {
            // Delete old file if exists
            if ($suratPernyataan->file_tanda_tangan && Storage::exists($suratPernyataan->file_tanda_tangan)) {
                Storage::delete($suratPernyataan->file_tanda_tangan);
            }
            $validated['file_tanda_tangan'] = $this->storeFile(
                $request->file('file_tanda_tangan'),
                'surat-pernyataan/tanda-tangan',
                $permohonan->id
            );
        }

        if ($request->hasFile('file_materai')) {
            // Delete old file if exists
            if ($suratPernyataan->file_materai && Storage::exists($suratPernyataan->file_materai)) {
                Storage::delete($suratPernyataan->file_materai);
            }
            $validated['file_materai'] = $this->storeFile(
                $request->file('file_materai'),
                'surat-pernyataan/materai',
                $permohonan->id
            );
        }

        $suratPernyataan->update($validated);

        return redirect()
            ->route('surat-pernyataan.show', $permohonan)
            ->with('success', 'Surat Pernyataan berhasil diperbarui');
    }

    /**
     * Download Surat Pernyataan PDF
     */
    public function downloadPdf(PermohonanReklame $permohonan)
    {
        $suratPernyataan = $permohonan->suratPernyataan;

        if (!$suratPernyataan) {
            abort(404, 'Surat Pernyataan tidak ditemukan');
        }

        // Check authorization
        if (auth()->id() !== $permohonan->user_id && auth()->user()->role !== 'operator' && auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Generate PDF using DomPDF
        $pdf = \PDF::loadView('surat-pernyataan.pdf', compact('permohonan', 'suratPernyataan'));
        return $pdf->download('Surat_Pernyataan_' . $permohonan->nomor_registrasi . '.pdf');
    }

    /**
     * Delete Surat Pernyataan
     */
    public function destroy(PermohonanReklame $permohonan): RedirectResponse
    {
        // Verify bahwa user adalah pemilik permohonan
        if ($permohonan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $suratPernyataan = $permohonan->suratPernyataan;

        if (!$suratPernyataan) {
            abort(404, 'Surat Pernyataan tidak ditemukan');
        }

        // Delete files
        if ($suratPernyataan->file_tanda_tangan && Storage::exists($suratPernyataan->file_tanda_tangan)) {
            Storage::delete($suratPernyataan->file_tanda_tangan);
        }
        if ($suratPernyataan->file_materai && Storage::exists($suratPernyataan->file_materai)) {
            Storage::delete($suratPernyataan->file_materai);
        }

        $suratPernyataan->delete();

        return redirect()
            ->route('permohonan.show', $permohonan)
            ->with('success', 'Surat Pernyataan berhasil dihapus');
    }

    /**
     * Store file dengan naming convention
     */
    private function storeFile($file, $path, $permohonanId)
    {
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($path, $fileName, 'public');
    }
}
