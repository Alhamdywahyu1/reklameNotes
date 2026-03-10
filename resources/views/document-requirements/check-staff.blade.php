@extends('layouts.app')

@section('title', 'Pemeriksaan Persyaratan Dokumen - ' . $permohonan->nomor_registrasi)

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
    
    .status-summary-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .status-summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .status-summary-card h4 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .btn-status-action {
        padding: 0.5rem 1rem;
        font-weight: 600;
        border-radius: 6px;
    }
    
    .btn-approve {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
    }
    
    .btn-approve:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        color: white;
    }
    
    .btn-reject {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
    }
    
    .btn-reject:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: white;
    }
    
    .btn-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
    }
    
    .btn-pending:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
    }
    
    .info-sidebar {
        position: sticky;
        top: 20px;
    }
</style>
@endpush

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-file-earmark-check"></i> Pemeriksaan Dokumen</h1>
            <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Detail
            </a>
            
            @if(auth()->user()->hasRole('operator') && !$requirements->isEmpty())
                @php
                    $allApproved = $requirements->every(fn($r) => $r->status === 'Lengkap');
                @endphp
                @if($allApproved && $permohonan->canBeApprovedByOperator())
                    <a href="{{ route('approval.verify', $permohonan) }}" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Verifikasi
                    </a>
                @else
                    <button type="button" class="btn btn-success" disabled title="Semua dokumen harus disetujui terlebih dahulu">
                        <i class="bi bi-check-circle"></i> Verifikasi
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-9">
        @if($requirements->isEmpty())
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> <strong>Tidak ada dokumen.</strong> Pemohon belum mendaftarkan persyaratan dokumen apapun.
            </div>
        @else
            <!-- Ringkasan Status -->
            <div class="row mb-4">
                <div class="col-md-3 col-6 mb-3">
                    <div class="status-summary-card">
                        <h4 class="text-warning">{{ $requirements->where('status', 'Belum Lengkap')->count() }}</h4>
                        <small class="text-muted fw-semibold">Belum Lengkap</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="status-summary-card">
                        <h4 class="text-success">{{ $requirements->where('status', 'Lengkap')->count() }}</h4>
                        <small class="text-muted fw-semibold">Lengkap</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="status-summary-card">
                        <h4 class="text-danger">{{ $requirements->where('status', 'Ditolak')->count() }}</h4>
                        <small class="text-muted fw-semibold">Ditolak</small>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="status-summary-card">
                        <h4 class="text-primary">{{ $requirements->count() }}</h4>
                        <small class="text-muted fw-semibold">Total Dokumen</small>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            @php
                $completedCount = $requirements->where('status', 'Lengkap')->count();
                $totalCount = $requirements->count();
                $percentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
            @endphp
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-semibold">Progress Kelengkapan Dokumen</span>
                        <span class="badge bg-primary">{{ $percentage }}%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">{{ $completedCount }} dari {{ $totalCount }} dokumen sudah lengkap</small>
                </div>
            </div>

            <!-- Daftar Dokumen -->
            <div class="row">
                @foreach($requirements as $requirement)
                <div class="col-md-6 mb-4">
                    <div class="doc-card">
                        <div class="doc-card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-file-earmark-text"></i> {{ $requirement->jenis_persyaratan }}
                            </h6>
                            @php
                                $statusBadge = match($requirement->status) {
                                    'Lengkap' => 'bg-success',
                                    'Ditolak' => 'bg-danger',
                                    default => 'bg-warning text-dark'
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }}">{{ $requirement->status }}</span>
                        </div>
                        
                        <div class="doc-card-body">
                            <!-- Preview Dokumen -->
                            <div class="doc-preview">
                                @if($requirement->file_dokumen)
                                    @php
                                        $extension = pathinfo($requirement->file_dokumen, PATHINFO_EXTENSION);
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']);
                                    @endphp
                                    @if($isImage)
                                        <img src="{{ route('document-requirements.preview', $requirement) }}" alt="{{ $requirement->jenis_persyaratan }}" onerror="this.parentElement.innerHTML='<div class=\'doc-preview-empty\'><i class=\'bi bi-file-earmark-image\'></i><p>Gagal memuat gambar</p></div>'">
                                    @else
                                        <div class="doc-preview-empty">
                                            <i class="bi bi-file-earmark-pdf text-danger"></i>
                                            <p class="mb-0">File PDF</p>
                                            <small>Klik download untuk melihat</small>
                                        </div>
                                    @endif
                                @else
                                    <div class="doc-preview-empty">
                                        <i class="bi bi-cloud-upload"></i>
                                        <p class="mb-0">Belum Ada File</p>
                                        <small>Pemohon belum mengunggah</small>
                                    </div>
                                @endif
                            </div>

                            @if($requirement->keterangan)
                                <p class="small text-muted mb-3"><i class="bi bi-info-circle"></i> {{ $requirement->keterangan }}</p>
                            @endif

                            @if(($requirement->status ?? 'Belum Lengkap') === 'Ditolak' && $requirement->catatan_penolakan)
                                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem;">
                                    <strong><i class="bi bi-exclamation-triangle"></i> Catatan Penolakan:</strong><br>
                                    {{ $requirement->catatan_penolakan }}
                                </div>
                            @endif

                            <!-- Tombol Aksi -->
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#statusModal{{ $requirement->id }}">
                                    <i class="bi bi-search"></i> Periksa Dokumen
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk ubah status -->
                <div class="modal fade" id="statusModal{{ $requirement->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><i class="bi bi-pencil"></i> {{ $requirement->jenis_persyaratan }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('document-requirements.update-status', $requirement) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="modal-body">
                                    @if($requirement->file_dokumen)
                                        @php
                                            $modalExt = strtolower(pathinfo($requirement->file_dokumen, PATHINFO_EXTENSION));
                                            $modalIsImg = in_array($modalExt, ['jpg', 'jpeg', 'png', 'gif']);
                                        @endphp
                                        <div class="mb-3">
                                            @if($modalIsImg)
                                                <a href="{{ route('document-requirements.preview', $requirement) }}" class="btn btn-outline-info btn-sm w-100" target="_blank">
                                                    <i class="bi bi-eye"></i> Lihat Dokumen
                                                </a>
                                            @else
                                                <a href="{{ route('document-requirements.download', $requirement) }}" class="btn btn-outline-info btn-sm w-100" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf"></i> Buka Dokumen PDF
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select status-select" required>
                                            <option value="Belum Lengkap" @selected(($requirement->status ?? 'Belum Lengkap') === 'Belum Lengkap')>⏳ Belum Lengkap</option>
                                            <option value="Lengkap" @selected(($requirement->status ?? '') === 'Lengkap')>✅ Lengkap</option>
                                            <option value="Ditolak" @selected(($requirement->status ?? '') === 'Ditolak')>❌ Ditolak</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 catatan-section" style="display: none;">
                                        <label class="form-label fw-semibold">Catatan Penolakan</label>
                                        <textarea name="catatan_penolakan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan dokumen ini...">{{ $requirement->catatan_penolakan }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk penolakan cepat -->
                <div class="modal fade" id="rejectModal{{ $requirement->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title"><i class="bi bi-x-circle"></i> Tolak Dokumen</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('document-requirements.update-status', $requirement) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="Ditolak">
                                
                                <div class="modal-body">
                                    <p class="mb-3">Anda akan menolak dokumen: <strong>{{ $requirement->jenis_persyaratan }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                        <textarea name="catatan_penolakan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan dokumen ini..." required>{{ $requirement->catatan_penolakan }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle"></i> Tolak Dokumen
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-3">
        <div style="position: sticky; top: 20px; z-index: 10;">
            <div class="card info-sidebar">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Info Permohonan</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">No. Registrasi</small>
                        <strong>{{ $permohonan->nomor_registrasi }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Nama Pemohon</small>
                        <strong>{{ $permohonan->nama_pemohon }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">NIK</small>
                        <strong>{{ $permohonan->nik }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Jenis Reklame</small>
                        <span class="badge bg-info">{{ $permohonan->jenis_reklame }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Status</small>
                        <span class="badge bg-primary">{{ $permohonan->status }}</span>
                    </div>
                    <hr>
                    <div class="mb-0">
                        <small class="text-muted d-block">Tanggal Pengajuan</small>
                        <strong>{{ $permohonan->created_at->format('d M Y H:i') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const modal = this.closest('.modal-content');
            const catatanSection = modal.querySelector('.catatan-section');
            
            if (this.value === 'Ditolak') {
                catatanSection.style.display = 'block';
            } else {
                catatanSection.style.display = 'none';
            }
        });

        // Trigger on load untuk menampilkan catatan jika status Ditolak
        select.dispatchEvent(new Event('change'));
    });
</script>
@endpush
@endsection
