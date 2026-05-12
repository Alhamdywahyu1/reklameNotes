@extends('layouts.app')

@section('title', 'Approval Dokumen - Kepala Seksi - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-check-circle"></i> Approval Dokumen - Kepala Seksi</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('approval.approve-seksi.store', $permohonan) }}">
                    @csrf

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-4" id="verifikasiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" 
                                type="button" role="tab">
                                <i class="bi bi-person-vcard"></i> Detail Pemohon
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" 
                                type="button" role="tab">
                                <i class="bi bi-file-earmark-check"></i> Dokumen & Verifikasi
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="verifikasiTabContent">
                        <!-- Tab 1: Detail Pemohon -->
                        <div class="tab-pane fade show active" id="detail" role="tabpanel">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-person"></i> Data Pemohon</h5>
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

                        <!-- Tab 2: Dokumen & Verifikasi -->
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
                                                <h6 class="card-title">
                                                    {{ $item->jenis_persyaratan }}
                                                    @if($item->is_optional)
                                                        <span class="badge bg-warning text-dark">(opsional)</span>
                                                    @else
                                                        <span class="badge bg-danger">(wajib)</span>
                                                    @endif
                                                </h6>
                                                
                                                @if($item->file_dokumen)
                                                    <p class="mb-2 small text-muted">
                                                        {{ basename($item->file_dokumen) }}
                                                    </p>
                                                @endif

                                                <!-- Radio Button Status -->
                                                <div class="mb-2">
                                                    <label class="form-label small mb-2"><strong>Status Dokumen:</strong></label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" 
                                                            name="persyaratan[{{ $item->id }}][status]" 
                                                            id="lengkap_{{ $item->id }}" value="Lengkap"
                                                            {{ $item->status === 'Lengkap' ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="lengkap_{{ $item->id }}">
                                                            <span style="color: #198754;">✓ Lengkap</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" 
                                                            name="persyaratan[{{ $item->id }}][status]" 
                                                            id="belum_{{ $item->id }}" value="Belum Lengkap"
                                                            {{ $item->status === 'Belum Lengkap' || $item->status !== 'Lengkap' ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="belum_{{ $item->id }}">
                                                            <span style="color: #ffc107;">⚠ Belum Lengkap</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                @if($item->file_dokumen)
                                                    <div class="d-flex gap-2 mt-2">
                                                        <a href="{{ route('document-requirements.download', $item) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="Download">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                        @php
                                                            $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                                            $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            $isPdf = $fileExt === 'pdf';
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="{{ route('document-requirements.preview', $item) }}" 
                                                                class="btn btn-sm btn-outline-info" target="_blank" title="Preview">
                                                                <i class="bi bi-eye"></i> Preview
                                                            </a>
                                                        @elseif($isPdf)
                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}" title="Preview PDF">
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

                            <!-- Keputusan Section (Moved from separate tab) -->
                            <hr class="my-4">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan Approval</h5>

                            <div class="mb-3">
                                <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                <div id="keputusanContainer">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui">
                                        <label class="form-check-label" for="disetujui">
                                            <strong style="color: #198754;">✓ Disetujui</strong> - Lanjut ke Kepala Bidang
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak">
                                        <label class="form-check-label" for="ditolak">
                                            <strong style="color: #dc3545;">✗ Ditolak</strong> - Kembalikan ke Operator
                                        </label>
                                    </div>
                                </div>
                                <small class="text-danger d-none" id="keputusanError">Harus memilih keputusan (Disetujui atau Ditolak)</small>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan <span class="text-danger" id="keteranganRequired" style="display: none;">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                                    placeholder="Tuliskan keterangan atau alasan keputusan Anda..."></textarea>
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
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Permohonan</h5>
            </div>
            <div class="card-body small">
                <p class="mb-2">
                    <strong>Nomor Registrasi:</strong><br>
                    {{ $permohonan->nomor_registrasi }}
                </p>
                <p class="mb-2">
                    <strong>Nama Pemohon:</strong><br>
                    {{ $permohonan->nama_pemohon }}
                </p>
                <p class="mb-2">
                    <strong>NIK:</strong><br>
                    {{ $permohonan->nik }}
                </p>
                <p class="mb-2">
                    <strong>Jenis Reklame:</strong><br>
                    {{ $permohonan->jenis_reklame }}
                </p>
                <p class="mb-2">
                    <strong>Lokasi:</strong><br>
                    {{ $permohonan->lokasi_pemasangan }}
                </p>
                <hr>
                <p class="mb-0 text-muted">
                    <strong>Tanggal Pengajuan:</strong><br>
                    {{ $permohonan->created_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- PDF Preview Modals -->
@foreach($persyaratan as $item)
        @php
            $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
            $isPdf = $fileExt === 'pdf';
        @endphp
        @if($isPdf)
            <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel{{ $item->id }}">
                                <i class="bi bi-file-pdf"></i> Preview: {{ $item->jenis_persyaratan }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <embed src="{{ route('document-requirements.preview', $item) }}" type="application/pdf" width="100%" height="600px" />
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('document-requirements.download', $item) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

<!-- Auto-save and Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const keputusanRadios = document.querySelectorAll('input[name="keputusan"]');
    const keteranganField = document.getElementById('keterangan');
    const keteranganRequired = document.getElementById('keteranganRequired');
    const keteranganError = document.getElementById('keteranganError');
    const keputusanError = document.getElementById('keputusanError');
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const permohonanId = {{ $permohonan->id }};

    // Auto-save persyaratan status
    const statusRadios = document.querySelectorAll('input[name^="persyaratan["][name$="][status]"]');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const persyaratanId = this.name.match(/\d+/)[0];
            const status = this.value;

            fetch(`{{ route('approval.persyaratan-status', $permohonan) }}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    persyaratan_id: persyaratanId,
                    status: status,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show auto-save indicator
                    autoSaveIndicator.classList.remove('d-none');
                    setTimeout(() => {
                        autoSaveIndicator.classList.add('d-none');
                    }, 3000);
                } else {
                    console.error('Auto-save failed:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Validation for keputusan
    keputusanRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            validateForm();
            if (this.value === 'Ditolak') {
                keteranganRequired.style.display = 'inline';
            } else {
                keteranganRequired.style.display = 'none';
                keteranganError.classList.add('d-none');
            }
        });
    });

    // Validation for keterangan
    keteranganField.addEventListener('input', function() {
        validateForm();
    });

    // Form validation
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

        submitBtn.disabled = !isValid;
    }
});
</script>
@endsection
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('approval.approve-seksi.store', $permohonan) }}">
                    @csrf

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-4" id="verifikasiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" 
                                type="button" role="tab">
                                <i class="bi bi-person-vcard"></i> Detail Pemohon
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="dokumen-tab" data-bs-toggle="tab" data-bs-target="#dokumen" 
                                type="button" role="tab">
                                <i class="bi bi-file-earmark-check"></i> Dokumen & Verifikasi
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content" id="verifikasiTabContent">
                        <!-- Tab 1: Detail Pemohon -->
                        <div class="tab-pane fade show active" id="detail" role="tabpanel">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-person"></i> Data Pemohon</h5>
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

                        <!-- Tab 2: Dokumen & Verifikasi -->
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
                                                <h6 class="card-title">
                                                    {{ $item->jenis_persyaratan }}
                                                </h6>
                                                
                                                @if($item->file_dokumen)
                                                    <p class="mb-2 small text-muted">
                                                        {{ basename($item->file_dokumen) }}
                                                    </p>
                                                @endif

                                                <!-- Radio Button Status -->
                                                <div class="mb-2">
                                                    <label class="form-label small mb-2"><strong>Status Dokumen:</strong></label>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" 
                                                            name="persyaratan[{{ $item->id }}][status]" 
                                                            id="lengkap_{{ $item->id }}" value="Lengkap"
                                                            {{ $item->status === 'Lengkap' ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="lengkap_{{ $item->id }}">
                                                            <span style="color: #198754;">✓ Lengkap</span>
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" 
                                                            name="persyaratan[{{ $item->id }}][status]" 
                                                            id="belum_{{ $item->id }}" value="Belum Lengkap"
                                                            {{ $item->status === 'Belum Lengkap' || $item->status !== 'Lengkap' ? 'checked' : '' }}>
                                                        <label class="form-check-label small" for="belum_{{ $item->id }}">
                                                            <span style="color: #ffc107;">⚠ Belum Lengkap</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Action Buttons -->
                                                @if($item->file_dokumen)
                                                    <div class="d-flex gap-2 mt-2">
                                                        <a href="{{ route('document-requirements.download', $item) }}" 
                                                            class="btn btn-sm btn-outline-primary" title="Download">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                        @php
                                                            $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                                            $isImage = in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                            $isPdf = $fileExt === 'pdf';
                                                        @endphp
                                                        @if($isImage)
                                                            <a href="{{ route('document-requirements.preview', $item) }}" 
                                                                class="btn btn-sm btn-outline-info" target="_blank" title="Preview">
                                                                <i class="bi bi-eye"></i> Preview
                                                            </a>
                                                        @elseif($isPdf)
                                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" data-bs-target="#previewModal{{ $item->id }}" title="Preview PDF">
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

                            <!-- Keputusan Section -->
                            <hr class="my-4">
                            <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan Approval</h5>

                            <div class="mb-3">
                                <label class="form-label">Keputusan <span class="text-danger">*</span></label>
                                <div id="keputusanContainer">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui">
                                        <label class="form-check-label" for="disetujui">
                                            <strong style="color: #198754;">✓ Disetujui</strong> - Dokumen lengkap, lanjut ke Kepala Bidang
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak">
                                        <label class="form-check-label" for="ditolak">
                                            <strong style="color: #dc3545;">✗ Ditolak</strong> - Dokumen tidak lengkap, kembalikan ke Operator
                                        </label>
                                    </div>
                                </div>
                                <small class="text-danger d-none" id="keputusanError">Harus memilih keputusan (Disetujui atau Ditolak)</small>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="form-label">Keterangan <span class="text-danger" id="keteranganRequired" style="display: none;">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                                    placeholder="Tuliskan keterangan atau alasan keputusan Anda..."></textarea>
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
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Permohonan</h5>
            </div>
            <div class="card-body small">
                <p class="mb-2">
                    <strong>Nomor Registrasi:</strong><br>
                    {{ $permohonan->nomor_registrasi }}
                </p>
                <p class="mb-2">
                    <strong>Nama Pemohon:</strong><br>
                    {{ $permohonan->nama_pemohon }}
                </p>
                <p class="mb-2">
                    <strong>NIK:</strong><br>
                    {{ $permohonan->nik }}
                </p>
                <p class="mb-2">
                    <strong>Jenis Reklame:</strong><br>
                    {{ $permohonan->jenis_reklame }}
                </p>
                <p class="mb-2">
                    <strong>Lokasi:</strong><br>
                    {{ $permohonan->lokasi_pemasangan }}
                </p>
                <hr>
                <p class="mb-0 text-muted">
                    <strong>Tanggal Pengajuan:</strong><br>
                    {{ $permohonan->created_at->format('d M Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- PDF Preview Modals -->
@foreach($persyaratan as $item)
        @php
            $fileExt = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
            $isPdf = $fileExt === 'pdf';
        @endphp
        @if($isPdf)
            <div class="modal fade" id="previewModal{{ $item->id }}" tabindex="-1" aria-labelledby="previewModalLabel{{ $item->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel{{ $item->id }}">
                                <i class="bi bi-file-pdf"></i> Preview: {{ $item->jenis_persyaratan }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <embed src="{{ route('document-requirements.preview', $item) }}" type="application/pdf" width="100%" height="600px" />
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('document-requirements.download', $item) }}" class="btn btn-primary">
                                <i class="bi bi-download"></i> Download
                            </a>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

<!-- Auto-save and Validation Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const keputusanRadios = document.querySelectorAll('input[name="keputusan"]');
    const keteranganField = document.getElementById('keterangan');
    const keteranganRequired = document.getElementById('keteranganRequired');
    const keteranganError = document.getElementById('keteranganError');
    const keputusanError = document.getElementById('keputusanError');
    const autoSaveIndicator = document.getElementById('autoSaveIndicator');
    const permohonanId = {{ $permohonan->id }};

    // Auto-save persyaratan status
    const statusRadios = document.querySelectorAll('input[name^="persyaratan["][name$="][status]"]');
    statusRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const persyaratanId = this.name.match(/\d+/)[0];
            const status = this.value;

            fetch(`{{ route('approval.persyaratan-status', $permohonan) }}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    persyaratan_id: persyaratanId,
                    status: status,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show auto-save indicator
                    autoSaveIndicator.classList.remove('d-none');
                    setTimeout(() => {
                        autoSaveIndicator.classList.add('d-none');
                    }, 3000);
                } else {
                    console.error('Auto-save failed:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Validation for keputusan
    keputusanRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            validateForm();
            if (this.value === 'Ditolak') {
                keteranganRequired.style.display = 'inline';
            } else {
                keteranganRequired.style.display = 'none';
                keteranganError.classList.add('d-none');
            }
        });
    });

    // Validation for keterangan
    keteranganField.addEventListener('input', function() {
        validateForm();
    });

    // Form validation
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

        submitBtn.disabled = !isValid;
    }

    // Form submission
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

        // Submit the form if all validations pass
        form.submit();
    });

    // Initial validation
    validateForm();
});
</script>
