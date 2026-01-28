@extends('layouts.app')

@section('title', 'Dashboard Kepala Seksi')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-speedometer2"></i> Dashboard Kepala Seksi</h1>
            <p class="text-muted">Kelola approval tahap kedua permohonan reklame</p>
        </div>
        <div>
            <a href="{{ route('dashboard.reklame-chart') }}" class="btn btn-primary">
                <i class="bi bi-bar-chart"></i> Lihat Analytics
            </a>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="number">{{ $totalPermohonan }}</div>
            <div class="label">Total Pending</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="number" style="color: #198754;">{{ $disetujui }}</div>
            <div class="label">Disetujui</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="number" style="color: #dc3545;">{{ $ditolak }}</div>
            <div class="label">Ditolak</div>
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
        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Permohonan Menunggu Approval</h5>
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
                            <th>Status</th>
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
                                <td>
                                    <span class="badge bg-warning text-dark">{{ $item->status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('approval.approve-seksi', $item) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Tidak ada permohonan yang menunggu approval.
            </div>
        @endif
    </div>
</div>
@endsection
