<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PemohonExport;
use App\Http\Controllers\Controller;
use App\Models\PermohonanReklame;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = PermohonanReklame::query();

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis_reklame')) {
            $query->where('jenis_reklame', $request->jenis_reklame);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildFilteredQuery($request);

        $reports = (clone $query)->with('user')->orderBy('created_at', 'desc')->paginate(20);

        // Stats for summary
        $stats = [
            'total' => $query->count(),
            'disetujui' => (clone $query)->where('status', 'Disetujui Kepala Bidang')->count(),
            'ditolak' => (clone $query)->whereIn('status', ['Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang'])->count(),
            'pending' => (clone $query)->whereNotIn('status', ['Disetujui Kepala Bidang', 'Ditolak Operator', 'Ditolak Kepala Seksi', 'Ditolak Kepala Bidang'])->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    public function exportPemohon(Request $request)
    {
        $filename = 'data_pemohon_reklame_' . now()->format('Ymd_His') . '.xlsx';
        $query = $this->buildFilteredQuery($request);

        return Excel::download(new PemohonExport($query), $filename);
    }
}
