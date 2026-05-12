@extends('layouts.app')

@section('title', 'Status Approval - Disetujui & Ditolak')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-clipboard-check"></i> Status Permohonan</h1>
            <p class="text-muted">Ringkasan data permohonan yang telah disetujui dan ditolak</p>
        </div>
        <div>
            <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">DISETUJUI</h6>
                        <h2 class="text-success mb-0">{{ $totalDisetujui }}</h2>
                    </div>
                    <i class="bi bi-check-circle text-success" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">DITOLAK</h6>
                        <h2 class="text-danger mb-0">{{ $totalDitolak }}</h2>
                    </div>
                    <i class="bi bi-x-circle text-danger" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">PENDING</h6>
                        <h2 class="text-warning mb-0">{{ $totalPending }}</h2>
                    </div>
                    <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Buttons -->
<div class="card mb-3">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="{{ route('approval.status', ['status' => 'all']) }}" 
               class="btn btn-outline-primary {{ $status === 'all' ? 'active' : '' }}">
                <i class="bi bi-funnel"></i> Semua Data
            </a>
            <a href="{{ route('approval.status', ['status' => 'disetujui']) }}" 
               class="btn btn-outline-success {{ $status === 'disetujui' ? 'active' : '' }}">
                <i class="bi bi-check-circle"></i> Disetujui
            </a>
            <a href="{{ route('approval.status', ['status' => 'ditolak']) }}" 
               class="btn btn-outline-danger {{ $status === 'ditolak' ? 'active' : '' }}">
                <i class="bi bi-x-circle"></i> Ditolak
            </a>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-list-ul"></i>
            @if ($status === 'disetujui')
                Data Permohonan Disetujui
            @elseif ($status === 'ditolak')
                Data Permohonan Ditolak
            @else
                Semua Data Permohonan
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        @if ($permohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Reklame</th>
                            <th>Status</th>
                            <th>Tanggal Update</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permohonan as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->nomor_registrasi }}</strong>
                                </td>
                                <td>{{ $item->nama_pemohon }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $item->jenis_reklame }}</span>
                                </td>
                                <td>
                                    @if ($item->status === 'Disetujui Kepala Bidang')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Disetujui
                                        </span>
                                    @elseif ($item->status === 'Ditolak Operator')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Ditolak Operator
                                        </span>
                                    @elseif ($item->status === 'Ditolak Kepala Seksi')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle"></i> Ditolak Seksi
                                        </span>
                                    @elseif ($item->status === 'Revisi Menunggu Operator')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-arrow-repeat"></i> Revisi → Operator
                                        </span>
                                    @elseif ($item->status === 'Revisi Menunggu Kepala Seksi')
                                        <span class="badge bg-warning">
                                            <i class="bi bi-arrow-repeat"></i> Revisi → Kepala Seksi
                                        </span>
                                    @else
                                        <span class="badge bg-warning">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $item->updated_at->format('d M Y H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="card-footer bg-light">
                {{ $permohonan->appends(request()->query())->links() }}
            </div>
        @else
            <div class="alert alert-info m-3 mb-0">
                <i class="bi bi-info-circle"></i>
                @if ($status === 'disetujui')
                    Belum ada permohonan yang disetujui
                @elseif ($status === 'ditolak')
                    Belum ada permohonan yang ditolak
                @else
                    Belum ada data permohonan
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Details Information -->
<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi</h6>
            </div>
            <div class="card-body">
                <p class="text-muted mb-2">
                    <strong>Total Disetujui:</strong> {{ $totalDisetujui }} permohonan
                </p>
                <p class="text-muted mb-2">
                    <strong>Total Ditolak:</strong> {{ $totalDitolak }} permohonan
                </p>
                <p class="text-muted mb-0">
                    <strong>Total Pending:</strong> {{ $totalPending }} permohonan
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-question-circle"></i> Penjelasan Status</h6>
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <strong class="text-success">Disetujui:</strong> Permohonan telah disetujui oleh Kepala Bidang dan siap cetak surat
                </p>
                <p class="small mb-2">
                    <strong class="text-danger">Ditolak:</strong> Permohonan ditolak oleh Operator atau Kepala Seksi dengan alasan tertentu
                </p>
                <p class="small">
                    <strong class="text-warning">Pending:</strong> Permohonan masih dalam proses verifikasi oleh Operator
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
