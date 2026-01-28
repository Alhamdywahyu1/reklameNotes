@extends('layouts.app')

@section('title', 'Approval Kepala Seksi - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-check-circle"></i> Approval Kepala Seksi</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('approval.approve-seksi.store', $permohonan) }}">
                    @csrf

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-info-circle"></i> Data Permohonan</h5>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Nomor Registrasi</label>
                            <p class="fw-bold">{{ $permohonan->nomor_registrasi }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Status Saat Ini</label>
                            <p><span class="badge bg-info">{{ $permohonan->status }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Nama Pemohon</label>
                            <p class="fw-bold">{{ $permohonan->nama_pemohon }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted">NIK</label>
                            <p class="fw-bold">{{ $permohonan->nik }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Jenis Reklame</label>
                        <p class="fw-bold">{{ $permohonan->jenis_reklame }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Lokasi Pemasangan</label>
                        <p>{{ $permohonan->lokasi_pemasangan }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted">Narasi Reklame</label>
                        <p>{{ $permohonan->narasi_reklame }}</p>
                    </div>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan Approval</h5>

                    <div class="mb-3">
                        <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui" required>
                                <label class="form-check-label" for="disetujui">
                                    <strong style="color: #198754;">✓ Disetujui</strong> - Lanjut ke Kepala Bidang
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak">
                                <label class="form-check-label" for="ditolak">
                                    <strong style="color: #dc3545;">✗ Ditolak</strong> - Kembalikan ke pemohon
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                            id="keterangan" name="keterangan" rows="4" placeholder="Berikan keterangan jika perlu...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Kirim Approval
                        </button>
                        <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title">Status Approval</h6>
                <div class="timeline-simple">
                    <div class="timeline-item completed">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <p class="small fw-bold">Operator</p>
                            <p class="small text-muted">Diverifikasi</p>
                        </div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <p class="small fw-bold">Kepala Seksi</p>
                            <p class="small text-muted">Proses Approval</p>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-marker bg-secondary"></div>
                        <div class="timeline-content">
                            <p class="small fw-bold">Kepala Bidang</p>
                            <p class="small text-muted">Menunggu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h6>
                <small class="text-muted">
                    <ul class="mb-0">
                        <li>Review dokumen yang sudah diverifikasi operator</li>
                        <li>Setujui jika sesuai dengan regulasi</li>
                        <li>Tolak jika ada ketidaksesuaian</li>
                        <li>Keputusan Anda akan diteruskan ke Kepala Bidang</li>
                    </ul>
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    .header-page {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #1a5490;
    }

    .header-page h1 {
        color: #1a5490;
        font-weight: bold;
    }

    .timeline-simple {
        position: relative;
        padding: 0;
    }

    .timeline-item {
        display: flex;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .timeline-marker {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        margin-right: 1rem;
        flex-shrink: 0;
        margin-top: 0.25rem;
    }

    .timeline-item.completed .timeline-marker {
        background-color: #198754 !important;
    }

    .timeline-item.active .timeline-marker {
        background-color: #0d6efd !important;
    }
</style>
@endsection
