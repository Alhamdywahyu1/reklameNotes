@extends('layouts.app')

@section('title', 'Form Pendaftaran Step 2 - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-form-check"></i> Form Pendaftaran Step 2</h1>
    <p class="text-muted">Melengkapi data detail reklame Anda - Nomor Registrasi: <strong>{{ $permohonan->nomor_registrasi }}</strong></p>
</div>

<!-- Progress Bar -->
<div class="mb-5">
    <div class="d-flex justify-content-between mb-3">
        <div class="text-center flex-grow-1">
            <div class="badge bg-success rounded-circle p-2 mb-2" style="font-size: 0.9rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="bi bi-check"></i>
            </div>
            <p class="small fw-bold">Step 1: Data Pemohon</p>
        </div>
        <div class="text-center flex-grow-1">
            <div class="badge bg-primary rounded-circle p-2 mb-2" style="font-size: 0.9rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                2
            </div>
            <p class="small fw-bold">Step 2: Detail Reklame</p>
        </div>
        <div class="text-center flex-grow-1">
            <div class="badge bg-secondary rounded-circle p-2 mb-2" style="font-size: 0.9rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                3
            </div>
            <p class="small fw-bold">Step 3: Surat Pernyataan</p>
        </div>
    </div>
    <div class="progress" style="height: 3px;">
        <div class="progress-bar" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>

<div class="row g-5">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-5">
                <form method="POST" action="{{ route('permohonan.updateStep2', $permohonan) }}">
                    @csrf
                    @method('PUT')

                    <!-- Data Pemohon (Read-only reference) -->
                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-person"></i> Referensi Data Pemohon</h5>
                    
                    <div class="mb-4 p-3 bg-light border-start border-primary ps-3">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">Nama Pemohon</small>
                                <strong>{{ $permohonan->nama_pemohon }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">NIK</small>
                                <strong>{{ $permohonan->nik }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">Nomor Telepon</small>
                                <strong>{{ $permohonan->nomor_telepon }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <small class="text-muted d-block">Alamat</small>
                                <strong>{{ substr($permohonan->alamat_pemohon, 0, 50) }}...</strong>
                            </div>
                        </div>
                    </div>

                    <hr class="my-5">

                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-briefcase"></i> Informasi Pekerjaan & Status Reklame</h5>

                    <div class="mb-4">
                        <label for="pekerjaan" class="form-label fw-600">Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" 
                            id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $permohonan->pekerjaan) }}" 
                            placeholder="Contoh: Pemilik Usaha, Wiraswasta, dll" required>
                        @error('pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status_reklame" class="form-label fw-600">Status Reklame <span class="text-danger">*</span></label>
                        <select class="form-select @error('status_reklame') is-invalid @enderror" 
                            id="status_reklame" name="status_reklame" required>
                            <option value="">-- Pilih --</option>
                            <option value="Baru" @if(old('status_reklame', $permohonan->status_reklame) === 'Baru') selected @endif>Baru</option>
                            <option value="Perpanjangan" @if(old('status_reklame', $permohonan->status_reklame) === 'Perpanjangan') selected @endif>Perpanjangan</option>
                        </select>
                        @error('status_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-5">

                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-signpost-split"></i> Detail Reklame</h5>

                    <div class="mb-4">
                        <label for="nama_reklame" class="form-label fw-600">Nama Reklame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_reklame') is-invalid @enderror" 
                            id="nama_reklame" name="nama_reklame" value="{{ old('nama_reklame', $permohonan->nama_reklame) }}" 
                            placeholder="Contoh: Reklame Toko ABC, Billboard Produk XYZ" required>
                        @error('nama_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="alamat_perusahaan" class="form-label fw-600">Alamat Perusahaan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat_perusahaan') is-invalid @enderror" 
                            id="alamat_perusahaan" name="alamat_perusahaan" rows="3" required>{{ old('alamat_perusahaan', $permohonan->alamat_perusahaan) }}</textarea>
                        @error('alamat_perusahaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="jumlah_warna" class="form-label fw-600">Jumlah Warna <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_warna') is-invalid @enderror" 
                                id="jumlah_warna" name="jumlah_warna" value="{{ old('jumlah_warna', $permohonan->jumlah_warna) }}" 
                                min="1" required>
                            @error('jumlah_warna')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="rata_rata" class="form-label fw-600">Rata-rata <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('rata_rata') is-invalid @enderror" 
                                id="rata_rata" name="rata_rata" value="{{ old('rata_rata', $permohonan->rata_rata) }}" 
                                placeholder="Contoh: Hitam, RGB, dll" required>
                            @error('rata_rata')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="masa_berlaku" class="form-label fw-600">Masa Berlaku <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('masa_berlaku') is-invalid @enderror" 
                            id="masa_berlaku" name="masa_berlaku" value="{{ old('masa_berlaku', $permohonan->masa_berlaku) }}" required>
                        <small class="text-muted d-block mt-1">Pilih tanggal sampai kapan reklame ini berlaku</small>
                        @error('masa_berlaku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-5">

                    <div class="d-flex gap-2">
                        <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary" style="background: #cbd5e1; color: #0f172a; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-check-circle"></i> Lanjut ke Step 3
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-light h-100 sticky-top" style="top: 20px;">
            <div class="card-body p-4">
                <h5 class="card-title mb-3"><i class="bi bi-info-circle"></i> Panduan Pengisian</h5>
                
                <div class="mb-4">
                    <h6 class="small mb-2"><strong>Pekerjaan</strong></h6>
                    <p class="small text-muted">Isi dengan jenis pekerjaan Anda (contoh: Pemilik Toko, Wiraswasta, Karyawan, dll)</p>
                </div>

                <div class="mb-4">
                    <h6 class="small mb-2"><strong>Status Reklame</strong></h6>
                    <p class="small text-muted">Pilih "Baru" jika ini adalah reklame pertama kali, atau "Perpanjangan" jika memperbarui reklame yang sudah ada</p>
                </div>

                <div class="mb-4">
                    <h6 class="small mb-2"><strong>Nama Reklame</strong></h6>
                    <p class="small text-muted">Berikan nama yang jelas dan mudah dikenali untuk reklame Anda</p>
                </div>

                <div class="mb-4">
                    <h6 class="small mb-2"><strong>Jumlah Warna</strong></h6>
                    <p class="small text-muted">Masukkan berapa banyak warna yang digunakan dalam reklame</p>
                </div>

                <div class="mb-4">
                    <h6 class="small mb-2"><strong>Masa Berlaku</strong></h6>
                    <p class="small text-muted">Tentukan tanggal berakhirnya izin reklame ini</p>
                </div>

                <hr>

                <div class="alert alert-info mb-0">
                    <i class="bi bi-lightbulb"></i> 
                    <strong class="small d-block mb-1">Catatan:</strong>
                    <small>Pastikan semua data terisi dengan benar sebelum melanjutkan ke step berikutnya.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
