@extends('layouts.app')

@section('title', 'Input Persyaratan Dokumen - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-check"></i> Persyaratan Dokumen</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Daftar Persyaratan Dokumen</h5>
            </div>
            <div class="card-body">
                @if($requirements->isEmpty())
                    <p class="text-muted">Tidak ada persyaratan dokumen.</p>
                @else
                    <form id="documentsForm" method="POST" action="{{ route('document-requirements.store-multiple', $permohonan) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            @foreach($requirements as $index => $req)
                            <div class="col-md-6 mb-4">
                                <div class="card border">
                                    <div class="card-body">
                                                    <h6 class="card-title">
                                                        <i class="bi bi-file-earmark"></i> {{ $req->jenis_persyaratan }}
                                                        @if($req->jenis_persyaratan === 'Surat Kuasa')
                                                            <span class="badge bg-secondary ms-2">Opsional</span>
                                                        @else
                                                            <span class="badge bg-danger ms-2">Wajib</span>
                                                        @endif
                                                    </h6>
                                                    <p class="small text-muted mb-3">{{ $req->keterangan }}</p>

                                        @php
                                            // Logika status:
                                            // - Lengkap: sudah diverifikasi dan disetujui
                                            // - Ditolak: sudah diverifikasi dan ditolak
                                            // - Dalam Peninjauan: file sudah ada tapi belum dicheck
                                            // - Belum Lengkap: file belum diupload
                                            if ($req->status === 'Lengkap') {
                                                $displayStatus = 'Lengkap';
                                                $statusClass = 'badge bg-success';
                                            } elseif ($req->status === 'Ditolak') {
                                                $displayStatus = 'Ditolak';
                                                $statusClass = 'badge bg-danger';
                                            } elseif ($req->file_dokumen) {
                                                $displayStatus = 'Dalam Peninjauan';
                                                $statusClass = 'badge bg-info';
                                            } else {
                                                $displayStatus = 'Belum Lengkap';
                                                $statusClass = 'badge bg-warning text-dark';
                                            }
                                        @endphp
                                        <div class="mb-3">
                                            <small class="text-muted">Status:</small>
                                            <span class="{{ $statusClass }}">{{ $displayStatus }}</span>
                                        </div>

                                        @if($req->status === 'Ditolak' && $req->catatan_penolakan)
                                            <div class="alert alert-danger py-2 px-3 mb-3" style="font-size: 0.85rem;">
                                                <strong>Catatan penolakan:</strong> {{ $req->catatan_penolakan }}
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label small">Upload File</label>
                                            <input type="file" class="form-control @error("documents.$index.file") is-invalid @enderror" 
                                                name="documents[{{ $index }}][file]" 
                                                accept=".pdf,.jpg,.jpeg,.png">
                                            <small class="text-muted d-block mt-2">Format: PDF, JPG, PNG | Max 5MB</small>
                                            @error("documents.$index.file")
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror

                                            @if($req->file_dokumen)
                                                @php
                                                    $extension = strtolower(pathinfo($req->file_dokumen, PATHINFO_EXTENSION));
                                                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                                @endphp
                                                <div class="mt-2 p-2 bg-light rounded border">
                                                    <small class="text-muted d-block mb-2"><i class="bi bi-paperclip"></i> File yang sudah diupload:</small>
                                                    <div class="btn-group btn-group-sm">
                                                        @if($isImage)
                                                            <a href="{{ route('document-requirements.preview', $req) }}" class="btn btn-outline-info" title="Lihat file" target="_blank">
                                                                <i class="bi bi-eye"></i> Lihat
                                                            </a>
                                                        @endif
                                                        <a href="{{ route('document-requirements.download', $req) }}" class="btn btn-outline-primary" title="Download file">
                                                            <i class="bi bi-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <input type="hidden" name="documents[{{ $index }}][id]" value="{{ $req->id }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan Dokumen
                            </button>
                            <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Penting</h6>
            </div>
            <div class="card-body">
                <ul class="small text-muted mb-0">
                    <li>Upload semua dokumen yang diperlukan dengan format PDF, JPG, atau PNG</li>
                    <li>Ukuran file maksimal 5MB per dokumen</li>
                    <li>Dokumen yang ditandai <span class="badge bg-danger">Wajib</span> harus diupload</li>
                    <li>Dokumen yang ditandai <span class="badge bg-secondary">Opsional</span> dapat dilewati jika tidak tersedia</li>
                    <li>Jika ada dokumen yang ditolak, silakan perbaiki dan upload kembali</li>
                    <li>Petugas akan memeriksa kelengkapan dokumen Anda</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
