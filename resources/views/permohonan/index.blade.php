@extends('layouts.app')

@section('title', 'Permohonan Saya')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-text"></i> Permohonan Saya</h1>
    <p class="text-muted">Kelola semua permohonan reklame Anda</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number">{{ $permohonan->total() }}</div>
            <div class="label">Total Permohonan</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Permohonan</h5>
        <a href="{{ route('permohonan.create') }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus-circle"></i> Buat Permohonan Baru
        </a>
    </div>
    <div class="card-body">
        @if ($permohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Reklame</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permohonan as $item)
                            <tr>
                                <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                <td>{{ $item->narasi_reklame }}</td>
                                <td>{{ Str::limit($item->lokasi_pemasangan, 30) }}</td>
                                <td>
                                    <span class="badge status-badge" 
                                        style="background-color: 
                                        @if($item->status === 'Disetujui Kepala Bidang') #28a745
                                        @elseif($item->status === 'Ditolak Operator' || $item->status === 'Ditolak Kepala Seksi') #dc3545
                                        @else #17a2b8 @endif
                                        ">
                                        {{ $item->status }}
                                    </span>
                                </td>
                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $permohonan->links() }}
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Anda belum membuat permohonan. 
                <a href="{{ route('permohonan.create') }}">Buat permohonan baru</a>
            </div>
        @endif
    </div>
</div>
@endsection
