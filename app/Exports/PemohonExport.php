<?php

namespace App\Exports;

use App\Models\PermohonanReklame;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PemohonExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function __construct(private readonly Builder $query)
    {
    }

    public function collection()
    {
        return (clone $this->query)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal Daftar',
            'Nomor Registrasi',
            'Nama Pemohon',
            'Email',
            'No. Telepon',
            'NIK',
            'NPWP',
            'Jenis Reklame',
            'Lokasi Pemasangan',
            'Status',
            'Tanggal Berlaku',
            'Tanggal Berakhir',
        ];
    }

    public function map($item): array
    {
        return [
            optional($item->created_at)->format('d/m/Y H:i'),
            $item->nomor_registrasi,
            $item->nama_pemohon,
            optional($item->user)->email,
            '"' . $item->nomor_telepon . '"',
            '"' . $item->nik . '"',
            $item->npwp,
            $item->jenis_reklame,
            $item->lokasi_pemasangan,
            $item->status,
            optional($item->tanggal_berlaku)->format('d/m/Y'),
            optional($item->tanggal_berakhir)->format('d/m/Y'),
        ];
    }
}
