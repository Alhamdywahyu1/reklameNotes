@extends('layouts.app')

@section('title', 'Edit Permohonan Reklame')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 fw-bold">Edit Permohonan Reklame</h2>
            <p class="text-muted">Nomor Registrasi: <strong>{{ $permohonan->nomor_registrasi }}</strong></p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi Kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('permohonan.update', $permohonan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-fill"></i> Data Pemohon
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_pemohon" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pemohon') is-invalid @enderror" 
                               id="nama_pemohon" name="nama_pemohon" 
                               value="{{ old('nama_pemohon', $permohonan->nama_pemohon) }}" required>
                        @error('nama_pemohon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nik') is-invalid @enderror" 
                               id="nik" name="nik" maxlength="16"
                               value="{{ old('nik', $permohonan->nik) }}" readonly>
                        @error('nik')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nomor_telepon" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('nomor_telepon') is-invalid @enderror" 
                               id="nomor_telepon" name="nomor_telepon" 
                               value="{{ old('nomor_telepon', $permohonan->nomor_telepon) }}" required>
                        @error('nomor_telepon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="npwp" class="form-label">NPWP</label>
                        <input type="text" class="form-control @error('npwp') is-invalid @enderror" 
                               id="npwp" name="npwp" maxlength="15"
                               value="{{ old('npwp', $permohonan->npwp) }}">
                        @error('npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat_pemohon" class="form-label">Alamat <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('alamat_pemohon') is-invalid @enderror" 
                              id="alamat_pemohon" name="alamat_pemohon" rows="3" required>{{ old('alamat_pemohon', $permohonan->alamat_pemohon) }}</textarea>
                    @error('alamat_pemohon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-billboard"></i> Data Reklame
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_reklame" class="form-label">Jenis Reklame <span class="text-danger">*</span></label>
                        <select class="form-select @error('jenis_reklame') is-invalid @enderror" 
                                id="jenis_reklame" name="jenis_reklame" required>
                            <option value="">Pilih Jenis Reklame</option>
                            <option value="Permanen" @selected(old('jenis_reklame', $permohonan->jenis_reklame) === 'Permanen')>Permanen</option>
                            <option value="Non Permanen" @selected(old('jenis_reklame', $permohonan->jenis_reklame) === 'Non Permanen')>Non Permanen</option>
                        </select>
                        @error('jenis_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="ukuran_reklame" class="form-label">Ukuran Reklame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ukuran_reklame') is-invalid @enderror" 
                               id="ukuran_reklame" name="ukuran_reklame" placeholder="Contoh: 3m x 5m"
                               value="{{ old('ukuran_reklame', $permohonan->ukuran_reklame) }}" required>
                        @error('ukuran_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jumlah_reklame" class="form-label">Jumlah Reklame <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('jumlah_reklame') is-invalid @enderror" 
                               id="jumlah_reklame" name="jumlah_reklame" 
                               value="{{ old('jumlah_reklame', $permohonan->jumlah_reklame) }}" min="1" required>
                        @error('jumlah_reklame')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                <div class="mb-3">
                    <label for="lokasi_pemasangan" class="form-label">Lokasi Pemasangan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('lokasi_pemasangan') is-invalid @enderror" 
                              id="lokasi_pemasangan" name="lokasi_pemasangan" rows="3" required>{{ old('lokasi_pemasangan', $permohonan->lokasi_pemasangan) }}</textarea>
                    @error('lokasi_pemasangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="keperluan_reklame" class="form-label">Reklame untuk Keperluan</label>
                    <select class="form-select @error('keperluan_reklame') is-invalid @enderror" 
                        id="keperluan_reklame" name="keperluan_reklame">
                        <option value="">-- Pilih --</option>
                        <option value="Komersial" @selected(old('keperluan_reklame', $permohonan->keperluan_reklame) === 'Komersial')>Komersial</option>
                        <option value="Non Komersial" @selected(old('keperluan_reklame', $permohonan->keperluan_reklame) === 'Non Komersial')>Non Komersial</option>
                        <option value="Sosial" @selected(old('keperluan_reklame', $permohonan->keperluan_reklame) === 'Sosial')>Sosial</option>
                    </select>
                    @error('keperluan_reklame')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="narasi_reklame" class="form-label">Narasi/Isi Reklame <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('narasi_reklame') is-invalid @enderror" 
                              id="narasi_reklame" name="narasi_reklame" rows="3" required>{{ old('narasi_reklame', $permohonan->narasi_reklame) }}</textarea>
                    @error('narasi_reklame')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-geo-alt"></i> Koordinat Lokasi
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="searchAddress" class="form-label">Cari Alamat <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchAddress" placeholder="Ketik alamat lengkap (contoh: Jl. Sudirman No. 10, Jakarta Pusat)" value="{{ old('lokasi_pemasangan', $permohonan->lokasi_pemasangan) }}">
                        <button class="btn btn-primary" type="button" id="searchBtn" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600;"><i class="bi bi-search"></i> Cari</button>
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
                            value="{{ old('latitude', $permohonan->latitude) }}" required>
                        @error('latitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('longitude') is-invalid @enderror" 
                            id="longitude" name="longitude" step="0.0000001" placeholder="106.816666" 
                            value="{{ old('longitude', $permohonan->longitude) }}" required>
                        @error('longitude')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <small class="text-muted d-block mb-3">
                    <i class="bi bi-lightbulb"></i> Klik pada peta untuk menandai lokasi reklame, atau masukkan koordinat secara manual.
                </small>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-pdf"></i> Dokumen
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="file_ktp" class="form-label">Scan KTP (PDF, JPG, PNG) - Max 5MB</label>
                        @if ($permohonan->file_ktp)
                            <div class="alert alert-info alert-sm mb-2">
                                <small>File saat ini: <a href="{{ Storage::url($permohonan->file_ktp) }}" target="_blank">{{ basename($permohonan->file_ktp) }}</a></small>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('file_ktp') is-invalid @enderror" 
                               id="file_ktp" name="file_ktp" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                        @error('file_ktp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="file_npwp" class="form-label">Scan NPWP (PDF, JPG, PNG) - Max 5MB</label>
                        @if ($permohonan->file_npwp)
                            <div class="alert alert-info alert-sm mb-2">
                                <small>File saat ini: <a href="{{ Storage::url($permohonan->file_npwp) }}" target="_blank">{{ basename($permohonan->file_npwp) }}</a></small>
                            </div>
                        @endif
                        <input type="file" class="form-control @error('file_npwp') is-invalid @enderror" 
                               id="file_npwp" name="file_npwp" accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                        @error('file_npwp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="file_desain" class="form-label">Desain Reklame (PDF, JPG, PNG) - Max 5MB</label>
                    @if ($permohonan->file_desain)
                        <div class="alert alert-info alert-sm mb-2">
                            <small>File saat ini: <a href="{{ Storage::url($permohonan->file_desain) }}" target="_blank">{{ basename($permohonan->file_desain) }}</a></small>
                        </div>
                    @endif
                    <input type="file" class="form-control @error('file_desain') is-invalid @enderror" 
                           id="file_desain" name="file_desain" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                    @error('file_desain')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mb-4">
            <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
            </button>
            <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary" style="background: #cbd5e1; color: #0f172a; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                <i class="bi bi-x-circle"></i> Batal
            </a>
        </div>
    </form>

    <div class="card border-warning bg-light mb-4">
        <div class="card-body">
            <h6 class="card-title text-warning">
                <i class="bi bi-info-circle"></i> Informasi
            </h6>
            <small>
                <ul class="mb-0">
                    <li>Anda hanya dapat mengubah data permohonan selama status masih <strong>Draft</strong></li>
                    <li>Setelah permohonan diajukan, Anda tidak dapat mengubah data</li>
                    <li>Jika permohonan ditolak, Anda dapat mengubah kembali dan mengajukan ulang</li>
                    <li>Upload file dengan format PDF, JPG, atau PNG dengan ukuran maksimal 5MB</li>
                </ul>
            </small>
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
    const map = L.map('mapContainer').setView([{{ $permohonan->latitude ?? '-6.200000' }}, {{ $permohonan->longitude ?? '106.816666' }}], 13);
    
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
