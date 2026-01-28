<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role.
     */
    public function index(): View
    {
        $userRole = auth()->user()->role?->slug;

        if ($userRole === 'pemohon') {
            return $this->pemohonDashboard();
        } elseif ($userRole === 'operator') {
            return $this->operatorDashboard();
        } elseif ($userRole === 'kepala_seksi') {
            return $this->kepalaSeksiDashboard();
        } elseif ($userRole === 'kepala_bidang') {
            return $this->kepalaBidangDashboard();
        } elseif ($userRole === 'admin') {
            return $this->adminDashboard();
        }

        abort(403, 'Role tidak dikenali');
    }

    /**
     * Pemohon dashboard.
     */
    private function pemohonDashboard(): View
    {
        // Optimize queries dengan single query
        $user = auth()->user();
        
        $totalPermohonan = $user->permohonanReklame()->count();
        $draft = $user->permohonanReklame()->where('status', 'Draft')->count();
        $diajukan = $user->permohonanReklame()->where('status', 'Diajukan')->count();
        $revisi = $user->permohonanReklame()->where('status', 'Revisi Menunggu Verifikasi')->count();
        $disetujui = $user->permohonanReklame()->where('status', 'Disetujui Kepala Bidang')->count();
        $ditolak = $user->permohonanReklame()
            ->whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang'])
            ->count();

        // Use eager loading untuk get related user data
        $recentPermohonan = $user->permohonanReklame()
            ->with('user') // Eager load user relation
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.pemohon', compact(
            'totalPermohonan',
            'draft',
            'diajukan',
            'revisi',
            'disetujui',
            'ditolak',
            'recentPermohonan'
        ));
    }

    /**
     * Operator dashboard.
     */
    private function operatorDashboard(): View
    {
        // Optimize with eager loading dan caching untuk stats
        $totalPermohonan = PermohonanReklame::whereIn('status', ['Diajukan', 'Diverifikasi Operator', 'Revisi Menunggu Verifikasi'])
            ->count();
        $diajukan = PermohonanReklame::where('status', 'Diajukan')->count();
        $diverifikasi = PermohonanReklame::where('status', 'Diverifikasi Operator')->count();
        $revisiMenunggu = PermohonanReklame::where('status', 'Revisi Menunggu Verifikasi')->count();
        $ditolak = PermohonanReklame::where('status', 'Ditolak Operator')->count();

        // Permohonan yang menunggu verifikasi (baru diajukan + revisi)
        $pendingPermohonan = PermohonanReklame::whereIn('status', ['Diajukan', 'Revisi Menunggu Verifikasi'])
            ->with('user') // Eager load user relation
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Permohonan yang sudah direvisi (untuk tab terpisah) dengan eager loading
        $revisiPermohonan = PermohonanReklame::where('status', 'Revisi Menunggu Verifikasi')
            ->with('user') // Eager load user relation
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('dashboard.operator', compact(
            'totalPermohonan',
            'diajukan',
            'diverifikasi',
            'revisiMenunggu',
            'ditolak',
            'pendingPermohonan',
            'revisiPermohonan'
        ));
    }

    /**
     * Kepala Seksi dashboard.
     */
    private function kepalaSeksiDashboard(): View
    {
        $totalPermohonan = PermohonanReklame::where('status', 'Diverifikasi Operator')->count();
        $disetujui = PermohonanReklame::where('status', 'Disetujui Kepala Seksi')->count();
        $ditolak = PermohonanReklame::where('status', 'Ditolak Kepala Seksi')->count();

        $pendingPermohonan = PermohonanReklame::where('status', 'Diverifikasi Operator')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Reklame statistics
        $reklamePermanen = PermohonanReklame::where('jenis_reklame', 'Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();
        $reklameNonPermanen = PermohonanReklame::where('jenis_reklame', 'Non Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();

        return view('dashboard.kepala-seksi', compact(
            'totalPermohonan',
            'disetujui',
            'ditolak',
            'pendingPermohonan',
            'reklamePermanen',
            'reklameNonPermanen'
        ));
    }

    /**
     * Kepala Bidang dashboard.
     */
    private function kepalaBidangDashboard(): View
    {
        $totalPermohonan = PermohonanReklame::where('status', 'Disetujui Kepala Seksi')->count();
        $disetujui = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();

        $pendingPermohonan = PermohonanReklame::where('status', 'Disetujui Kepala Seksi')
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // Reklame statistics
        $reklamePermanen = PermohonanReklame::where('jenis_reklame', 'Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();
        $reklameNonPermanen = PermohonanReklame::where('jenis_reklame', 'Non Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();

        return view('dashboard.kepala-bidang', compact(
            'totalPermohonan',
            'disetujui',
            'pendingPermohonan',
            'reklamePermanen',
            'reklameNonPermanen'
        ));
    }

    /**
     * Admin dashboard.
     */
    private function adminDashboard(): View
    {
        $totalUsers = User::count();
        $totalPermohonan = PermohonanReklame::count();
        $totalDisetujui = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();
        $totalDitolak = PermohonanReklame::whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi'])->count();

        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $permohonanByStatus = PermohonanReklame::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status');

        return view('dashboard.admin', compact(
            'totalUsers',
            'totalPermohonan',
            'totalDisetujui',
            'totalDitolak',
            'recentActivities',
            'permohonanByStatus'
        ));
    }

    /**
     * Show reklame chart (Kepala Seksi & Kepala Bidang only).
     */
    public function reklameChart(): View
    {
        $userRole = auth()->user()->role?->slug;
        
        if (!in_array($userRole, ['kepala_seksi', 'kepala_bidang'])) {
            abort(403, 'Hanya Kepala Seksi dan Kepala Bidang yang dapat mengakses halaman ini');
        }

        // Data reklame yang sudah disetujui
        $reklamePermanen = PermohonanReklame::where('jenis_reklame', 'Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();
        
        $reklameNonPermanen = PermohonanReklame::where('jenis_reklame', 'Non Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->count();

        // Detail reklame permanen
        $reklamePermananDetail = PermohonanReklame::where('jenis_reklame', 'Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->selectRaw('jenis_reklame, COUNT(*) as total')
            ->groupBy('jenis_reklame')
            ->get();

        // Detail reklame non permanen
        $reklameNonPermananDetail = PermohonanReklame::where('jenis_reklame', 'Non Permanen')
            ->where('status', 'Disetujui Kepala Bidang')
            ->selectRaw('jenis_reklame, COUNT(*) as total')
            ->groupBy('jenis_reklame')
            ->get();

        // Reklame by location (top 10)
        $reklameByLocation = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')
            ->selectRaw('lokasi_pemasangan, COUNT(*) as total')
            ->groupBy('lokasi_pemasangan')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        // Total approved
        $totalApproved = PermohonanReklame::where('status', 'Disetujui Kepala Bidang')->count();

        return view('dashboard.reklame-chart', compact(
            'reklamePermanen',
            'reklameNonPermanen',
            'totalApproved',
            'reklameByLocation'
        ));
    }
}
