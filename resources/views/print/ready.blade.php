@extends('layouts.app')

@section('title', 'Siap Cetak')

@push('styles')
@include('components.workflow.styles')
@endpush

@section('content')
<div class="header-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1><i class="bi bi-printer"></i> Siap Cetak</h1>
            <p class="text-muted">Daftar permohonan yang sudah disetujui Kepala Bidang dan siap diproses cetak</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('approval.dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-check2-circle"></i> Kembali ke Approval
            </a>
            <a href="{{ route('permohonan.peta') }}" class="btn btn-outline-danger">
                <i class="bi bi-map"></i> Buka Peta Reklame
            </a>
        </div>
    </div>
</div>

<x-workflow.hero
    title="Ringkasan cetak"
    :description="'Saat ini ada ' . $approvedPermohonan->total() . ' permohonan final yang siap dicetak, serta ' . $printedPermohonan->count() . ' permohonan yang sudah dicetak ditampilkan di bawah.'"
>
    <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2"></i> Dashboard Operator
            </a>
            @if (auth()->user()->hasRole('operator'))
                <a href="{{ route('approval.status') }}" class="btn btn-success">
                    <i class="bi bi-graph-up"></i> Status Approval
                </a>
            @endif
    </x-slot:actions>
</x-workflow.hero>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$approvedPermohonan->total()"
            label="Total Siap Cetak"
            helper="Semua permohonan yang sudah selesai approval final."
            number-class="text-success"
        />
    </div>
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$printedPermohonan->count()"
            label="Sudah Dicetak"
            helper="Permohonan yang sudah diproses operator dan berstatus Sudah Terbit."
            number-class="text-primary"
        />
    </div>
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$approvedPermohonan->lastPage()"
            label="Total Halaman"
            helper="Memudahkan operator menelusuri antrian cetak bertahap."
            number-class="text-info"
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
                    description="Kembali ke antrian approval untuk memproses berkas baru."
                    icon-class="icon-approval"
                />

                <x-workflow.quick-link
                    :href="route('print.ready')"
                    icon="bi-printer"
                    title="Siap Cetak"
                    description="Kelola daftar cetak untuk berkas yang sudah final."
                    icon-class="icon-print"
                />

                <x-workflow.quick-link
                    :href="route('permohonan.peta')"
                    icon="bi-map"
                    title="Peta Reklame"
                    description="Pantau titik reklame yang aktif maupun yang perlu tindak lanjut."
                    icon-class="icon-map"
                />
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Alur Kerja</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>1. Approval final selesai</strong>
                        <div class="small text-muted">Permohonan masuk daftar siap cetak setelah disetujui Kepala Bidang.</div>
                    </div>


                    <span class="badge bg-success">Final</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                    <div>
                        <strong>2. Operator cetak dokumen</strong>
                        <div class="small text-muted">Lakukan preview lalu cetak dokumen yang dibutuhkan.</div>
                    </div>
                    <span class="badge bg-primary">Cetak</span>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                    <div>
                        <strong>3. Lanjut ke pemantauan lapangan</strong>
                        <div class="small text-muted">Gunakan peta reklame untuk pemantauan status aktif dan kedaluwarsa.</div>
                    </div>
                    <span class="badge bg-danger">Peta</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Daftar Permohonan Siap Cetak</h5>
        <span class="badge bg-success">{{ $approvedPermohonan->total() }} Data</span>
    </div>
    <div class="card-body">
        @if ($approvedPermohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
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
                                    {{-- Status will be shown on individual surat page --}}
                                </td>
                                <td>{{ $item->updated_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('print.preview', $item) }}" class="btn btn-sm btn-success" title="Preview & Cetak">
                                        <i class="bi bi-printer"></i> Cetak
                                    </a>
                                    <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    {{-- Operator action moved to individual surat page --}}
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

<div class="card mt-4">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-check2-circle"></i> Daftar Permohonan Sudah Dicetak</h5>
        <span class="badge bg-primary">{{ $printedPermohonan->count() }} Data</span>
    </div>
    <div class="card-body">
        @if ($printedPermohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Pemohon</th>
                            <th>Jenis Reklame</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal Terbit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($printedPermohonan as $item)
                            <tr>
                                <td><strong>{{ $item->nomor_registrasi }}</strong></td>
                                <td>{{ $item->nama_pemohon }}</td>
                                <td><span class="badge bg-primary">{{ $item->jenis_reklame }}</span></td>
                                <td>{{ Str::limit($item->lokasi_pemasangan, 25) }}</td>
                                <td><span class="badge bg-success">Sudah Dicetak</span></td>
                                <td>
                                    {{ $item->tanggal_terbit ? \Carbon\Carbon::parse($item->tanggal_terbit)->format('d M Y H:i') : '-' }}
                                </td>
                                <td>
                                    <a href="{{ route('print.preview', $item) }}" class="btn btn-sm btn-outline-primary" title="Lihat Preview">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                    <a href="{{ route('permohonan.show', $item) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-info-circle"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Belum ada permohonan yang sudah dicetak.
            </div>
        @endif
    </div>
</div>
@endsection
