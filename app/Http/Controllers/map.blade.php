@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Peta Pengawasan Reklame (Satpol PP)</h4>
        </div>
    </div>

    <!-- Panel Kontrol Peta -->
    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap gap-3 align-items-center">
            <button id="btn-location" class="btn btn-primary">
                <i class="bi bi-geo-alt-fill"></i> Lokasi Saya
            </button>
            
            <div class="form-check form-switch ms-md-4">
                <input class="form-check-input" type="checkbox" id="filter-expired" style="transform: scale(1.3);">
                <label class="form-check-label ms-2 font-weight-bold text-danger" for="filter-expired">
                    Hanya Tampilkan Reklame Kedaluwarsa
                </label>
            </div>
            
            <div class="ms-auto text-muted small">
                <span class="badge bg-success me-1">&nbsp;</span> Aktif &nbsp; | &nbsp; 
                <span class="badge bg-danger me-1">&nbsp;</span> Kedaluwarsa
            </div>
        </div>
    </div>

    <!-- Container Peta -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div id="map" style="height: 70vh; width: 100%; border-radius: 8px; z-index: 1;"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-popup-content { margin: 15px; width: 250px !important; }
    .reklame-img { width: 100%; height: 140px; object-fit: cover; border-radius: 6px; margin-top: 10px; border: 1px solid #ddd; }
    .detail-table { width: 100%; font-size: 0.85rem; margin-top: 10px; }
    .detail-table td { padding: 3px 0; vertical-align: top; }
    .detail-table td:first-child { font-weight: 600; width: 40%; color: #555; }
</style>
@endpush

@push('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Peta (Koordinat default diset ke area Bangkalan)
    const map = L.map('map').setView([-7.0308, 112.7441], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // 2. Setup Icon Marker Custom (Hijau = Aktif, Merah = Kedaluwarsa)
    const greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });
    
    const redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    const reklameData = @json($reklameList);
    let markers = [];

    // 3. Render Marker ke Peta
    reklameData.forEach(item => {
        const marker = L.marker([item.latitude, item.longitude], {
            icon: item.is_expired ? redIcon : greenIcon
        });

        const popupHTML = `
            <div>
                <h6 class="mb-1 fw-bold text-primary">${item.nomor_registrasi}</h6>
                <div class="mb-2">
                    <span class="badge ${item.is_expired ? 'bg-danger' : 'bg-success'}">${item.status_text}</span>
                </div>
                <table class="detail-table">
                    <tr><td>Pemilik</td><td>: ${item.nama_pemilik}</td></tr>
                    <tr><td>Jenis</td><td>: ${item.jenis_reklame}</td></tr>
                    <tr><td>Ukuran</td><td>: ${item.ukuran}</td></tr>
                    <tr><td>Masa Berlaku</td><td class="${item.is_expired ? 'text-danger fw-bold' : ''}">: ${item.masa_berlaku}</td></tr>
                </table>
                ${item.foto_desain ? 
                    `<a href="${item.foto_desain}" target="_blank"><img src="${item.foto_desain}" class="reklame-img" alt="Desain Reklame"></a>` : 
                    `<div class="text-center mt-3 p-3 bg-light text-muted border rounded small">Tidak ada foto</div>`
                }
            </div>
        `;

        marker.bindPopup(popupHTML);
        marker.is_expired = item.is_expired; // Simpan properti untuk filter
        markers.push(marker);
        marker.addTo(map);
    });

    // 4. Fitur Filter "Hanya Kedaluwarsa"
    document.getElementById('filter-expired').addEventListener('change', function(e) {
        const showOnlyExpired = e.target.checked;
        markers.forEach(marker => {
            if (showOnlyExpired && !marker.is_expired) {
                map.removeLayer(marker);
            } else {
                if (!map.hasLayer(marker)) marker.addTo(map);
            }
        });
    });

    // 5. Fitur "Lokasi Saya" (Geolocation)
    const btnLocation = document.getElementById('btn-location');
    let userLocationMarker = null;
    let userLocationCircle = null;

    btnLocation.addEventListener('click', function() {
        btnLocation.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Mencari...';
        btnLocation.disabled = true;
        map.locate({setView: true, maxZoom: 16});
    });

    map.on('locationfound', function(e) {
        btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
        btnLocation.disabled = false;
        
        if (userLocationMarker) map.removeLayer(userLocationMarker);
        if (userLocationCircle) map.removeLayer(userLocationCircle);

        userLocationMarker = L.marker(e.latlng).addTo(map).bindPopup("Lokasi Anda saat ini.").openPopup();
        userLocationCircle = L.circle(e.latlng, e.accuracy / 2).addTo(map);
    });

    map.on('locationerror', function(e) {
        alert("Gagal mendeteksi lokasi. Pastikan GPS/Location aktif di perangkat/browser Anda.");
        btnLocation.innerHTML = '<i class="bi bi-geo-alt-fill"></i> Lokasi Saya';
        btnLocation.disabled = false;
    });
});
</script>
@endpush