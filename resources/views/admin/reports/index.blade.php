@extends('layouts.app')

@section('title', 'Laporan Permohonan Reklame')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-bar-graph"></i> Laporan Permohonan</h1>
    <p class="text-muted">Rekapitulasi data pendaftaran reklame</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white text-center p-3">
            <h6 class="mb-1">Total Permohonan</h6>
            <h3 class="mb-0">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white text-center p-3">
            <h6 class="mb-1">Disetujui</h6>
            <h3 class="mb-0">{{ $stats['disetujui'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white text-center p-3">
            <h6 class="mb-1">Ditolak</h6>
            <h3 class="mb-0">{{ $stats['ditolak'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark text-center p-3">
            <h6 class="mb-1">Sedang Proses</h6>
            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-filter"></i> Filter Laporan</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="Diajukan" @selected(request('status') == 'Diajukan')>Diajukan</option>
                    <option value="Disetujui Kepala Bidang" @selected(request('status') == 'Disetujui Kepala Bidang')>Disetujui</option>
                    <option value="Ditolak Operator" @selected(request('status') == 'Ditolak Operator')>Ditolak</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Jenis Reklame</label>
                <select name="jenis_reklame" class="form-select">
                    <option value="">Semua Jenis</option>
                    <option value="Permanen" @selected(request('jenis_reklame') == 'Permanen')>Permanen</option>
                    <option value="Non Permanen" @selected(request('jenis_reklame') == 'Non Permanen')>Non Permanen</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Tgl Daftar</th>
                        <th>No. Registrasi</th>
                        <th>Pemohon</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $item)
                    <tr>
                        <td class="ps-3">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td><span class="fw-bold">{{ $item->nomor_registrasi }}</span></td>
                        <td>{{ $item->nama_pemohon }}</td>
                        <td>{{ $item->jenis_reklame }}</td>
                        <td class="text-truncate" style="max-width: 200px;">{{ $item->lokasi_pemasangan }}</td>
                        <td>
                            @php
                                $badgeClass = 'bg-secondary';
                                if(str_contains($item->status, 'Disetujui')) $badgeClass = 'bg-success';
                                if(str_contains($item->status, 'Ditolak')) $badgeClass = 'bg-danger';
                                if(str_contains($item->status, 'Diajukan') || str_contains($item->status, 'Verifikasi')) $badgeClass = 'bg-warning text-dark';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $item->status }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Data tidak ditemukan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reports->hasPages())
    <div class="card-footer bg-white">
        {{ $reports->links() }}
    </div>
    @endif
</div>
@endsection
