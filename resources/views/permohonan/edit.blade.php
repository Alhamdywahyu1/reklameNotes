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
                <i class="bi bi-file-earmark-check"></i> Upload Persyaratan Dokumen
            </h5>
        </div>
        <div class="card-body">
            @if(isset($requirements) && $requirements->isNotEmpty())
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
                                        if ($permohonan->status === 'Draft') {
                                            $displayStatus = 'Draft';
                                            $statusClass = 'badge bg-secondary';
                                        } elseif ($req->status === 'Lengkap') {
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

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Status unggahan:</small>
                                        @if($req->file_dokumen)
                                            <div class="d-flex flex-column flex-sm-row align-items-center gap-2">
                                                <span class="badge bg-success align-self-center">Sudah diupload</span>
                                                    <button type="submit" form="delete-requirement-{{ $req->id }}" class="btn btn-outline-danger d-inline-flex align-items-center justify-content-center align-self-center" title="Hapus dokumen" style="width: 2.5rem; height: 2.5rem;">
                                                        <i class="bi bi-trash fs-5"></i>
                                                    </button>
                                            </div>

                                            @php
                                                $fileExtension = strtolower(pathinfo($req->file_dokumen, PATHINFO_EXTENSION));
                                                $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $isPdf = $fileExtension === 'pdf';
                                            @endphp

                                            <div class="mt-3">
                                                <small class="text-muted d-block mb-2">Dokumen terupload:</small>
                                                @if($isImage)
                                                    <a href="{{ route('document-requirements.preview', $req) }}" target="_blank" rel="noopener" class="d-inline-block border rounded overflow-hidden bg-light" title="Buka preview dokumen">
                                                        <img src="{{ route('document-requirements.preview', $req) }}" alt="Dokumen terupload" class="d-block" style="max-width:min(240px,100%); max-height:180px; width:auto; height:auto; object-fit:contain;" loading="lazy">
                                                    </a>
                                                @elseif($isPdf)
                                                    <div class="border rounded overflow-hidden bg-white" style="height:180px; max-width:min(320px,100%);">
                                                        <embed src="{{ route('document-requirements.preview', $req) }}" type="application/pdf" width="100%" height="100%" />
                                                    </div>
                                                @else
                                                    <a href="{{ route('document-requirements.preview', $req) }}" class="btn btn-sm btn-outline-info" target="_blank" rel="noopener">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark">Belum diupload</span>
                                        @endif
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
                                    </div>

                                    <input type="hidden" name="documents[{{ $index }}][id]" value="{{ $req->id }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex flex-column flex-sm-row gap-2 justify-content-start">
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('permohonan.show', $permohonan) }}" class="btn btn-secondary" style="background: #cbd5e1; color: #0f172a; border: none; font-weight: 600; padding: 0.75rem 2rem;">
                        <i class="bi bi-x-circle"></i> Batal
                    </a>
                </div>
            @else
                <p class="text-muted mb-0">Tidak ada persyaratan dokumen.</p>
            @endif
        </div>
    </div>

    </form>

    @foreach($requirements as $req)
        @if($req->file_dokumen)
            <form id="delete-requirement-{{ $req->id }}" action="{{ route('document-requirements.destroy', $req) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endif
    @endforeach

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
