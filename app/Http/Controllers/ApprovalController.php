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

        $persyaratan = $permohonan->persyaratanDokumen()->get();

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
            'persyaratan.*.jenis_persyaratan' => 'sometimes|string',
            'persyaratan.*.is_lengkap' => 'sometimes|boolean',
            'persyaratan.*.keterangan' => 'nullable|string',
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
        ]);

        // Update persyaratan dokumen
        if (isset($validated['persyaratan'])) {
            foreach ($validated['persyaratan'] as $id => $data) {
                $permohonan->persyaratanDokumen()
                    ->where('id', $id)
                    ->update([
                        'is_lengkap' => $data['is_lengkap'] ?? false,
                        'keterangan' => $data['keterangan'] ?? null,
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
            'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan']],
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
     * Show approval form for Kepala Seksi.
     */
    public function approveKepalaSeksi(PermohonanReklame $permohonan): View
    {
        if (!auth()->user()->hasRole('kepala_seksi')) {
            abort(403, 'Hanya Kepala Seksi yang dapat mengakses halaman ini');
        }

        return view('approval.approve-kepala-seksi', compact('permohonan'));
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
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
        ]);

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
        // Temporarily allow anyone logged in to see this page for testing
        // if (!auth()->user()->hasAnyRole(['kepala_bidang', 'admin', 'operator', 'kepala_seksi'])) {
        //     abort(403, 'Hanya staff yang dapat mengakses halaman ini');
        // }

        return view('approval.approve-kepala-bidang', compact('permohonan'));
    }

    /**
     * Store Kepala Bidang approval (final).
     */
    public function storeKepalaBidangApproval(Request $request, PermohonanReklame $permohonan): RedirectResponse
    {
        // Relax check temporarily - allow any approval status
        // if (!$permohonan->canBeApprovedByKepalaBidang()) {
        //     abort(403);
        // }

        $validated = $request->validate([
            'keputusan' => 'required|in:Disetujui,Ditolak',
            'keterangan' => 'nullable|string',
            'tanggal_berlaku' => 'nullable|date|required_if:keputusan,Disetujui',
            'tanggal_berakhir' => 'nullable|date|after:tanggal_berlaku|required_if:keputusan,Disetujui',
        ]);

        $newStatus = $validated['keputusan'] === 'Disetujui' ? 'Disetujui Kepala Bidang' : 'Ditolak Kepala Bidang';
        $oldStatus = $permohonan->status;

        if ($validated['keputusan'] === 'Ditolak') {
            $permohonan->keterangan_penolakan = $validated['keterangan'] ?? null;
            $permohonan->rejected_by_role_id = auth()->user()->role_id;
            $permohonan->rejected_by_user_id = auth()->id();
        } else {
            // Set tanggal berlaku & berakhir jika disetujui
            $permohonan->tanggal_berlaku = $validated['tanggal_berlaku'];
            $permohonan->tanggal_berakhir = $validated['tanggal_berakhir'];
            $permohonan->status_kedaluwarsa = 'Aktif';
            
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
            'new_values' => ['status' => $newStatus, 'keputusan' => $validated['keputusan'], 'tanggal_berlaku' => $validated['tanggal_berlaku'] ?? null, 'tanggal_berakhir' => $validated['tanggal_berakhir'] ?? null],
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
            if (auth()->user()->hasRole('operator')) {
                return redirect()->route('print.surat', $permohonan)
                    ->with('success', 'Permohonan berhasil disetujui FINAL. Surat persetujuan sudah siap dicetak.');
            } else {
                return redirect()->route('approval.dashboard')
                    ->with('success', 'Permohonan berhasil disetujui FINAL. Operator akan mencetak surat persetujuan.');
            }
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
    public function dashboard(): View
    {
        $user = auth()->user();
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        $userRole = $user->role?->slug;

        // Check if user has proper role
        if (!$userRole || !in_array($userRole, ['operator', 'kepala_seksi', 'kepala_bidang', 'admin'])) {
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
        } elseif ($userRole === 'admin') {
            // Admin can see everything
            $query->whereNotNull('status');
        }

        $permohonan = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistics
        $totalPermohonan = $query->count();
        $disetujui = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();
        $ditolak = PermohonanReklame::whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi'])->count();

        return view('approval.dashboard', compact('permohonan', 'userRole', 'totalPermohonan', 'disetujui', 'ditolak'));
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
