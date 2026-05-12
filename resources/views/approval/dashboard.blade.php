@extends('layouts.app')

@section('title', 'Dashboard Approval')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-check2-circle"></i> Dashboard Approval</h1>
            <p class="text-muted">Kelola permohonan yang memerlukan approval</p>
        </div>
        @if (auth()->user()->hasRole('operator'))
            <div>
                <a href="{{ route('approval.status') }}" class="btn btn-success">
                    <i class="bi bi-graph-up"></i> Lihat Status Approval
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Search Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('search') }}" class="d-flex gap-2">
            <input type="text" name="q" class="form-control" placeholder="Cari berdasarkan nomor registrasi, NIK, atau nama pemohon" value="{{ request('q') }}">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
        </form>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stats-card">
            <div class="number" style="color: #0d6efd;">{{ $totalPermohonan }}</div>
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

<!-- Permohonan List -->
<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Daftar Permohonan</h5>
    </div>
    <div class="card-body">
        @if ($permohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Pemohon</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permohonan as $item)
                            <tr>
                                <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                <td>{{ $item->nama_pemohon }}</td>
                                <td>{{ Str::limit($item->lokasi_pemasangan, 25) }}</td>
                                <td>
                                    <span class="badge" style="background-color: 
                                        @if($item->status === 'Disetujui Kepala Bidang') #28a745
                                        @elseif(str_contains($item->status, 'Ditolak') || str_contains($item->status, 'Revisi')) #dc3545
                                        @else #17a2b8 @endif
                                        ">
                                        {{ $item->status }}
                                    </span>
                                    @if(str_contains($item->status, 'Revisi'))
                                        <small class="text-muted d-block mt-1">
                                            @if($item->rejectedByRole)
                                                Revisi dari {{ $item->rejectedByRole->name }}
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($item->canBeApprovedByOperator() && auth()->user()->hasRole('operator'))
                                        <a href="{{ route('approval.verify', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-circle"></i> Verifikasi
                                        </a>
                                    @elseif ($item->canBeApprovedByKepalaSeksi() && auth()->user()->hasRole('kepala_seksi'))
                                        <a href="{{ route('approval.approve-seksi', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </a>
                                    @elseif ($item->canBeApprovedByKepalaBidang() && auth()->user()->hasRole('kepala_bidang'))
                                        <a href="{{ route('approval.approve-bidang', $item) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-check-circle"></i> Final
                                        </a>
                                    @endif
                                    @if ($item->isPrintable() && auth()->user()->hasRole('operator'))
                                        <a href="{{ route('print.preview', $item) }}" class="btn btn-sm btn-success" title="Preview & Cetak">
                                            <i class="bi bi-printer"></i> Cetak
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $permohonan->links() }}
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Tidak ada permohonan yang memerlukan approval pada tahap ini.
            </div>
        @endif
    </div>
</div>
@endsection
