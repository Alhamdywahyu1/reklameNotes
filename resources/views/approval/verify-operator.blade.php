@extends('layouts.app')

@section('title', 'Verifikasi Dokumen - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-check-circle"></i> Verifikasi Dokumen</h1>
    <p class="text-muted">{{ $permohonan->nomor_registrasi }} - {{ $permohonan->nama_pemohon }}</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('approval.verify.store', $permohonan) }}">
                    @csrf

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-file-earmark-check"></i> Checklist Persyaratan</h5>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Persyaratan</th>
                                    <th>Lengkap?</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($persyaratan as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $item->jenis_persyaratan }}</strong>
                                            @if ($item->file_dokumen)
                                                <br>
                                                <a href="{{ asset('storage/' . $item->file_dokumen) }}" target="_blank" class="small btn-link">
                                                    <i class="bi bi-download"></i> Lihat File
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <input type="hidden" name="persyaratan[{{ $item->id }}][jenis_persyaratan]" value="{{ $item->jenis_persyaratan }}">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" 
                                                    name="persyaratan[{{ $item->id }}][is_lengkap]" 
                                                    value="1" 
                                                    id="persyaratan_{{ $item->id }}"
                                                    @if($item->is_lengkap) checked @endif>
                                                <label class="form-check-label" for="persyaratan_{{ $item->id }}">
                                                    Lengkap
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" 
                                                name="persyaratan[{{ $item->id }}][keterangan]" 
                                                rows="2" placeholder="Keterangan...">{{ $item->keterangan }}</textarea>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-pencil-square"></i> Keputusan</h5>

                    <div class="mb-3">
                        <label class="form-label">Keputusan Verifikasi <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="disetujui" value="Disetujui" required>
                                <label class="form-check-label" for="disetujui">
                                    <strong style="color: #198754;">✓ Disetujui</strong> - Dokumen lengkap, lanjut ke Kepala Seksi
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="keputusan" id="ditolak" value="Ditolak" required>
                                <label class="form-check-label" for="ditolak">
                                    <strong style="color: #dc3545;">✗ Ditolak</strong> - Dokumen tidak lengkap, kembalikan ke Pemohon
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="4" 
                            placeholder="Tuliskan keterangan atau alasan keputusan Anda..."></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan Keputusan
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
@endsection
