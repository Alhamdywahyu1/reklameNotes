@extends('layouts.app')

@section('title', 'Dashboard Approval')

@push('styles')
@include('components.workflow.styles')
@endpush

@section('content')
<div class="header-page">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
        <div>
            <h1><i class="bi bi-check2-circle"></i> Dashboard Approval</h1>
            <p class="text-muted">Kelola antrian approval sesuai tahapan kerja masing-masing role</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if (auth()->user()->hasRole('operator'))
                <a href="{{ route('approval.status') }}" class="btn btn-success">
                    <i class="bi bi-graph-up"></i> Lihat Status Approval
                </a>
                <a href="{{ route('print.ready') }}" class="btn btn-outline-success">
                    <i class="bi bi-printer"></i> Buka Siap Cetak
                </a>
            @endif
        </div>
    </div>
</div>

<x-workflow.hero
    title="Ringkasan antrian saat ini"
    :description="'Terdapat ' . $totalPermohonan . ' permohonan pada antrian approval, dengan ' . $disetujui . ' sudah disetujui final dan ' . $ditolak . ' ditolak.'"
>
    <x-slot:actions>
            <a href="{{ route('search') }}" class="btn btn-outline-primary">
                <i class="bi bi-search"></i> Pencarian Lanjutan
            </a>
            <a href="{{ route('permohonan.peta') }}" class="btn btn-outline-danger">
                <i class="bi bi-map"></i> Peta Reklame
            </a>
    </x-slot:actions>
</x-workflow.hero>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$totalPermohonan"
            label="Total Pending"
            helper="Permohonan yang masih berada dalam alur approval aktif."
            number-class="text-primary"
        />
    </div>
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$disetujui"
            label="Disetujui"
            helper="Permohonan yang sudah lolos sampai persetujuan final."
            number-class="text-success"
        />
    </div>
    <div class="col-md-4">
        <x-workflow.summary-card
            :number="$ditolak"
            label="Ditolak"
            helper="Permohonan yang dikembalikan atau dihentikan prosesnya."
            number-class="text-danger"
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
                    description="Tinjau dan proses antrian approval yang aktif."
                    icon-class="icon-approval"
                />

                @if (auth()->user()->hasRole('operator'))
                    <x-workflow.quick-link
                        :href="route('print.ready')"
                        icon="bi-printer"
                        title="Siap Cetak"
                        description="Lanjutkan dokumen yang sudah selesai approval final."
                        icon-class="icon-print"
                    />
                @endif

                <x-workflow.quick-link
                    :href="route('permohonan.peta')"
                    icon="bi-map"
                    title="Peta Reklame"
                    description="Lihat sebaran reklame aktif dan titik yang perlu tindak lanjut."
                    icon-class="icon-map"
                />
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-search"></i> Cari Permohonan</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('search') }}" class="d-flex flex-column flex-sm-row gap-2">
                    <input type="text" name="q" class="form-control" placeholder="Cari berdasarkan nomor registrasi, NIK, atau nama pemohon" value="{{ request('q') }}">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </form>
                <p class="small text-muted mt-3 mb-0">
                    Gunakan pencarian untuk membuka permohonan tertentu tanpa harus menelusuri seluruh antrian.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Daftar Permohonan</h5>
        <span class="badge bg-primary">{{ $permohonan->total() }} Data</span>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="{{ route('approval.dashboard', ['masa_filter' => 'all']) }}"
               class="btn btn-sm {{ $masaFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-list"></i> Semua
            </a>
            <a href="{{ route('approval.dashboard', ['masa_filter' => 'missing']) }}"
               class="btn btn-sm {{ $masaFilter === 'missing' ? 'btn-danger' : 'btn-outline-danger' }}">
                <i class="bi bi-exclamation-circle"></i> Belum Diatur (@if(isset($countMissing)){{ $countMissing }}@else-@endif)
            </a>
            <a href="{{ route('approval.dashboard', ['masa_filter' => 'invalid']) }}"
               class="btn btn-sm {{ $masaFilter === 'invalid' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                <i class="bi bi-exclamation-triangle"></i> Tidak Valid (@if(isset($countInvalid)){{ $countInvalid }}@else-@endif)
            </a>
        </div>

        @if ($permohonan->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Nama Pemohon</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Masa Berlaku</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permohonan as $item)
                            @php
                                $hasMasaBerlaku = $item->tanggal_berlaku && $item->tanggal_berakhir;
                                $masaBerlakuValid = $hasMasaBerlaku
                                    ? \Carbon\Carbon::parse($item->tanggal_berakhir)->gt(\Carbon\Carbon::parse($item->tanggal_berlaku))
                                    : false;
                            @endphp
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
                                <td>
                                    @if(!$hasMasaBerlaku)
                                        <span class="badge bg-danger">Belum Diatur Operator</span>
                                    @elseif(!$masaBerlakuValid)
                                        <span class="badge bg-warning text-dark">Tidak Valid</span>
                                        <small class="text-muted d-block mt-1">Periksa tanggal berlaku/berakhir</small>
                                    @else
                                        <span class="badge bg-success">Valid</span>
                                        <small class="text-muted d-block mt-1">
                                            {{ \Carbon\Carbon::parse($item->tanggal_berlaku)->format('d M Y') }} -
                                            {{ \Carbon\Carbon::parse($item->tanggal_berakhir)->format('d M Y') }}
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
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Tidak ada permohonan yang memerlukan approval pada tahap ini.
            </div>
        @endif
    </div>
</div>
@endsection
