@extends('layouts.app')

@section('title', 'Buat Permohonan Reklame')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-plus-circle"></i> Buat Permohonan Reklame Baru</h1>
    <p class="text-muted">Isi formulir di bawah untuk mengajukan permohonan reklame</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('permohonan.store') }}" enctype="multipart/form-data">
                    @csrf

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-person"></i> Data Pemohon</h5>
                    
                    <div class="mb-3">
                        <label for="nama_pemohon" class="form-label">Nama Pemohon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" 
                            id="nama_pemohon" name="nama_pemohon" value="{{ old('nama_pemohon') }}" required>
                        @error('nama_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat_pemohon" class="form-label">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" 
                            id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon') }}</textarea>
                        @error('alamat_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror" 
                                id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon') }}" required>
                            @error('nomor_telepon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nik" class="form-label">NIK (16 digit) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                                id="nik" name="nik" value="{{ old('nik') }}" placeholder="1234567890123456" required>
                            @error('nik')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="npwp" class="form-label">NPWP (Opsional)</label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" 
                            id="npwp" name="npwp" value="{{ old('npwp') }}">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    
                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-signpost-split"></i> Data Reklame</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="jenis_reklame" class="form-label">Jenis Reklame <span class="text-danger">*</span></label>
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
                        <div class="col-md-6 mb-3">
                            <label for="jumlah_reklame" class="form-label">Jumlah Reklame <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('jumlah_reklame') is-invalid @enderror" 
                                id="jumlah_reklame" name="jumlah_reklame" value="{{ old('jumlah_reklame', 1) }}" min="1" required>
                            @error('jumlah_reklame')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="ukuran_reklame" class="form-label">Ukuran Reklame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ukuran_reklame') is-invalid @enderror" 
                            id="ukuran_reklame" name="ukuran_reklame" placeholder="Contoh: 3m x 5m" 
                            value="{{ old('ukuran_reklame') }}" required>
                        @error('ukuran_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="narasi_reklame" class="form-label">Narasi/Deskripsi Reklame <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('narasi_reklame') is-invalid @enderror" 
                            id="narasi_reklame" name="narasi_reklame" rows="3" required>{{ old('narasi_reklame') }}</textarea>
                        @error('narasi_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="lokasi_pemasangan" class="form-label">Lokasi Pemasangan <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('lokasi_pemasangan') is-invalid @enderror" 
                            id="lokasi_pemasangan" name="lokasi_pemasangan" rows="3" required>{{ old('lokasi_pemasangan') }}</textarea>
                        @error('lokasi_pemasangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-geo-alt"></i> Koordinat Lokasi</h5>

                    <div class="mb-3">
                        <label for="searchAddress" class="form-label">Cari Alamat <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchAddress" placeholder="Ketik alamat lengkap (contoh: Jl. Sudirman No. 10, Jakarta Pusat)" value="{{ old('lokasi_pemasangan') }}">
                            <button class="btn btn-primary" type="button" id="searchBtn"><i class="bi bi-search"></i> Cari</button>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-lightbulb"></i> Ketikkan alamat lengkap lokasi reklame, kemudian klik "Cari" untuk menemukan koordinatnya di peta.
                        </small>
                    </div>

                    <div id="mapContainer" style="height: 300px; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #ddd;"></div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('latitude') is-invalid @enderror" 
                                id="latitude" name="latitude" step="0.0000001" placeholder="-6.200000" 
                                value="{{ old('latitude') }}" required>
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                                id="longitude" name="longitude" step="0.0000001" placeholder="106.816666" 
                                value="{{ old('longitude') }}" required>
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <small class="text-muted d-block mb-3">
                        <i class="bi bi-lightbulb"></i> Klik pada peta untuk menandai lokasi reklame, atau masukkan koordinat secara manual.
                    </small>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-file-earmark"></i> Dokumen</h5>

                    <div class="mb-3">
                        <label for="file_ktp" class="form-label">Scan KTP (PDF/JPG/PNG, max 5MB)</label>
                        <input type="file" class="form-control @error('file_ktp') is-invalid @enderror" 
                            id="file_ktp" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_ktp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="file_npwp" class="form-label">Scan NPWP (PDF/JPG/PNG, max 5MB)</label>
                        <input type="file" class="form-control @error('file_npwp') is-invalid @enderror" 
                            id="file_npwp" name="file_npwp" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="file_desain" class="form-label">Desain Reklame (PDF/JPG/PNG, max 5MB)</label>
                        <input type="file" class="form-control @error('file_desain') is-invalid @enderror" 
                            id="file_desain" name="file_desain" accept=".pdf,.jpg,.jpeg,.png">
                        @error('file_desain')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <h5 class="mb-3" style="color: #1a5490;"><i class="bi bi-file-earmark-check"></i> Persyaratan Dokumen</h5>

                    <p class="small text-muted mb-4">
                        <i class="bi bi-info-circle"></i> Upload dokumen pendukung di bawah ini. 
                        Dokumen yang ditandai <span class="badge bg-danger" style="font-size: 0.7rem;">Wajib</span> 
                        harus diupload, sedangkan yang ditandai 
                        <span class="badge bg-secondary" style="font-size: 0.7rem;">Opsional</span> dapat dilewati.
                    </p>

                    @php
                        $requirementsList = [
                            ['jenis' => 'Fotocopy KTP berwarna', 'optional' => false],
                            ['jenis' => 'Fotocopy NPWP berwarna', 'optional' => false],
                            ['jenis' => 'Fotocopy Akta Pendirian', 'optional' => false],
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

                    
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Simpan
                        </button>
                        <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">
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
                <h5 class="card-title"><i class="bi bi-info-circle"></i> Informasi</h5>
                <p class="small text-muted mb-3">Pastikan semua data yang Anda isi sudah benar sebelum mengajukan permohonan.</p>
                
                <h6 class="small mb-2"><strong>Dokumen yang diperlukan:</strong></h6>
                <ul class="small text-muted">
                    <li>Fotocopy KTP berwarna</li>
                    <li>Fotocopy NPWP (jika ada)</li>
                    <li>Desain reklame</li>
                </ul>

                <p class="small text-muted mt-3">Permohonan dalam status <strong>Draft</strong> dapat diubah atau dihapus kapan saja. Setelah diajukan, permohonan tidak dapat diubah.</p>
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
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
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

    // Geocoding dengan Nominatim API
    document.getElementById('searchBtn').addEventListener('click', function() {
        const address = document.getElementById('searchAddress').value.trim();
        
        if (!address) {
            alert('Silakan masukkan alamat terlebih dahulu');
            return;
        }

        // Disable button dan tampilkan loading
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';

        // Call Nominatim API
        fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`, {
            headers: {
                'Accept': 'application/json',
                'User-Agent': 'ReklameApp'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

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

                // Tampilkan notifikasi
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle"></i> Lokasi ditemukan: <strong>${result.display_name}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('h5'));
                setTimeout(() => alertDiv.remove(), 5000);
            } else {
                alert('Alamat tidak ditemukan. Silakan coba alamat yang lebih spesifik atau manual pin lokasi di peta.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mencari alamat. Silakan coba lagi.');
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
