@extends('layouts.app')

@section('title', 'Pemeriksaan Persyaratan Dokumen - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-check"></i> Pemeriksaan Persyaratan Dokumen</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        @if($requirements->isEmpty())
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Pemohon belum mendaftarkan persyaratan dokumen
            </div>
        @else
            <!-- Ringkasan Status -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-light text-center">
                        <div class="card-body">
                            <h4 class="text-warning">{{ $requirements->where('status', 'Belum Lengkap')->count() }}</h4>
                            <small class="text-muted">Belum Lengkap</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light text-center">
                        <div class="card-body">
                            <h4 class="text-success">{{ $requirements->where('status', 'Lengkap')->count() }}</h4>
                            <small class="text-muted">Lengkap</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light text-center">
                        <div class="card-body">
                            <h4 class="text-danger">{{ $requirements->where('status', 'Ditolak')->count() }}</h4>
                            <small class="text-muted">Ditolak</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-light text-center">
                        <div class="card-body">
                            <h4 class="text-primary">{{ $requirements->count() }}</h4>
                            <small class="text-muted">Total Dokumen</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Dokumen -->
            <div class="row">
                @foreach($requirements as $requirement)
                <div class="col-md-6 mb-4">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                        <h6 class="card-title mb-2">
                                        <i class="bi bi-file-earmark"></i> {{ $requirement->jenis_persyaratan }}
                                    </h6>
                                    <p class="small text-muted">{{ $requirement->keterangan }}</p>
                                </div>
                                @php
                                    $statusBadge = match($requirement->status) {
                                        'Lengkap' => 'bg-success',
                                        'Ditolak' => 'bg-danger',
                                        default => 'bg-warning text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }}">{{ $requirement->status }}</span>
                            </div>

                            @if(($requirement->status ?? 'Belum Lengkap') === 'Ditolak' && $requirement->catatan_penolakan)
                                <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem;">
                                    <strong>Catatan:</strong> {{ $requirement->catatan_penolakan }}
                                </div>
                            @endif

                            <div class="mb-3">
                                @if($requirement->file_dokumen)
                                    <a href="{{ route('document-requirements.download', $requirement) }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="bi bi-download"></i> Download File
                                    </a>
                                @else
                                    <small class="text-muted">Belum ada file yang diunggah</small>
                                @endif
                            </div>

                            <button type="button" class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#statusModal{{ $requirement->id }}">
                                <i class="bi bi-pencil"></i> Ubah Status
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal untuk ubah status -->
                <div class="modal fade" id="statusModal{{ $requirement->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $requirement->jenis_persyaratan }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('document-requirements.update-status', $requirement) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-select status-select" required>
                                            <option value="Belum Lengkap" @selected(($requirement->status ?? 'Belum Lengkap') === 'Belum Lengkap')>Belum Lengkap</option>
                                            <option value="Lengkap" @selected(($requirement->status ?? '') === 'Lengkap')>Lengkap</option>
                                            <option value="Ditolak" @selected(($requirement->status ?? '') === 'Ditolak')>Ditolak</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 catatan-section" style="display: none;">
                                        <label class="form-label">Catatan Penolakan</label>
                                        <textarea name="catatan_penolakan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan...">{{ $requirement->catatan_penolakan }}</textarea>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
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
