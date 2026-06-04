@extends('layouts.app')

@section('title', 'Dashboard Operator')

@push('styles')
@include('components.workflow.styles')
@endpush

@section('content')
<div class="header-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1><i class="bi bi-speedometer2"></i> Dashboard Operator</h1>
            <p class="text-muted">Ringkasan kerja operator untuk verifikasi, cetak, dan pemantauan reklame</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('approval.dashboard') }}" class="btn btn-primary">
                <i class="bi bi-check2-circle"></i> Buka Approval
            </a>
            <a href="{{ route('print.ready') }}" class="btn btn-success">
                <i class="bi bi-printer"></i> Buka Siap Cetak
            </a>
        </div>
    </div>
</div>

<x-workflow.hero
    title="Fokus kerja hari ini"
    :description="'Ada ' . $diajukan . ' permohonan baru, ' . $revisiMenunggu . ' revisi menunggu verifikasi, dan ' . $siapCetak . ' berkas siap cetak.'"
>
    <x-slot:actions>
            <a href="{{ route('approval.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-list-check"></i> Tinjau Antrian
            </a>
            <a href="{{ route('permohonan.peta') }}" class="btn btn-outline-danger">
                <i class="bi bi-map"></i> Cek Peta Reklame
            </a>
    </x-slot:actions>
</x-workflow.hero>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <x-workflow.summary-card
            :number="$totalPermohonan"
            label="Total Pending"
            helper="Semua permohonan yang masih perlu tindakan operator."
        />
    </div>
    <div class="col-md-6 col-xl-3">
        <x-workflow.summary-card
            :number="$diajukan"
            label="Menunggu Verifikasi"
            helper="Permohonan baru yang siap diperiksa dokumennya."
            number-class="text-primary"
        />
    </div>
    <div class="col-md-6 col-xl-3">
        <x-workflow.summary-card
            :number="$revisiMenunggu"
            label="Revisi Menunggu"
            helper="Berkas revisi yang perlu diverifikasi ulang."
            number-class="text-warning"
        />
    </div>
    <div class="col-md-6 col-xl-3">
        <x-workflow.summary-card
            :number="$siapCetak"
            label="Siap Cetak"
            helper="Permohonan final yang sudah siap dicetak."
            number-class="text-success"
        />
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-lightning-charge"></i> Akses Cepat</h5>
            </div>
            <div class="card-body d-grid gap-3">
                <x-workflow.quick-link
                    :href="route('approval.dashboard')"
                    icon="bi-check2-circle"
                    title="Approval"
                    description="Masuk ke antrian verifikasi dan proses approval operator."
                    icon-class="icon-approval"
                />

                <x-workflow.quick-link
                    :href="route('print.ready')"
                    icon="bi-printer"
                    title="Siap Cetak"
                    description="Lihat daftar permohonan yang sudah siap diproses cetak."
                    icon-class="icon-print"
                />

                <x-workflow.quick-link
                    :href="route('permohonan.peta')"
                    icon="bi-map"
                    title="Peta Reklame"
                    description="Pantau titik reklame aktif dan yang masa berlakunya sudah habis."
                    icon-class="icon-map"
                />
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clipboard-data"></i> Status Kerja</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>Permohonan terverifikasi operator</strong>
                        <div class="small text-muted">Berkas yang sudah lolos tahap verifikasi operator.</div>
                    </div>
                    <span class="badge bg-primary">{{ $diverifikasi }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>Permohonan ditolak operator</strong>
                        <div class="small text-muted">Berkas yang dikembalikan ke pemohon.</div>
                    </div>
                    <span class="badge bg-danger">{{ $ditolak }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>Titik reklame kedaluwarsa di peta</strong>
                        <div class="small text-muted">Titik merah yang masih menunggu tindak lanjut manual operator.</div>
                    </div>
                    <span class="badge bg-danger">{{ $kedaluwarsaMap }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Prioritas Verifikasi</h5>
                <a href="{{ route('approval.dashboard') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                @if ($pendingPermohonan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Pemohon</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingPermohonan as $item)
                                    <tr>
                                        <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                        <td>{{ $item->nama_pemohon }}</td>
                                        <td>{{ $item->created_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('approval.verify', $item) }}" class="btn btn-sm btn-success">
                                                <i class="bi bi-file-earmark-check"></i> Verifikasi
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
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-arrow-repeat"></i> Prioritas Revisi</h5>
                <a href="{{ route('approval.dashboard') }}" class="btn btn-sm btn-outline-primary">Buka Approval</a>
            </div>
            <div class="card-body">
                @if ($revisiPermohonan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No. Registrasi</th>
                                    <th>Pemohon</th>
                                    <th>Tanggal Revisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($revisiPermohonan as $item)
                                    <tr>
                                        <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                        <td>{{ $item->nama_pemohon }}</td>
                                        <td>{{ $item->updated_at->format('d M Y') }}</td>
                                        <td>
                                            <a href="{{ route('approval.verify', $item) }}" class="btn btn-sm btn-warning text-dark">
                                                <i class="bi bi-arrow-repeat"></i> Tinjau
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Tidak ada permohonan revisi yang menunggu operator.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
