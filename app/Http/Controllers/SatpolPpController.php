<?php

namespace App\Http\Controllers;

use App\Models\PermohonanReklame;
use App\Models\PersyaratanDokumen;
use Illuminate\View\View;

class SatpolPpController extends Controller
{
    /**
     * Menampilkan peta sebaran reklame untuk inspeksi Satpol PP.
     */
    public function map(): View
    {
        $reklames = PermohonanReklame::whereIn('status', ['Disetujui Kepala Bidang', 'Sudah Terbit'])
            ->where(function ($query) {
                $query->whereNull('status_kedaluwarsa')
                    ->orWhere('status_kedaluwarsa', '!=', 'Dicabut');
            })
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->with([
                'user',
                'persyaratanDokumen' => function ($query) {
                    $query->where('jenis_persyaratan', PersyaratanDokumen::JENIS_FOTO_KONDISI_REKLAME);
                },
            ])
            ->orderBy('updated_at', 'desc')
            ->get();

        $reklameList = $reklames->map(function (PermohonanReklame $reklame) {
            $fotoRow = $reklame->persyaratanDokumen->first();
            $fotoPreviewUrl = null;
            $fotoKind = null;

            if ($fotoRow && $fotoRow->file_dokumen) {
                $fotoPreviewUrl = route('document-requirements.preview', $fotoRow);
                $ext = strtolower(pathinfo($fotoRow->file_dokumen, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    $fotoKind = 'image';
                } elseif ($ext === 'pdf') {
                    $fotoKind = 'pdf';
                }
            }

            return [
                'id' => $reklame->id,
                'nomor_registrasi' => $reklame->nomor_registrasi,
                'nama_pemilik' => $reklame->nama_pemohon ?: ($reklame->user?->name ?? 'Tidak Diketahui'),
                'jenis_reklame' => $reklame->jenis_reklame,
                'lokasi_pemasangan' => $reklame->lokasi_pemasangan ?: '-',
                'ukuran' => $reklame->ukuran_reklame ?: '-',
                'masa_berlaku' => $reklame->tanggal_berlaku && $reklame->tanggal_berakhir
                    ? $reklame->tanggal_berlaku->format('d/m/Y') . ' s/d ' . $reklame->tanggal_berakhir->format('d/m/Y')
                    : '-',
                'tanggal_berakhir' => optional($reklame->tanggal_berakhir)->format('Y-m-d'),
                'tanggal_terbit' => optional($reklame->tanggal_terbit)->format('d/m/Y'),
                'is_terbit' => $reklame->status === 'Sudah Terbit',
                'is_expired' => $reklame->isKedaluwarsa(),
                'status_text' => $reklame->status === 'Sudah Terbit' ? 'Terbit' : ($reklame->isKedaluwarsa() ? 'Kedaluwarsa' : 'Aktif'),
                'latitude' => (float) $reklame->latitude,
                'longitude' => (float) $reklame->longitude,
                'foto_preview_url' => $fotoPreviewUrl,
                'foto_kind' => $fotoKind,
                'detail_url' => route('permohonan.show', $reklame),
                'google_maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . $reklame->latitude . ',' . $reklame->longitude,
            ];
        })->values();

        return view('satpol-pp.map', compact('reklameList'));
    }
}
