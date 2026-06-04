<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\ApprovalWorkflow;
use App\Models\ActivityLog;
use App\Events\StatusBerubah;
use App\Events\PermohonanDitolak;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ApprovalController extends Controller
{
    /**
     * Show verification form for operator.
     */
    public function verifyOperator(PermohonanReklame $permohonan): View
    {
        // Only operator can access
        if (!auth()->user()->hasRole('operator')) {
            abort(403, 'Hanya operator yang dapat mengakses halaman ini');
        }

        // Filter: tampilkan dokumen wajib, atau dokumen opsional yang sudah ada file
        $persyaratan = $permohonan->persyaratanDokumen()
            ->where(function ($query) {
                $query->where('is_optional', false)
                      ->orWhere(function ($q) {
                          $q->where('is_optional', true)
                            ->whereNotNull('file_dokumen');
                      });
            })
            ->get();

        return view('approval.verify-operator', compact('permohonan', 'persyaratan'));
    }

    /**
     * Store operator verification.
     */
    public function storeOperatorVerification(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        if (!auth()->user()->hasRole('operator')) {
            abort(403, 'Hanya operator yang dapat mengakses halaman ini');
        }

        if (!$permohonan->canBeApprovedByOperator()) {
            return redirect()->route('approval.dashboard')
                ->with('error', 'Permohonan tidak dapat diproses. Status saat ini: ' . $permohonan->status);
        }

        $validated = $request->validate([
            'persyaratan.*.status' => 'sometimes|in:Lengkap,Belum Lengkap',
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
            'tanggal_berlaku' => 'nullable|date|required_if:keputusan,Disetujui',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku|required_if:keputusan,Disetujui',
        ]);

        // Update persyaratan dokumen status
        if (isset($validated['persyaratan'])) {
            foreach ($validated['persyaratan'] as $id => $data) {
                $permohonan->persyaratanDokumen()
                    ->where('id', $id)
                    ->update([
                        'status' => $data['status'] ?? 'Belum Lengkap',
                        'is_lengkap' => $data['status'] === 'Lengkap' ? true : false,
                    ]);
            }
        }

        // Determine new status
        $newStatus = $validated['keputusan'] === 'Disetujui' ? 'Diverifikasi Operator' : 'Ditolak Operator';
        $oldStatus = $permohonan->status;

        if ($validated['keputusan'] === 'Ditolak') {
            $permohonan->keterangan_penolakan = $validated['keterangan'] ?? null;
            // Track siapa yang menolak untuk routing revisi ke petugas yang tepat
            $permohonan->rejected_by_role_id = auth()->user()->role_id;
            $permohonan->rejected_by_user_id = auth()->id();
        } else {
            $permohonan->tanggal_berlaku = $validated['tanggal_berlaku'];
            $permohonan->tanggal_berakhir = $validated['tanggal_berakhir'];
            $permohonan->status_kedaluwarsa = 'Aktif';
            $permohonan->expiry_reminder_sent_at = null;
            $permohonan->expiry_reminder_h3_sent_at = null;
            // Clear rejection tracking ketika approved
            $permohonan->rejected_by_role_id = null;
            $permohonan->rejected_by_user_id = null;
        }

        $permohonan->status = $newStatus;
        $permohonan->save();

        // Record approval workflow
        ApprovalWorkflow::create([
            'permohonan_id' => $permohonan->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'status_approval' => 'Diverifikasi Operator',
            'keputusan' => $validated['keputusan'],
            'keterangan' => $validated['keterangan'] ?? null,
            'tanggal_approval' => now(),
        ]);

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'APPROVAL_OPERATOR',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Verifikasi operator permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
            'old_values' => ['status' => $oldStatus],
            'new_values' => [
                'status' => $newStatus,
                'keputusan' => $validated['keputusan'],
                'tanggal_berlaku' => $validated['tanggal_berlaku'] ?? null,
                'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Dispatch events
        StatusBerubah::dispatch($permohonan, $oldStatus, $newStatus);

        if ($validated['keputusan'] === 'Ditolak') {
            PermohonanDitolak::dispatch($permohonan, $validated['keterangan'] ?? '', 'Operator');
        }

        $message = $validated['keputusan'] === 'Disetujui'
            ? 'Permohonan berhasil diverifikasi'
            : 'Permohonan ditolak dan dikembalikan kepada pemohon';

        return redirect()->route('approval.dashboard')
            ->with('success', $message);
    }

    /**
     * Auto-save persyaratan status via AJAX.
     */
    public function savePersyaratanStatus(Request $request, PermohonanReklame $permohonan)
    {
        $isOperator = auth()->user()->hasRole('operator');
        $isKepalaSeksi = auth()->user()->hasRole('kepala_seksi');
        $isKepalaBidang = auth()->user()->hasRole('kepala_bidang');

        if (!$isOperator && !$isKepalaSeksi && !$isKepalaBidang) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'persyaratan_id' => 'required|exists:persyaratan_dokumen,id',
            'status' => 'required|in:Lengkap,Belum Lengkap',
        ]);

        try {
            $permohonan->persyaratanDokumen()
                ->where('id', $validated['persyaratan_id'])
                ->update([
                    'status' => $validated['status'],
                    'is_lengkap' => $validated['status'] === 'Lengkap' ? true : false,
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Status dokumen berhasil disimpan',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Gagal menyimpan status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show approval form for Kepala Seksi.
     */
    public function approveKepalaSeksi(PermohonanReklame $permohonan): View
    {
        if (!auth()->user()->hasRole('kepala_seksi')) {
            abort(403, 'Hanya Kepala Seksi yang dapat mengakses halaman ini');
        }

        // Filter: tampilkan dokumen wajib, atau dokumen opsional yang sudah ada file
        $persyaratan = $permohonan->persyaratanDokumen()
            ->where(function ($query) {
                $query->where('is_optional', false)
                      ->orWhere(function ($q) {
                          $q->where('is_optional', true)
                            ->whereNotNull('file_dokumen');
                      });
            })
            ->get();

        return view('approval.approve-kepala-seksi', compact('permohonan', 'persyaratan'));
    }

    /**
     * Store Kepala Seksi approval.
     */
    public function storeKepalaSeksiApproval(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        if (!auth()->user()->hasRole('kepala_seksi')) {
            abort(403, 'Hanya Kepala Seksi yang dapat mengakses halaman ini');
        }

        if (!$permohonan->canBeApprovedByKepalaSeksi()) {
            return redirect()->route('approval.dashboard')
                ->with('error', 'Permohonan tidak dapat diproses. Status saat ini: ' . $permohonan->status);
        }

        $validated = $request->validate([
            'persyaratan.*.status' => 'sometimes|in:Lengkap,Belum Lengkap',
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
        ]);

        if (
            $validated['keputusan'] === 'Disetujui'
            && (
                !$permohonan->tanggal_berlaku
                || !$permohonan->tanggal_berakhir
                || $permohonan->tanggal_berakhir->lte($permohonan->tanggal_berlaku)
            )
        ) {
            return back()->withErrors([
                'keputusan' => 'Masa berlaku harus diatur valid oleh Operator terlebih dahulu sebelum disetujui Kepala Seksi.',
            ])->withInput();
        }

        // Samakan dengan operator: simpan status dokumen dari form saat submit
        if (isset($validated['persyaratan'])) {
            foreach ($validated['persyaratan'] as $id => $data) {
                $permohonan->persyaratanDokumen()
                    ->where('id', $id)
                    ->update([
                        'status' => $data['status'] ?? 'Belum Lengkap',
                        'is_lengkap' => ($data['status'] ?? '') === 'Lengkap',
                    ]);
            }
        }

        $newStatus = $validated['keputusan'] === 'Disetujui' ? 'Disetujui Kepala Seksi' : 'Ditolak Kepala Seksi';
        $oldStatus = $permohonan->status;

        if ($validated['keputusan'] === 'Ditolak') {
            $permohonan->keterangan_penolakan = $validated['keterangan'] ?? null;
            // Track siapa yang menolak untuk routing revisi ke petugas yang tepat
            $permohonan->rejected_by_role_id = auth()->user()->role_id;
            $permohonan->rejected_by_user_id = auth()->id();
        } else {
            // Clear rejection tracking ketika approved
            $permohonan->rejected_by_role_id = null;
            $permohonan->rejected_by_user_id = null;
        }

        $permohonan->status = $newStatus;
        $permohonan->save();

        ApprovalWorkflow::create([
            'permohonan_id' => $permohonan->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'status_approval' => 'Disetujui Kepala Seksi',
            'keputusan' => $validated['keputusan'],
            'keterangan' => $validated['keterangan'] ?? null,
            'tanggal_approval' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'APPROVAL_KEPALA_SEKSI',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Approval Kepala Seksi permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
            'old_values' => ['status' => $oldStatus],
            'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan']],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Dispatch events
        StatusBerubah::dispatch($permohonan, $oldStatus, $newStatus);

        if ($validated['keputusan'] === 'Ditolak') {
            PermohonanDitolak::dispatch($permohonan, $validated['keterangan'] ?? '', 'Kepala Seksi');
        }

        $message = $validated['keputusan'] === 'Disetujui'
            ? 'Permohonan disetujui oleh Kepala Seksi'
            : 'Permohonan ditolak dan dikembalikan kepada pemohon';

        return redirect()->route('approval.dashboard')
            ->with('success', $message);
    }

    /**
     * Show approval form for Kepala Bidang.
     */
    public function approveKepalaBidang(PermohonanReklame $permohonan): View
    {
        if (!auth()->user()->hasRole('kepala_bidang')) {
            abort(403, 'Hanya Kepala Bidang yang dapat mengakses halaman ini');
        }

        $persyaratan = $permohonan->persyaratanDokumen()
            ->where(function ($query) {
                $query->where('is_optional', false)
                      ->orWhere(function ($q) {
                          $q->where('is_optional', true)
                            ->whereNotNull('file_dokumen');
                      });
            })
            ->get();

        return view('approval.approve-kepala-bidang', compact('permohonan', 'persyaratan'));
    }

    /**
     * Store Kepala Bidang approval (final).
     */
    public function storeKepalaBidangApproval(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        if (!auth()->user()->hasRole('kepala_bidang')) {
            abort(403, 'Hanya Kepala Bidang yang dapat mengakses halaman ini');
        }

        if (!$permohonan->canBeApprovedByKepalaBidang()) {
            return redirect()->route('approval.dashboard')
                ->with('error', 'Permohonan tidak dapat diproses. Status saat ini: ' . $permohonan->status);
        }

        $validated = $request->validate([
            'persyaratan.*.status' => 'sometimes|in:Lengkap,Belum Lengkap',
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
        ]);

        if (
            $validated['keputusan'] === 'Disetujui'
            && (
                !$permohonan->tanggal_berlaku
                || !$permohonan->tanggal_berakhir
                || $permohonan->tanggal_berakhir->lte($permohonan->tanggal_berlaku)
            )
        ) {
            return back()->withErrors([
                'keputusan' => 'Masa berlaku dari Operator belum valid. Periksa kembali sebelum approval final.',
            ])->withInput();
        }

        if (isset($validated['persyaratan'])) {
            foreach ($validated['persyaratan'] as $id => $data) {
                $permohonan->persyaratanDokumen()
                    ->where('id', $id)
                    ->update([
                        'status' => $data['status'] ?? 'Belum Lengkap',
                        'is_lengkap' => ($data['status'] ?? '') === 'Lengkap',
                    ]);
            }
        }

        $newStatus = $validated['keputusan'] === 'Disetujui' ? 'Disetujui Kepala Bidang' : 'Ditolak Kepala Bidang';
        $oldStatus = $permohonan->status;

        if ($validated['keputusan'] === 'Ditolak') {
            $permohonan->keterangan_penolakan = $validated['keterangan'] ?? null;
            $permohonan->rejected_by_role_id = auth()->user()->role_id;
            $permohonan->rejected_by_user_id = auth()->id();
        } else {
            // Clear rejection tracking ketika approved
            $permohonan->rejected_by_role_id = null;
            $permohonan->rejected_by_user_id = null;
        }

        $permohonan->status = $newStatus;
        $permohonan->save();

        ApprovalWorkflow::create([
            'permohonan_id' => $permohonan->id,
            'user_id' => auth()->id(),
            'role_id' => auth()->user()->role_id,
            'status_approval' => 'Disetujui Kepala Bidang',
            'keputusan' => $validated['keputusan'],
            'keterangan' => $validated['keterangan'] ?? null,
            'tanggal_approval' => now(),
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'APPROVAL_KEPALA_BIDANG',
            'model_type' => 'PermohonanReklame',
            'model_id' => $permohonan->id,
            'description' => "Final approval Kepala Bidang permohonan {$permohonan->nomor_registrasi}: {$validated['keputusan']}",
            'old_values' => ['status' => $oldStatus],
            'new_values' => [
                'status' => $newStatus,
                'keputusan' => $validated['keputusan'],
                'tanggal_berlaku' => optional($permohonan->tanggal_berlaku)->toDateString(),
                'tanggal_berakhir' => optional($permohonan->tanggal_berakhir)->toDateString(),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        // Dispatch events
        // StatusBerubah::dispatch($permohonan, $oldStatus, $newStatus);

        if ($validated['keputusan'] === 'Ditolak') {
            // PermohonanDitolak::dispatch($permohonan, $validated['keterangan'] ?? '', 'Kepala Bidang');
        }

        if ($validated['keputusan'] === 'Disetujui') {
            // Kalau operator, bisa langsung ke print surat
            // Kalau role lain (kepala_bidang, etc), balik ke approval dashboard
            return redirect()->route('approval.dashboard')
                ->with('success', 'Permohonan berhasil disetujui FINAL. Operator akan mencetak surat persetujuan.');
        } else {
            return redirect()->route('approval.dashboard')
                ->with('success', 'Permohonan ditolak dan akan dikembalikan kepada pemohon');
        }
    }

    /**
     * Show revision list for specific staff role.
     */
    public function revisi(): View
    {
        $user = auth()->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        $userRole = $user->role?->slug;

        // Check if user has proper role for revisions
        if (!$userRole || !in_array($userRole, ['kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Anda tidak memiliki akses ke halaman revisi');
        }

        // Filter permohonan based on role (Hanya list revisi yang menunggu mereka)
        $query = PermohonanReklame::query();

        if ($userRole === 'kepala_seksi') {
            $query->where('status', 'Revisi Menunggu Kepala Seksi');
        } elseif ($userRole === 'kepala_bidang') {
            $query->where('status', 'Revisi Menunggu Kepala Bidang');
        }

        $permohonan = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('approval.revisi', compact('permohonan', 'userRole'));
    }

    /**
     * Show approval dashboard.
     */
    public function dashboard(Request $request): View
    {
        $user = auth()->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        $userRole = $user->role?->slug;

        // Check if user has proper role (exclude admin - they should use admin.dashboard)
        if (!$userRole || !in_array($userRole, ['operator', 'kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Anda tidak memiliki akses ke fitur approval');
        }

        // Filter permohonan based on role
        $query = PermohonanReklame::query();

        if ($userRole === 'operator') {
            $query->whereIn('status', ['Diajukan', 'Revisi Menunggu Operator', 'Diverifikasi Operator', 'Ditolak Operator']);
        } elseif ($userRole === 'kepala_seksi') {
            $query->whereIn('status', ['Revisi Menunggu Kepala Seksi', 'Diverifikasi Operator', 'Disetujui Kepala Seksi', 'Ditolak Kepala Seksi']);
        } elseif ($userRole === 'kepala_bidang') {
            $query->whereIn('status', ['Revisi Menunggu Kepala Bidang', 'Disetujui Kepala Seksi', 'Disetujui Kepala Bidang', 'Ditolak Kepala Bidang']);
        }

        $masaFilter = $request->query('masa_filter', 'all');

        // Counts for quick-filter badges (scoped to current role/status view)
        $baseForCounts = (clone $query);
        $countMissing = (clone $baseForCounts)
            ->where(function ($q) {
                $q->whereNull('tanggal_berlaku')
                  ->orWhereNull('tanggal_berakhir');
            })->count();

        $countInvalid = (clone $baseForCounts)
            ->whereNotNull('tanggal_berlaku')
            ->whereNotNull('tanggal_berakhir')
            ->whereColumn('tanggal_berakhir', '<=', 'tanggal_berlaku')
            ->count();

        if ($masaFilter === 'missing') {
            $query->where(function ($q) {
                $q->whereNull('tanggal_berlaku')
                    ->orWhereNull('tanggal_berakhir');
            });
        } elseif ($masaFilter === 'invalid') {
            $query->whereNotNull('tanggal_berlaku')
                ->whereNotNull('tanggal_berakhir')
                ->whereColumn('tanggal_berakhir', '<=', 'tanggal_berlaku');
        }

        $permohonan = $query->orderBy('created_at', 'desc')->paginate(10);
        $permohonan->appends($request->query());

        // Statistics
        $totalPermohonan = $query->count();
        $disetujui = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();
        $ditolak = PermohonanReklame::whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi'])->count();

        return view('approval.dashboard', compact('permohonan', 'userRole', 'totalPermohonan', 'disetujui', 'ditolak', 'masaFilter', 'countMissing', 'countInvalid'));
    }

    /**
     * Show approval status (disetujui & ditolak) - operator only
     */
    public function approvalStatus(Request $request): View
    {
        // Only operator can access
        if (!auth()->user()->hasRole('operator')) {
            abort(403, 'Hanya operator yang dapat mengakses halaman ini');
        }

        $status = $request->query('status', 'all'); // all, disetujui, ditolak

        $query = PermohonanReklame::query();

        if ($status === 'disetujui') {
            $query->where('status', 'Disetujui Kepala Bidang');
        } elseif ($status === 'ditolak') {
            $query->whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi']);
        }

        $permohonan = $query->orderBy('updated_at', 'desc')->paginate(15);

        // Statistics
        $totalDisetujui = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();
        $totalDitolak = PermohonanReklame::whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi'])->count();
        $totalPending = PermohonanReklame::whereIn('status', ['Diajukan', 'Diverifikasi Operator'])->count();

        return view('approval.status', compact('permohonan', 'status', 'totalDisetujui', 'totalDitolak', 'totalPending'));
    }
}
