@extends('layouts.app')

@section('title', 'Dashboard Operator')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-speedometer2"></i> Dashboard Operator</h1>
    <p class="text-muted">Kelola verifikasi dokumen dan approval tahap pertama</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number">{{ $totalPermohonan }}</div>
            <div class="label">Total Pending</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #0d6efd;">{{ $diajukan }}</div>
            <div class="label">Menunggu Verifikasi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #ffc107;">{{ $revisiMenunggu }}</div>
            <div class="label">Revisi Menunggu Verifikasi</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #dc3545;">{{ $ditolak }}</div>
            <div class="label">Ditolak</div>
        </div>
    </div>
</div>

<!-- Tabs untuk Permohonan -->
<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="verifikasi-tab" data-bs-toggle="tab" data-bs-target="#verifikasi" type="button" role="tab" aria-controls="verifikasi" aria-selected="true">
                    <i class="bi bi-hourglass-split"></i> Menunggu Verifikasi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="revisi-tab" data-bs-toggle="tab" data-bs-target="#revisi" type="button" role="tab" aria-controls="revisi" aria-selected="false">
                    <i class="bi bi-arrow-repeat"></i> Revisi
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cetak-tab" data-bs-toggle="tab" data-bs-target="#cetak" type="button" role="tab" aria-controls="cetak" aria-selected="false">
                    <i class="bi bi-printer"></i> Siap Cetak
                </button>
            </li>
        </ul>
    </div>
    
    <div class="tab-content">
        <!-- Tab Verifikasi -->
        <div class="tab-pane fade show active" id="verifikasi" role="tabpanel" aria-labelledby="verifikasi-tab">
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
                                            <a href="{{ route('approval.verify', $item) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-check-circle"></i> Verifikasi
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tidak ada permohonan yang menunggu verifikasi.
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Revisi -->
        <div class="tab-pane fade" id="revisi" role="tabpanel" aria-labelledby="revisi-tab">
            <div class="card-body">
                @if ($revisiPermohonan->count() > 0)
                    <div class="alert alert-warning mb-3">
                        <i class="bi bi-exclamation-triangle"></i> Permohonan berikut telah direvisi oleh pemohon dan menunggu verifikasi ulang
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Nama Pemohon</th>
                                    <th>Jenis Reklame</th>
                                    <th>Lokasi</th>
                                    <th>Tanggal Revisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($revisiPermohonan as $item)
                                    <tr>
                                        <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                        <td>{{ $item->nama_pemohon }}</td>
                                        <td><span class="badge bg-warning text-dark">{{ $item->jenis_reklame }}</span></td>
                                        <td>{{ Str::limit($item->lokasi_pemasangan, 25) }}</td>
                                        <td>{{ $item->updated_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('approval.verify', $item) }}" class="btn btn-sm btn-primary">
                                                <i class="bi bi-check-circle"></i> Verifikasi Revisi
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $revisiPermohonan->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Tidak ada permohonan yang sedang dalam revisi.
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Cetak -->
        <div class="tab-pane fade" id="cetak" role="tabpanel" aria-labelledby="cetak-tab">
            <div class="card-body">
                @php
                    $approvedPermohonan = \App\Models\PermohonanReklame::where('status', 'Disetujui Kepala Bidang')
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                @endphp
                @if ($approvedPermohonan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Nama Pemohon</th>
                                    <th>Jenis Reklame</th>
                                    <th>Lokasi</th>
                                    <th>Status Cetak</th>
                                    <th>Tanggal Approval</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($approvedPermohonan as $item)
                                    <tr>
                                        <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                        <td>{{ $item->nama_pemohon }}</td>
                                        <td><span class="badge bg-success">{{ $item->jenis_reklame }}</span></td>
                                        <td>{{ Str::limit($item->lokasi_pemasangan, 25) }}</td>
                                        <td>
                                            <span class="badge bg-info">Siap Cetak</span>
                                        </td>
                                        <td>{{ $item->updated_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('print.preview', $item) }}" class="btn btn-sm btn-success" title="Preview & Cetak">
                                                <i class="bi bi-printer"></i> Cetak
                                            </a>
                                            <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        {{ $approvedPermohonan->links() }}
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Belum ada permohonan yang siap cetak.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
