@extends('layouts.app')

@section('title', 'Lihat Surat Pernyataan')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-text"></i> Surat Pernyataan</h1>
    <p class="text-muted">No. Registrasi: {{ $permohonan->nomor_registrasi }}</p>
</div>

<div class="row g-5">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-5">
                <!-- Status Badge -->
                <div class="mb-5">
                    <span class="badge bg-{{ $suratPernyataan->status == 'verified' ? 'success' : ($suratPernyataan->status == 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($suratPernyataan->status) }}
                    </span>
                </div>

                <!-- Header -->
                <div class="text-center mb-5">
                    <h5 style="color: #1a5490; font-weight: bold;">SURAT PERNYATAAN</h5>
                </div>

                <!-- Data Pemohon -->
                <div class="mb-5">
                    <h6 style="color: #1a5490; border-bottom: 2px solid #1a5490; padding-bottom: 8px;">
                        <i class="bi bi-person"></i> Data Pemohon
                    </h6>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Nama</small>
                            <strong>{{ $suratPernyataan->nama_pemohon }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Pekerjaan</small>
                            <strong>{{ $suratPernyataan->pekerjaan }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">No KTP</small>
                            <strong>{{ $suratPernyataan->no_ktp }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Tanggal Pernyataan</small>
                            <strong>{{ $suratPernyataan->tanggal_pernyataan?->format('d/m/Y') ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Alamat</small>
                        <strong>{{ $suratPernyataan->alamat_pemohon }}</strong>
                    </div>
                </div>

                <!-- Syarat dan Ketentuan -->
                <div class="mb-5">
                    <h6 style="color: #1a5490; border-bottom: 2px solid #1a5490; padding-bottom: 8px;">
                        <i class="bi bi-list-check"></i> Syarat & Ketentuan yang Disepakati
                    </h6>
                    <div class="mt-3">
                        @for($i = 1; $i <= 8; $i++)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" checked disabled id="syarat_{{ $i }}">
                                    <label class="form-check-label" for="syarat_{{ $i }}">
                                        @switch($i)
                                            @case(1)
                                                <strong>1.</strong> Bahwa saya sanggup menaati segala peraturan / ketentuan – ketentuan yang di terapkan oleh Pemerintah Kabupaten Bangkalan
                                            @break
                                            @case(2)
                                                <strong>2.</strong> Bahwa sesungguhnya dengan izin penyelenggaraan / pemasangan reklame tersebut, akan saya pergunakan sendiri dan semata-mata untuk kepentingan reklame sesuai dengan ketentuan yang berlaku
                                            @break
                                            @case(3)
                                                <strong>3.</strong> Bahwa saya tidak akan mengubah konstruksi dan atau memindah tempat / lokasi yang ditentukan tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu
                                            @break
                                            @case(4)
                                                <strong>4.</strong> Bahwa saya tidak akan memindah tangankan surat izin pemasangan reklame kepada pihak lain tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu
                                            @break
                                            @case(5)
                                                <strong>5.</strong> Bahwa saya bertanggung jawab sepenuhnya atas konstruksi reklame, kebersihan, ketertiban dan keindahan reklame, serta pemeliharaan reklame yang di pasang
                                            @break
                                            @case(6)
                                                <strong>6.</strong> Bahwa saya bertanggung jawab sepenuhnya atas barang pihak lain dan kecelakaan terhadap orang lain yang di akibatkan oleh reklame
                                            @break
                                            @case(7)
                                                <strong>7.</strong> Bahwa saya penyelenggara reklame harus membongkar dan menurunkan reklame selambat-lambatnya 7 (tujuh) hari setelah masa berlakunya berakhir dan tidak diperpanjang
                                            @break
                                            @case(8)
                                                <strong>8.</strong> Bahwa apabila saya kemudian hari ternyata tidak mematuhi janji tersebut di atas baik seluruhnya maupun sebagian dalam pernyataan ini, maka saya bersedia reklame tersebut di bongkar atau dikenakan sanksi lain sesuai dengan ketentuan yang berlaku
                                            @break
                                        @endswitch
                                    </label>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Dokumen Pendukung -->
                @if($suratPernyataan->file_tanda_tangan || $suratPernyataan->file_materai)
                    <div class="mb-5">
                        <h6 style="color: #1a5490; border-bottom: 2px solid #1a5490; padding-bottom: 8px;">
                            <i class="bi bi-file-earmark"></i> Dokumen Pendukung
                        </h6>
                        <div class="row mt-3">
                            @if($suratPernyataan->file_tanda_tangan)
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Bukti Tanda Tangan</small>
                                    <a href="{{ Storage::url($suratPernyataan->file_tanda_tangan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            @endif
                            @if($suratPernyataan->file_materai)
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block mb-2">Bukti Materai (Rp. 10.000)</small>
                                    <a href="{{ Storage::url($suratPernyataan->file_materai) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Keterangan Penolakan -->
                @if($suratPernyataan->status == 'rejected' && $suratPernyataan->keterangan_penolakan)
                    <div class="alert alert-danger mb-5">
                        <h6 class="alert-heading">
                            <i class="bi bi-exclamation-circle"></i> Keterangan Penolakan
                        </h6>
                        {{ $suratPernyataan->keterangan_penolakan }}
                    </div>
                @endif

                <!-- Audit Info -->
                <div class="border-top pt-4 mt-5">
                    <h6 class="mb-3">
                        <i class="bi bi-clock-history"></i> Riwayat
                    </h6>
                    <div class="row small">
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">Dibuat:</span>
                            <strong>{{ $suratPernyataan->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="text-muted">Diupdate:</span>
                            <strong>{{ $suratPernyataan->updated_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        @if($suratPernyataan->submitted_at)
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">Disubmit:</span>
                                <strong>{{ $suratPernyataan->submitted_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        @endif
                        @if($suratPernyataan->verified_at)
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">Diverifikasi:</span>
                                <strong>{{ $suratPernyataan->verified_at->format('d/m/Y H:i') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex gap-2 mt-5 pt-4 border-top">
                    <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                    @if($suratPernyataan->status == 'draft' && auth()->id() === $permohonan->user_id)
                        <a href="{{ route('surat-pernyataan.edit', $permohonan) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                    @endif
                    <a href="{{ route('surat-pernyataan.download-pdf', $permohonan) }}" class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-info-circle-fill text-primary"></i> Informasi
                </h6>
                <div class="mb-3">
                    <small class="text-muted d-block">Status Pernyataan</small>
                    <span class="badge bg-{{ $suratPernyataan->status == 'verified' ? 'success' : ($suratPernyataan->status == 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($suratPernyataan->status) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status Permohonan</small>
                    <span class="badge bg-{{ $permohonan->status == 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst($permohonan->status) }}
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Semua Syarat Disetujui</small>
                    <strong>
                        @if($suratPernyataan->areAllConditionsAgreed())
                            <span class="badge bg-success">Ya</span>
                        @else
                            <span class="badge bg-danger">Tidak</span>
                        @endif
                    </strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
