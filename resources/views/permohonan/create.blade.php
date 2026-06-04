@extends('layouts.app')

@section('title', 'Buat Permohonan Reklame')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-plus-circle"></i> Buat Permohonan Reklame Baru</h1>
    <p class="text-muted">Isi formulir di bawah untuk mengajukan permohonan reklame</p>
</div>

<div class="row g-5">
    @if(env('OTP_VERIFICATION_ENABLED', true) && !auth()->user()->hasVerifiedEmail())
    <div class="col-12">
        <div class="alert alert-warning alert-dismissible fade show border-start border-warning border-4" role="alert">
            <div class="d-flex gap-3">
                <div>
                    <i class="bi bi-exclamation-triangle-fill fs-5" style="color: #ffc107;"></i>
                </div>
                <div>
                    <h6 class="alert-heading mb-2">⚠️ Email Belum Terverifikasi</h6>
                    <p class="mb-0">Anda harus memverifikasi email terlebih dahulu sebelum dapat mengajukan permohonan reklame. Klik <a href="{{ route('otp.show') }}" class="fw-bold">di sini untuk verifikasi email</a> atau gunakan menu "Akun → Verifikasi Email" di navbar.</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal untuk notifikasi verifikasi diperlukan -->
    @if(env('OTP_VERIFICATION_ENABLED', true) && !auth()->user()->hasVerifiedEmail())
    <div class="modal fade" id="verificationRequiredModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title"><i class="bi bi-shield-exclamation"></i> Verifikasi Email Diperlukan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Anda harus memverifikasi email Anda terlebih dahulu sebelum dapat mengajukan permohonan reklame.</p>
                    <p class="mb-0 text-muted"><i class="bi bi-info-circle"></i> Email akan diverifikasi melalui kode OTP yang kami kirimkan ke alamat email Anda.</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('otp.show') }}" class="btn btn-warning"><i class="bi bi-shield-check"></i> Verifikasi Sekarang</a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body p-5">
                <form method="POST" action="{{ route('permohonan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-person"></i> Data Pemohon</h5>
                    
                    <div class="mb-4">
                        <label for="nama_pemohon" class="form-label fw-600">Nama Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" 
                            id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon') }}" required>
                        @error('nama_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="alamat_pemohon" class="form-label fw-600">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" 
                            id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon') }}</textarea>
                        @error('alamat_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="nomor_telepon" class="form-label fw-600">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror" 
                                id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required>
                            @error('nomor_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="nik" class="form-label fw-600">NIK (16 digit) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                id="nik" name="nik" value="{{ old('nik') }}" placeholder="1234567890123456" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="npwp" class="form-label fw-600">NPWP (Opsional)</label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" 
                            id="npwp" name="npwp" value="{{ old('npwp') }}">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-5">
                    
                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-signpost-split"></i> Data Reklame</h5>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="jenis_reklame" class="form-label fw-600">Jenis Reklame <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenis_reklame') is-invalid @enderror" 
                                id="jenis_reklame" name="jenis_reklame" required>
                                <option value="">-- Pilih --</option>
                                <option value="Permanen" @if(old('jenis_reklame') === 'Permanen') selected @endif>Permanen</option>
                                <option value="Non Permanen" @if(old('jenis_reklame') === 'Non Permanen') selected @endif>Non Permanen</option>
                            </select>
                            @error('jenis_reklame')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="jumlah_reklame" class="form-label fw-600">Jumlah Reklame <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_reklame') is-invalid @enderror" 
                                id="jumlah_reklame" name="jumlah_reklame" value="{{ old('jumlah_reklame', 1) }}" min="1" required>
                            @error('jumlah_reklame')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="ukuran_reklame" class="form-label fw-600">Ukuran Reklame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ukuran_reklame') is-invalid @enderror" 
                            id="ukuran_reklame" name="ukuran_reklame" placeholder="Contoh: 3m x 5m" 
                            value="{{ old('ukuran_reklame') }}" required>
                        @error('ukuran_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="narasi_reklame" class="form-label fw-600">Narasi/Deskripsi Reklame <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('narasi_reklame') is-invalid @enderror" 
                            id="narasi_reklame" name="narasi_reklame" rows="3" required>{{ old('narasi_reklame') }}</textarea>
                        @error('narasi_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="lokasi_pemasangan" class="form-label fw-600">Lokasi Pemasangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('lokasi_pemasangan') is-invalid @enderror" 
                            id="lokasi_pemasangan" name="lokasi_pemasangan" rows="3" required>{{ old('lokasi_pemasangan') }}</textarea>
                        @error('lokasi_pemasangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="keperluan_reklame" class="form-label fw-600">Reklame untuk Keperluan</label>
                        <select class="form-select @error('keperluan_reklame') is-invalid @enderror" 
                            id="keperluan_reklame" name="keperluan_reklame">
                            <option value="">-- Pilih --</option>
                            <option value="Komersial" @if(old('keperluan_reklame') === 'Komersial') selected @endif>Komersial</option>
                            <option value="Non Komersial" @if(old('keperluan_reklame') === 'Non Komersial') selected @endif>Non Komersial</option>
                            <option value="Sosial" @if(old('keperluan_reklame') === 'Sosial') selected @endif>Sosial</option>
                        </select>
                        @error('keperluan_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-5">

                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-geo-alt"></i> Koordinat Lokasi</h5>

                    <div class="mb-4">
                        <label for="searchAddress" class="form-label fw-600">Cari Alamat <span class="text-danger">*</span></label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="searchAddress" placeholder="Ketik alamat lengkap (contoh: Jl. Sudirman No. 10, Jakarta Pusat)" value="{{ old('lokasi_pemasangan') }}">
                            <button class="btn btn-primary" type="button" id="searchBtn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600;"><i class="bi bi-search"></i> Cari</button>
                        </div>
                        <small class="text-muted d-block">
                            <i class="bi bi-lightbulb"></i> Tips: Ketikkan alamat lengkap dengan nama jalan, nomor, dan kota untuk hasil yang lebih akurat. Atau klik langsung pada peta untuk menandai lokasi.
                        </small>
                    </div>

                    <div id="mapContainer" style="height: 400px; border-radius: 4px; margin-bottom: 2rem; border: 2px solid #dee2e6; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);"></div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                id="latitude" name="latitude" step="any" placeholder="-6.200000" 
                                value="{{ old('latitude') }}" required>
                            <small class="text-muted d-block mt-1">Format: -6.123456 (negativ untuk Selatan)</small>
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                id="longitude" name="longitude" step="any" placeholder="106.816666" 
                                value="{{ old('longitude') }}" required>
                            <small class="text-muted d-block mt-1">Format: 106.123456 (positif untuk Timur)</small>
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <small class="text-muted d-block mb-4 p-2 bg-light border-start border-primary ps-3">
                        <i class="bi bi-info-circle"></i> <strong>Cara menggunakan:</strong> Cari alamat atau klik langsung pada peta untuk menandai lokasi reklame. Koordinat akan terupdate otomatis.
                    </small>

                    <hr class="my-5">

                    <h5 class="mb-4 pb-3 border-bottom" style="color: #1a5490;"><i class="bi bi-file-earmark-check"></i> Persyaratan Dokumen</h5>

                    <!-- Download Form Reklame PDF -->
                    <div class="alert alert-success mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <i class="bi bi-file-earmark-pdf fs-5 me-2"></i>
                            <strong>Download Formulir Reklame</strong>
                            <small class="d-block text-muted">Unduh formulir yang diperlukan sebelum mengisi permohonan</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <a href="{{ asset('fom reklame-halaman-halaman-1.pdf') }}" class="btn btn-sm btn-success" download>
                                <i class="bi bi-download me-1"></i> Data Isian Pemohon
                            </a>
                            <a href="{{ asset('fom reklame-halaman-halaman-3.pdf') }}" class="btn btn-sm btn-success" download>
                                <i class="bi bi-download me-1"></i> Surat Pernyataan
                            </a>
                        </div>
                    </div>

                    <p class="small text-muted mb-4">
                        <i class="bi bi-info-circle"></i> Upload dokumen pendukung di bawah ini. 
                        Dokumen yang ditandai <span class="badge bg-danger" style="font-size: 0.7rem;">Wajib</span> 
                        harus diupload, sedangkan yang ditandai 
                        <span class="badge bg-secondary" style="font-size: 0.7rem;">Opsional</span> dapat dilewati.
                    </p>

                    @php
                        $requirementsList = [
                            ['jenis' => 'Fotocopy KTP berwarna', 'optional' => false],
                            ['jenis' => 'Fotocopy NPWP berwarna', 'optional' => true],
                            ['jenis' => 'Fotocopy Akta Pendirian', 'optional' => true],
                            ['jenis' => 'Fotocopy Retribusi Pajak Reklame', 'optional' => false],
                            ['jenis' => 'Data Isian Pemohon', 'optional' => false],
                            ['jenis' => 'Surat Pernyataan Pertanggungjawaban Konstruksi', 'optional' => false],
                            ['jenis' => 'Foto kondisi & visualisasi reklame', 'optional' => false],
                            ['jenis' => 'Gambar konstruksi bidang', 'optional' => false],
                            ['jenis' => 'Surat Kuasa', 'optional' => true],
                        ];
                    @endphp

                    <div class="row">
                        @foreach($requirementsList as $index => $req)
                        <div class="col-md-6 mb-3">
                            <div class="card border">
                                <div class="card-body pb-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-subtitle mb-0">
                                            <i class="bi bi-file-earmark"></i> {{ $req['jenis'] }}
                                        </h6>
                                        @if($req['optional'])
                                            <span class="badge bg-secondary">Opsional</span>
                                        @else
                                            <span class="badge bg-danger">Wajib</span>
                                        @endif
                                    </div>
                                    <input type="file" 
                                        class="form-control form-control-sm @error("requirement_files.$index") is-invalid @enderror" 
                                        name="requirement_files[{{ $index }}]"
                                        accept=".pdf,.jpg,.jpeg,.png"
                                        data-requirement-index="{{ $index }}"
                                        @if(!$req['optional']) required @endif>
                                    <small class="text-muted d-block mt-1">PDF, JPG, PNG | Max 5MB</small>
                                    @error("requirement_files.$index")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="alert alert-info mb-4">
                        <i class="bi bi-lightbulb"></i> 
                        <strong>Catatan:</strong> Anda dapat upload dokumen sekarang atau melengkapinya nanti setelah permohonan dibuat.
                    </div>

                    <hr>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                        <a href="{{ route('permohonan.index') }}" class="btn btn-secondary" style="background: #cbd5e1; color: #0f172a; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 align-self-start">
        <div class="card bg-light sticky-top" style="top: 20px;">
            <div class="card-body p-4">
                <h5 class="card-title mb-3"><i class="bi bi-info-circle"></i> Informasi</h5>
                <p class="small text-muted mb-4">Pastikan semua data yang Anda isi sudah benar sebelum mengajukan permohonan.</p>
                
                <h6 class="small mb-2"><strong>Dokumen yang diperlukan:</strong></h6>
                <ul class="small text-muted ps-3 mb-4">
                    <li>Fotocopy KTP berwarna</li>
                    <li>Fotocopy NPWP (jika ada)</li>
                    <li>Desain reklame</li>
                </ul>

                <p class="small text-muted mt-4 pt-3 border-top">Permohonan dalam status <strong>Draft</strong> dapat diubah atau dihapus kapan saja. Setelah diajukan, permohonan tidak dapat diubah.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #mapContainer {
        position: relative;
        z-index: 1;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .card {
        border-radius: 8px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card.bg-light {
        background-color: #f8f9fa !important;
    }
    
    hr {
        border-top: 1px solid #dee2e6;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-label {
        margin-bottom: 0.6rem;
        font-size: 0.95rem;
    }
    
    .form-control,
    .form-select {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        font-size: 0.95rem;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #1a5490;
        box-shadow: 0 0 0 0.2rem rgba(26, 84, 144, 0.25);
    }
    
    @media (max-width: 991.98px) {
        .card.bg-light {
            margin-top: 2rem;
        }
        
        .sticky-top {
            position: static !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Check email verification on form submit (only when OTP verification enabled)
    @if(env('OTP_VERIFICATION_ENABLED', true) && !auth()->user()->hasVerifiedEmail())
    const permohonanForm = document.querySelector('form');
    if (permohonanForm) {
        permohonanForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show alert modal
            const alertModal = new bootstrap.Modal(document.getElementById('verificationRequiredModal'));
            alertModal.show();
            
            return false;
        });
    }
    @endif

    // Initialize map
    const map = L.map('mapContainer').setView([-6.200000, 106.816666], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    let marker = null;

    // Get stored coordinates from form
    const savedLat = document.getElementById('latitude').value;
    const savedLng = document.getElementById('longitude').value;

    if (savedLat && savedLng) {
        marker = L.marker([parseFloat(savedLat), parseFloat(savedLng)]).addTo(map);
        map.setView([parseFloat(savedLat), parseFloat(savedLng)], 15);
    }

    // Click on map to set marker
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng).addTo(map);
        }

        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    });

    // Update map when inputs change
    document.getElementById('latitude').addEventListener('change', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (lat && lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            map.setView([lat, lng], 15);
        }
    });

    document.getElementById('longitude').addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(this.value);
        
        if (lat && lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            map.setView([lat, lng], 15);
        }
    });

    // Function to show alert
    function showAlert(message, type = 'danger') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        const formCardBody = document.querySelector('.card-body');
        const firstHr = formCardBody.querySelector('hr');
        if (firstHr) {
            formCardBody.insertBefore(alertDiv, firstHr);
        } else {
            formCardBody.insertBefore(alertDiv, formCardBody.firstChild);
        }
        setTimeout(() => alertDiv.remove(), 6000);
    }

    // Geocoding dengan Nominatim API (improved error handling)
    document.getElementById('searchBtn').addEventListener('click', function() {
        const address = document.getElementById('searchAddress').value.trim();
        
        if (!address) {
            showAlert('Silakan masukkan alamat terlebih dahulu');
            return;
        }

        // Disable button dan tampilkan loading
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';

        // Call Nominatim API with better error handling
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1&countrycodes=id`, {
            method: 'GET',
            signal: controller.signal,
            headers: {
                'Accept': 'application/json',
                'User-Agent': 'ReklameApp/1.0'
            }
        })
        .then(response => {
            clearTimeout(timeoutId);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                // Validate coordinates for Indonesia region (approximately)
                if ((lat < -11 || lat > 6) || (lng < 95 || lng > 141)) {
                    showAlert('Lokasi yang ditemukan berada di luar wilayah Indonesia. Silakan coba alamat yang lebih spesifik.', 'warning');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    return;
                }

                // Update form inputs
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                document.getElementById('lokasi_pemasangan').value = result.display_name;

                // Update peta
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }
                map.setView([lat, lng], 15);

                // Tampilkan notifikasi sukses
                showAlert(`<strong>Lokasi ditemukan:</strong> ${result.display_name}`, 'success');
            } else {
                showAlert('Alamat tidak ditemukan. Silakan coba alamat yang lebih spesifik atau manual pin lokasi di peta.', 'warning');
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('Geocoding error:', error);
            
            if (error.name === 'AbortError') {
                showAlert('Pencarian alamat timeout. Silakan coba lagi atau pin lokasi secara manual di peta.', 'warning');
            } else {
                showAlert('Terjadi kesalahan saat mencari alamat. Silakan coba lagi atau pin lokasi secara manual di peta dengan mengklik pada peta.', 'warning');
            }
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Pencarian alamat saat enter key ditekan
    document.getElementById('searchAddress').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchBtn').click();
        }
    });
</script>
@endpush
