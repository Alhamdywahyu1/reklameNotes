@extends('layouts.app')

@section('title', 'Dashboard Kepala Bidang')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-speedometer2"></i> Dashboard Kepala Bidang</h1>
            <p class="text-muted">Kelola approval final permohonan reklame</p>
        </div>
        <div>
            <a href="{{ route('dashboard.reklame-chart') }}" class="btn btn-primary">
                <i class="bi bi-bar-chart"></i> Lihat Analytics
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card stats-card">
            <div class="number">{{ $totalPermohonan }}</div>
            <div class="label">Total Pending</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stats-card">
            <div class="number" style="color: #198754;">{{ $disetujui }}</div>
            <div class="label">Final Approved</div>
        </div>
    </div>
</div>

<!-- Reklame Statistics -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">REKLAME PERMANEN (DISETUJUI)</h6>
                        <h3 class="text-success mb-0">{{ $reklamePermanen }}</h3>
                    </div>
                    <i class="bi bi-building text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">REKLAME NON-PERMANEN (DISETUJUI)</h6>
                        <h3 class="text-warning mb-0">{{ $reklameNonPermanen }}</h3>
                    </div>
                    <i class="bi bi-flag text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Permohonan Menunggu Final Approval</h5>
    </div>
    <div class="card-body">
        @if ($pendingPermohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Reklame</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingPermohonan as $item)
                            <tr>
                                <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                <td>{{ $item->nama_pemohon }}</td>
                                <td><span class="badge bg-info">{{ $item->jenis_reklame }}</span></td>
                                <td>{{ Str::limit($item->lokasi_pemasangan, 25) }}</td>
                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('approval.approve-bidang', $item) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Final Approval
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Tidak ada permohonan yang menunggu final approval.
            </div>
        @endif
    </div>
</div>
@endsection
