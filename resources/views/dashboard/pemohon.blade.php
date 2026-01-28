@extends('layouts.app')

@section('title', 'Dashboard Pemohon')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
    <p class="text-muted">Selamat datang, {{ auth()->user()->name }}</p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number">{{ $totalPermohonan }}</div>
            <div class="label">Total Permohonan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #6c757d;">{{ $draft }}</div>
            <div class="label">Draft</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #0d6efd;">{{ $diajukan + $revisi }}</div>
            <div class="label">Sedang Diproses</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="number" style="color: #198754;">{{ $disetujui }}</div>
            <div class="label">Disetujui</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Permohonan Terbaru</h5>
            </div>
            <div class="card-body">
                @if ($recentPermohonan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Narasi</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentPermohonan as $item)
                                    <tr>
                                        <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                        <td>{{ Str::limit($item->narasi_reklame, 30) }}</td>
                                        <td>
                                            <span class="badge" style="background-color: 
                                                @if($item->status === 'Disetujui Kepala Bidang') #28a745
                                                @elseif($item->status === 'Ditolak Operator' || $item->status === 'Ditolak Kepala Seksi') #dc3545
                                                @else #17a2b8 @endif
                                                ">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                        <td>{{ $item->created_at->format('d M') }}</td>
                                        <td>
                                            <a href="{{ route('permohonan.show', $item) }}" class="btn btn-xs btn-outline-primary">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('permohonan.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua Permohonan
                        </a>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Anda belum membuat permohonan.
                        <a href="{{ route('permohonan.create') }}" class="btn btn-sm btn-primary ms-2">
                            Buat Permohonan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-lightbulb"></i> Panduan</h5>
                <p class="small mb-3">Berikut langkah-langkah mengajukan permohonan reklame:</p>
                <ol class="small text-muted">
                    <li>Buat permohonan baru dengan mengklik tombol di samping</li>
                    <li>Isi data pemohon dan data reklame</li>
                    <li>Upload dokumen pendukung</li>
                    <li>Ajukan permohonan</li>
                    <li>Pantau status permohonan Anda</li>
                </ol>
            </div>
        </div>

        @if ($ditolak > 0)
            <div class="card border-danger mt-3">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="bi bi-exclamation-circle"></i> Perhatian</h5>
                    <p class="small mb-0">Anda memiliki {{ $ditolak }} permohonan yang ditolak. Silakan perbaiki dan ajukan kembali.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
