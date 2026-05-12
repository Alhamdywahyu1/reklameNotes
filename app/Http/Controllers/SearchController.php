<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Search permohonan by nomor_registrasi or NIK.
     */
    public function search(Request $request): View
    {
        $query = $request->query('q', '');
        $permohonan = [];
        
        if (!empty($query)) {
            $permohonan = PermohonanReklame::where(function ($q) use ($query) {
                $q->where('nomor_registrasi', 'like', "%{$query}%")
                  ->orWhere('nik', 'like', "%{$query}%")
                  ->orWhere('nama_pemohon', 'like', "%{$query}%");
            })
            ->when(!Auth::user()->hasRole('pemohon'), function ($q) {
                // Staff dapat melihat semua permohonan
                return $q;
            }, function ($q) {
                // Pemohon hanya lihat milik sendiri
                return $q->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        }

        return view('search.results', compact('permohonan', 'query'));
    }

    /**
     * Quick search API for navbar.
     */
    public function quickSearch(Request $request)
    {
        $query = $request->query('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = PermohonanReklame::where(function ($q) use ($query) {
            $q->where('nomor_registrasi', 'like', "%{$query}%")
              ->orWhere('nik', 'like', "%{$query}%")
              ->orWhere('nama_pemohon', 'like', "%{$query}%");
        })
        ->when(!Auth::user()->hasRole('pemohon'), function ($q) {
            return $q;
        }, function ($q) {
            return $q->where('user_id', Auth::id());
        })
        ->limit(10)
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'nomor_registrasi' => $item->nomor_registrasi,
                'nama_pemohon' => $item->nama_pemohon,
                'nik' => $item->nik,
                'status' => $item->status,
                'url' => route('permohonan.show', $item),
            ];
        });

        return response()->json($results);
    }
}
