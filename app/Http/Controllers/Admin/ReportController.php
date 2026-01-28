<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermohonanReklame;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = PermohonanReklame::with('user');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('jenis_reklame')) {
            $query->where('jenis_reklame', $request->jenis_reklame);
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats for summary
        $stats = [
            'total' => $query->count(),
            'disetujui' => (clone $query)->where('status', 'Disetujui Kepala Bidang')->count(),
            'ditolak' => (clone $query)->whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang'])->count(),
            'pending' => (clone $query)->whereNotIn('status', ['Disetujui Kepala Bidang', 'Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang'])->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }
}
