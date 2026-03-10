@extends('layouts.app')

@section('title', 'Form Surat Pernyataan')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-file-earmark-text"></i> Form Pendaftaran Step 3 - Surat Pernyataan</h1>
    <p class="text-muted">Isi dan tandatangani surat pernyataan reklame - Nomor Registrasi: <strong>{{ $permohonan->nomor_registrasi }}</strong></p>
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
            <div class="badge bg-success rounded-circle p-2 mb-2" style="font-size: 0.9rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="bi bi-check"></i>
            </div>
            <p class="small fw-bold">Step 2: Detail Reklame</p>
        </div>
        <div class="text-center flex-grow-1">
            <div class="badge bg-primary rounded-circle p-2 mb-2" style="font-size: 0.9rem; width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                3
            </div>
            <p class="small fw-bold">Step 3: Surat Pernyataan</p>
        </div>
    </div>
    <div class="progress" style="height: 3px;">
        <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div>

<div class="row g-5">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-5">
                <form method="POST" action="{{ route('surat-pernyataan.store', $permohonan) }}" enctype="multipart/form-data" id="suratPernyataanForm">
                    @csrf

                    <!-- Header -->
                    <div class="text-center mb-5">
                        <h5 style="color: #1a5490; font-weight: bold;">SURAT PERNYATAAN</h5>
                        <p class="text-muted mb-3">Permohonan Izin Reklame No. {{ $permohonan->nomor_registrasi }}</p>
                    </div>

                    <!-- Data Pemohon -->
                    <h6 class="mb-4 pb-2 border-bottom" style="color: #1a5490;"><i class="bi bi-person"></i> Data Pemohon</h6>

                    <div class="mb-4">
                        <label for="nama_pemohon" class="form-label fw-600">Nama <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" 
                            id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon', $suratPernyataan->nama_pemohon ?? '') }}" required>
                        @error('nama_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="pekerjaan" class="form-label fw-600">Pekerjaan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" 
                            id="pekerjaan" name="pekerjaan" value="{{ old('pekerjaan', $suratPernyataan->pekerjaan ?? '') }}" placeholder="Cth: Pengusaha, Karyawan, dll" required>
                        @error('pekerjaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="alamat_pemohon" class="form-label fw-600">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" 
                            id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon', $suratPernyataan->alamat_pemohon ?? '') }}</textarea>
                        @error('alamat_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="no_ktp" class="form-label fw-600">No KTP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" 
                            id="no_ktp" name="no_ktp" value="{{ old('no_ktp', $suratPernyataan->no_ktp ?? '') }}" placeholder="16 digit" required>
                        @error('no_ktp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Syarat dan Ketentuan -->
                    <h6 class="mb-4 pb-2 border-bottom" style="color: #1a5490;"><i class="bi bi-list-check"></i> Sehubungan dengan permohonan izin reklame, saya berjanji:</h6>

                    <div class="mb-4 p-4" style="background-color: #f8f9fa; border-left: 4px solid #1a5490; border-radius: 4px;">
                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_1') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_1" name="setuju_syarat_1" value="1" 
                                {{ old('setuju_syarat_1', $suratPernyataan->setuju_syarat_1 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_1">
                                <strong>1.</strong> Bahwa saya sanggup menaati segala peraturan / ketentuan – ketentuan yang di terapkan oleh Pemerintah Kabupaten Bangkalan
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_2') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_2" name="setuju_syarat_2" value="1" 
                                {{ old('setuju_syarat_2', $suratPernyataan->setuju_syarat_2 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_2">
                                <strong>2.</strong> Bahwa sesungguhnya dengan izin penyelenggaraan / pemasangan reklame tersebut, akan saya pergunakan sendiri dan semata-mata untuk kepentingan reklame sesuai dengan ketentuan yang berlaku
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_3') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_3" name="setuju_syarat_3" value="1" 
                                {{ old('setuju_syarat_3', $suratPernyataan->setuju_syarat_3 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_3">
                                <strong>3.</strong> Bahwa saya tidak akan mengubah konstruksi dan atau memindah tempat / lokasi yang ditentukan tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_4') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_4" name="setuju_syarat_4" value="1" 
                                {{ old('setuju_syarat_4', $suratPernyataan->setuju_syarat_4 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_4">
                                <strong>4.</strong> Bahwa saya tidak akan memindah tangankan surat izin pemasangan reklame kepada pihak lain tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_5') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_5" name="setuju_syarat_5" value="1" 
                                {{ old('setuju_syarat_5', $suratPernyataan->setuju_syarat_5 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_5">
                                <strong>5.</strong> Bahwa saya bertanggung jawab sepenuhnya atas konstruksi reklame, kebersihan, ketertiban dan keindahan reklame, serta pemeliharaan reklame yang di pasang
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_6') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_6" name="setuju_syarat_6" value="1" 
                                {{ old('setuju_syarat_6', $suratPernyataan->setuju_syarat_6 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_6">
                                <strong>6.</strong> Bahwa saya bertanggung jawab sepenuhnya atas barang pihak lain dan kecelakaan terhadap orang lain yang di akibatkan oleh reklame
                            </label>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input @error('setuju_syarat_7') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_7" name="setuju_syarat_7" value="1" 
                                {{ old('setuju_syarat_7', $suratPernyataan->setuju_syarat_7 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_7">
                                <strong>7.</strong> Bahwa saya penyelenggara reklame harus membongkar dan menurunkan reklame selambat-lambatnya 7 (tujuh) hari setelah masa berlakunya berakhir dan tidak diperpanjang
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input @error('setuju_syarat_8') is-invalid @enderror" type="checkbox" 
                                id="setuju_syarat_8" name="setuju_syarat_8" value="1" 
                                {{ old('setuju_syarat_8', $suratPernyataan->setuju_syarat_8 ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="setuju_syarat_8">
                                <strong>8.</strong> Bahwa apabila saya kemudian hari ternyata tidak mematuhi janji tersebut di atas baik seluruhnya maupun sebagian dalam pernyataan ini, maka saya bersedia reklame tersebut di bongkar atau dikenakan sanksi lain sesuai dengan ketentuan yang berlaku
                            </label>
                        </div>

                        @if ($errors->has('setuju_syarat_1') || $errors->has('setuju_syarat_2') || $errors->has('setuju_syarat_3') || 
                             $errors->has('setuju_syarat_4') || $errors->has('setuju_syarat_5') || $errors->has('setuju_syarat_6') || 
                             $errors->has('setuju_syarat_7') || $errors->has('setuju_syarat_8'))
                            <div class="alert alert-danger mt-3 mb-0">
                                Anda harus menyetujui semua syarat dan ketentuan
                            </div>
                        @endif
                    </div>

                    <!-- Dokumen Pendukung -->
                    <h6 class="mb-4 pb-2 border-bottom" style="color: #1a5490;"><i class="bi bi-file-upload"></i> Dokumen Pendukung</h6>

                    <div class="mb-4">
                        <label for="tanggal_pernyataan" class="form-label fw-600">Tanggal Pernyataan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_pernyataan') is-invalid @enderror" 
                            id="tanggal_pernyataan" name="tanggal_pernyataan" 
                            value="{{ old('tanggal_pernyataan', $suratPernyataan->tanggal_pernyataan ?? '') }}" required>
                        @error('tanggal_pernyataan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="file_tanda_tangan" class="form-label fw-600">
                            <i class="bi bi-pen"></i> Upload Bukti Tanda Tangan (Opsional)
                        </label>
                        <p class="small text-muted mb-2">Format: PDF, JPG, PNG | Ukuran maks: 5MB</p>
                        <input type="file" class="form-control @error('file_tanda_tangan') is-invalid @enderror" 
                            id="file_tanda_tangan" name="file_tanda_tangan" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_tanda_tangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($suratPernyataan->file_tanda_tangan)
                            <div class="mt-2">
                                <a href="{{ Storage::url($suratPernyataan->file_tanda_tangan) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Lihat File
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <label for="file_materai" class="form-label fw-600">
                            <i class="bi bi-stamp"></i> Upload Bukti Materai (Opsional)
                        </label>
                        <p class="small text-muted mb-2">Nominal Rp. 10.000 | Format: PDF, JPG, PNG | Ukuran maks: 5MB</p>
                        <input type="file" class="form-control @error('file_materai') is-invalid @enderror" 
                            id="file_materai" name="file_materai" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_materai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($suratPernyataan->file_materai)
                            <div class="mt-2">
                                <a href="{{ Storage::url($suratPernyataan->file_materai) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-download"></i> Lihat File
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Alert Info -->
                    <div class="alert alert-info mt-5">
                        <h6 class="alert-heading mb-2">
                            <i class="bi bi-info-circle"></i> Penting
                        </h6>
                        <ul class="mb-0">
                            <li>Surat pernyataan ini harus ditandatangani dan dibubuhi materai Rp. 10.000</li>
                            <li>Pastikan semua data sesuai dengan dokumen identitas Anda</li>
                            <li>Setelah submit, form tidak dapat diubah sampai diverifikasi oleh operator</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-5">
                        <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary" style="background: #cbd5e1; color: #0f172a; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-check-circle"></i> Simpan & Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-info-circle-fill text-primary"></i> Informasi Permohonan
                </h6>
                <div class="mb-3">
                    <small class="text-muted d-block">Nomor Registrasi</small>
                    <strong>{{ $permohonan->nomor_registrasi }}</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $permohonan->status == 'approved' ? 'success' : 'warning' }}">
                        {{ ucfirst($permohonan->status) }}
                    </span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Jenis Reklame</small>
                    <strong>{{ ucfirst(str_replace('_', ' ', $permohonan->jenis_reklame)) }}</strong>
                </div>
                <div>
                    <small class="text-muted d-block">Lokasi</small>
                    <strong>{{ $permohonan->lokasi_pemasangan }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
