@extends('layouts.app')

@section('title', 'Approval Kepala Bidang - ' . $permohonan->nomor_registrasi)

@push('styles')
<style>
    .doc-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .doc-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    .doc-card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .doc-card-body {
        padding: 1.25rem;
    }

    .doc-preview {
        width: 100%;
        height: 200px;
        background: #f8fafc;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        overflow: hidden;
        border: 2px dashed #e5e7eb;
    }

    .doc-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .doc-preview-empty {
        text-align: center;
        color: #9ca3af;
    }

    .doc-preview-empty i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }

    .info-sidebar {
        position: sticky;
        top: 20px;
    }
</style>
@endpush

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

                    <ul class="nav nav-tabs mb-4" id="verifikasiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">
                                <i class="bi bi-person-vcard"></i> Detail Pemohon
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" type="button" role="tab">
                                <i class="bi bi-file-earmark-check"></i> Dokumen & Final Approval
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="verifikasiTabContent">
                        <div class="tab-pane fade show active" id="detail" role="tabpanel">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-info-circle"></i> Data Permohonan</h5>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Nama Pemohon</label>
                                    <p class="mb-0"><strong>{{ $permohonan->nama_pemohon }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">NIK</label>
                                    <p class="mb-0"><strong>{{ $permohonan->nik }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Alamat</label>
                                    <p class="mb-0">{{ $permohonan->alamat_pemohon }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Nomor Telepon</label>
                                    <p class="mb-0">{{ $permohonan->nomor_telepon }}</p>
                                </div>
                                @if ($permohonan->npwp)
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">NPWP</label>
                                        <p class="mb-0">{{ $permohonan->npwp }}</p>
                                    </div>
                                @endif
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-signpost-split"></i> Data Reklame</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Jenis Reklame</label>
                                    <p class="mb-0"><strong>{{ $permohonan->jenis_reklame }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Jumlah Reklame</label>
                                    <p class="mb-0"><strong>{{ $permohonan->jumlah_reklame }}</strong></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Ukuran Reklame</label>
                                    <p class="mb-0">{{ $permohonan->ukuran_reklame }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small">Narasi Reklame</label>
                                    <p class="mb-0">{{ $permohonan->narasi_reklame }}</p>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="text-muted small">Lokasi Pemasangan</label>
                                    <p class="mb-0">{{ $permohonan->lokasi_pemasangan }}</p>
                                </div>
                            </div>

                            @if ($permohonan->status === 'Disetujui Kepala Bidang')
                                <hr class="my-4">
                                <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-calendar-event"></i> Masa Berlaku Surat</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Tanggal Berlaku</label>
                                        <p class="mb-0"><strong>{{ $permohonan->tanggal_berlaku ? \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->format('d F Y') : '-' }}</strong></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="text-muted small">Tanggal Berakhir</label>
                                        <p class="mb-0"><strong>{{ $permohonan->tanggal_berakhir ? \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->format('d F Y') : '-' }}</strong></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <label class="text-muted small">Status Kedaluwarsa</label>
                                        @php $statusKedaluarsa = $permohonan->getStatusKedaluarsa(); @endphp
                                        <p class="mb-0">
                                            @if($statusKedaluarsa === 'Aktif')
                                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> AKTIF</span>
                                                <small class="text-muted d-block mt-2">Surat masih berlaku dan dapat digunakan.</small>
                                            @elseif($statusKedaluarsa === 'Kedaluwarsa')
                                                <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> KEDALUWARSA</span>
                                                <small class="text-muted d-block mt-2">Surat sudah tidak berlaku dan perlu pembaruan.</small>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> DICABUT</span>
                                                <small class="text-muted d-block mt-2">Surat telah dicabut oleh pihak berwenang.</small>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="tab-pane fade" id="dokumen" role="tabpanel">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-file-earmark"></i> Daftar Dokumen Persyaratan</h5>

                            @if($persyaratan->isEmpty())
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Belum ada dokumen yang diupload oleh pemohon.
                                </div>
                            @else
                                <div class="row">
                                    @foreach($persyaratan as $item)
                                        <div class="col-md-6 mb-3">
                                            <div class="card border">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $item->jenis_persyaratan }}</h6>

                                                    @if($item->file_dokumen)
                                                        <p class="mb-2 small text-muted">{{ basename($item->file_dokumen) }}</p>
                                                    @endif

                                                    <div class="mb-2">
                                                        <label class="form-label small mb-2"><strong>Status Dokumen:</strong></label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="persyaratan[{{ $item->id }}][status]" id="lengkap_{{ $item->id }}" value="Lengkap" {{ $item->status === 'Lengkap' ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="lengkap_{{ $item->id }}"><span style="color: #198754;">✓ Lengkap</span></label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="persyaratan[{{ $item->id }}][status]" id="belum_{{ $item->id }}" value="Belum Lengkap" {{ $item->status === 'Belum Lengkap' || $item->status !== 'Lengkap' ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="belum_{{ $item->id }}"><span style="color: #ffc107;">⚠ Belum Lengkap</span></label>
                                                        </div>
                                                    </div>

                                                    @if($item->file_dokumen)
                                                        <div class="d-flex gap-2 mt-2">
                                                            <a href="{{ route('document-requirements.download', $item) }}" class="btn btn-sm btn-outline-primary" title="Download">
                                                                <i class="bi bi-download"></i> Download
                                                            </a>
                                                            @php
                                                                $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                                                $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                                $isPdf = $fileExt === 'pdf';
                                                            @endphp
                                                            @if($isImage)
                                                                <a href="{{ route('document-requirements.preview', $item) }}" class="btn btn-sm btn-outline-info" target="_blank" title="Preview">
                                                                    <i class="bi bi-eye"></i> Preview
                                                                </a>
                                                            @elseif($isPdf)
                                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}" title="Preview PDF">
                                                                    <i class="bi bi-eye"></i> Preview
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <hr class="my-4">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-calendar-event"></i> Cek Masa Berlaku (Ditetapkan Operator)</h5>

                            @if($permohonan->tanggal_berlaku && $permohonan->tanggal_berakhir)
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-2">
                                        <label class="text-muted small">Tanggal Berlaku</label>
                                        <p class="mb-0"><strong>{{ \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->format('d F Y') }}</strong></p>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="text-muted small">Tanggal Berakhir</label>
                                        <p class="mb-0"><strong>{{ \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->format('d F Y') }}</strong></p>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Masa berlaku belum diatur Operator. Approval tidak dapat dilanjutkan sebelum data ini diisi.
                                </div>
                            @endif

                            <hr class="my-4">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan Final</h5>

                            <div class="mb-3">
                                <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                <div id="keputusanContainer">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui" required>
                                        <label class="form-check-label" for="disetujui">
                                            <strong style="color: #198754;">✓ Disetujui</strong> - Lanjut ke status final
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak">
                                        <label class="form-check-label" for="ditolak">
                                            <strong style="color: #dc3545;">✗ Ditolak</strong> - Kembalikan ke pemohon
                                        </label>
                                    </div>
                                </div>
                                <small class="text-danger d-none" id="keputusanError">Harus memilih keputusan (Disetujui atau Ditolak)</small>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan <span class="text-danger" id="keteranganRequired" style="display: none;">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="4" placeholder="Tuliskan keterangan atau alasan keputusan Anda..."></textarea>
                                <small class="text-danger d-none" id="keteranganError">Keterangan wajib diisi jika memilih Ditolak</small>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                    <i class="bi bi-check-circle"></i> Simpan Keputusan
                                </button>
                                <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                                <div class="ms-auto">
                                    <small id="autoSaveIndicator" class="text-success d-none">
                                        <i class="bi bi-check-circle-fill"></i> Tersimpan otomatis
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top info-sidebar" style="top: 20px;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Permohonan</h5>
            </div>
            <div class="card-body small">
                <p class="mb-2"><strong>Nomor Registrasi:</strong><br>{{ $permohonan->nomor_registrasi }}</p>
                <p class="mb-2"><strong>Nama Pemohon:</strong><br>{{ $permohonan->nama_pemohon }}</p>
                <p class="mb-2"><strong>NIK:</strong><br>{{ $permohonan->nik }}</p>
                <p class="mb-2"><strong>Jenis Reklame:</strong><br>{{ $permohonan->jenis_reklame }}</p>
                <p class="mb-2"><strong>Lokasi:</strong><br>{{ $permohonan->lokasi_pemasangan }}</p>
                <hr>
                <p class="mb-0 text-muted"><strong>Tanggal Pengajuan:</strong><br>{{ $permohonan->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>

@foreach($persyaratan as $item)
    @if($item->file_dokumen)
        @php
            $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
            $isPdf = $fileExt === 'pdf';
        @endphp
        @if($isPdf)
        <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $item->id }}" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewModalLabel{{ $item->id }}">Preview {{ $item->jenis_persyaratan }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0" style="height: 80vh;">
                        <iframe src="{{ route('document-requirements.preview', $item) }}" width="100%" height="100%" style="border:0;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const keputusanRadios = document.querySelectorAll('input[name="keputusan"]');
    const keteranganField = document.getElementById('keterangan');
    const keteranganRequired = document.getElementById('keteranganRequired');
    const keteranganError = document.getElementById('keteranganError');
    const keputusanError = document.getElementById('keputusanError');

    function validateForm() {
        const keputusanSelected = Array.from(keputusanRadios).some(radio => radio.checked);
        const ditolakSelected = document.getElementById('ditolak').checked;
        const keteranganFilled = keteranganField.value.trim() !== '';

        let isValid = keputusanSelected;

        if (ditolakSelected && !keteranganFilled) {
            isValid = false;
            keteranganError.classList.remove('d-none');
        } else {
            keteranganError.classList.add('d-none');
        }

        if (!keputusanSelected) {
            keputusanError.classList.remove('d-none');
        } else {
            keputusanError.classList.add('d-none');
        }

        keteranganField.required = ditolakSelected;
        keteranganRequired.style.display = ditolakSelected ? 'inline' : 'none';
        submitBtn.disabled = !isValid;
    }

    keputusanRadios.forEach(radio => {
        radio.addEventListener('change', validateForm);
    });

    keteranganField.addEventListener('input', validateForm);

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const keputusanSelected = Array.from(keputusanRadios).some(radio => radio.checked);
        const ditolakSelected = document.getElementById('ditolak').checked;
        const keteranganFilled = keteranganField.value.trim() !== '';

        if (!keputusanSelected) {
            keputusanError.classList.remove('d-none');
            return;
        }

        if (ditolakSelected && !keteranganFilled) {
            keteranganError.classList.remove('d-none');
            return;
        }

        form.submit();
    });

    validateForm();
});
</script>
@endpush