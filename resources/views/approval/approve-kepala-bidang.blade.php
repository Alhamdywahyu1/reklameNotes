@extends('layouts.app')

@section('title', 'Approval Kepala Bidang - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-check-circle"></i> Approval Akhir Kepala Bidang</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('approval.approve-bidang.store', $permohonan) }}">
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

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan Akhir</h5>

                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Ini adalah tahap approval akhir. Keputusan Anda akan menentukan status permohonan secara final.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui" required>
                                <label class="form-check-label" for="disetujui">
                                    <strong style="color: #198754;">✓ Disetujui</strong> - Permohonan DITERIMA dan sertifikat dapat dicetak
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak">
                                <label class="form-check-label" for="ditolak">
                                    <strong style="color: #dc3545;">✗ Ditolak</strong> - Permohonan DITOLAK, kembalikan ke pemohon
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan / Alasan Penolakan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                            id="keterangan" name="keterangan" rows="4" placeholder="Tuliskan alasan jika ditolak...">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-calendar-event"></i> Masa Berlaku Surat</h5>

                    <p class="small text-muted mb-3">Isi tanggal berlaku dan berakhir jika permohonan DISETUJUI. Dokumen ini akan otomatis ditandai kedaluwarsa setelah tanggal berakhir.</p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_berlaku" class="form-label">Tanggal Berlaku <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_berlaku') is-invalid @enderror" 
                                id="tanggal_berlaku" name="tanggal_berlaku" value="{{ old('tanggal_berlaku') }}">
                            <small class="text-muted d-block mt-1">Tanggal mulai surat persetujuan berlaku</small>
                            @error('tanggal_berlaku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_berakhir" class="form-label">Tanggal Berakhir <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_berakhir') is-invalid @enderror" 
                                id="tanggal_berakhir" name="tanggal_berakhir" value="{{ old('tanggal_berakhir') }}">
                            <small class="text-muted d-block mt-1">Tanggal akhir surat persetujuan berlaku</small>
                            @error('tanggal_berakhir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Contoh:</strong> Jika Anda menginginkan surat berlaku selama 1 tahun, set Tanggal Berlaku hari ini dan Tanggal Berakhir setahun mendatang.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Kirim Keputusan Akhir
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
                    <div class="timeline-item completed">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <p class="small fw-bold">Kepala Seksi</p>
                            <p class="small text-muted">Disetujui</p>
                        </div>
                    </div>
                    <div class="timeline-item active">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <p class="small fw-bold">Kepala Bidang</p>
                            <p class="small text-muted">Proses Approval Akhir</p>
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
                        <li>Review semua data yang telah diverifikasi</li>
                        <li>Ini adalah approval AKHIR dan tidak dapat diubah</li>
                        <li>Setujui jika semuanya sudah lengkap dan sesuai</li>
                        <li>Keputusan akan langsung dikirim kepada pemohon</li>
                        <li>Sertifikat dapat dicetak setelah approval</li>
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

@push('scripts')
<script>
    // Form validation & conditional requirement
    document.addEventListener('DOMContentLoaded', function() {
        const disetujuiRadio = document.getElementById('disetujui');
        const ditolakRadio = document.getElementById('ditolak');
        const tanggalBerlakuInput = document.getElementById('tanggal_berlaku');
        const tanggalBerakhirInput = document.getElementById('tanggal_berakhir');
        const keteranganInput = document.getElementById('keterangan');
        
        function updateFieldRequirements() {
            if (disetujuiRadio.checked) {
                tanggalBerlakuInput.required = true;
                tanggalBerakhirInput.required = true;
                tanggalBerlakuInput.closest('.col-md-6').style.opacity = '1';
                tanggalBerakhirInput.closest('.col-md-6').style.opacity = '1';
                keteranganInput.required = false;
            } else {
                tanggalBerlakuInput.required = false;
                tanggalBerakhirInput.required = false;
                tanggalBerlakuInput.closest('.col-md-6').style.opacity = '0.6';
                tanggalBerakhirInput.closest('.col-md-6').style.opacity = '0.6';
                keteranganInput.required = true;
            }
        }
        
        // Set min date untuk tanggal_berlaku to today
        const today = new Date().toISOString().split('T')[0];
        tanggalBerlakuInput.min = today;
        
        // Validate tanggal_berakhir > tanggal_berlaku
        tanggalBerlakuInput.addEventListener('change', function() {
            const berlakuDate = new Date(this.value);
            const berakhirDate = new Date(tanggalBerakhirInput.value);
            
            // Set min tanggal_berakhir to day after berlaku
            const minBerakhir = new Date(berlakuDate);
            minBerakhir.setDate(minBerakhir.getDate() + 1);
            tanggalBerakhirInput.min = minBerakhir.toISOString().split('T')[0];
            
            if (tanggalBerakhirInput.value && berakhirDate <= berlakuDate) {
                tanggalBerakhirInput.value = '';
                alert('Tanggal Berakhir harus setelah Tanggal Berlaku');
            }
        });
        
        disetujuiRadio.addEventListener('change', updateFieldRequirements);
        ditolakRadio.addEventListener('change', updateFieldRequirements);
        
        // Initial state
        updateFieldRequirements();
    });
</script>
@endpush
@endsection