@extends('layouts.app')

@section('title', 'Peta Satpol PP')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-shield-check"></i> Peta Pengawasan Satpol PP</h1>
    <p class="text-muted">Pantau reklame aktif dan reklame yang masa berlakunya sudah habis untuk kebutuhan sidak lapangan</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="number fs-2 fw-bold">{{ $reklameList->count() }}</div>
                <div class="text-muted">Total Titik Reklame</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="number fs-2 fw-bold text-success">{{ $reklameList->where('is_terbit', true)->count() }}</div>
                <div class="text-muted">Terbit</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="number fs-2 fw-bold text-success">{{ $reklameList->where('is_expired', false)->where('is_terbit', false)->count() }}</div>
                <div class="text-muted">Masih Aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="number fs-2 fw-bold text-danger">{{ $reklameList->where('is_expired', true)->count() }}</div>
                <div class="text-muted">Kedaluwarsa</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body p-0">
        <div id="satpolMap" style="height: 600px; border-radius: 4px;"></div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar Titik Pengawasan</h5>
    </div>
    <div class="card-body">
        @if($reklameList->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No. Registrasi</th>
                            <th>Pemilik/Pemohon</th>
                            <th>Jenis</th>
                            <th>Alamat/Lokasi</th>
                            <th>Masa Berlaku</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reklameList as $item)
                            <tr>
                                <td><strong>{{ $item['nomor_registrasi'] }}</strong></td>
                                <td>{{ $item['nama_pemilik'] }}</td>
                                <td>{{ $item['jenis_reklame'] }}</td>
                                <td>{{ $item['lokasi_pemasangan'] }}</td>
                                <td>{{ $item['masa_berlaku'] }}</td>
                                <td>
                                    <span class="badge {{ $item['is_expired'] ? 'bg-danger' : 'bg-success' }}">
                                        {{ $item['status_text'] }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ $item['detail_url'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                    <a href="{{ $item['google_maps_url'] }}" class="btn btn-sm btn-success" target="_blank" rel="noopener">
                                        <i class="bi bi-geo-alt"></i> Google Maps
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> Belum ada titik reklame yang bisa dipantau.
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #satpolMap {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const reklameList = @json($reklameList);
    const map = L.map('satpolMap').setView([-6.200000, 106.816666], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function buildFotoBlock(item) {
        if (item.foto_preview_url && item.foto_kind === 'image') {
            return `
                <div class="mb-2 border rounded overflow-hidden bg-light">
                    <img src="${item.foto_preview_url}" alt="Foto reklame" loading="lazy"
                        style="width:100%; max-height:240px; height:auto; object-fit:contain; display:block;" />
                </div>`;
        }

        if (item.foto_preview_url && item.foto_kind === 'pdf') {
            return `
                <div class="mb-2 border rounded overflow-hidden bg-white" style="height:220px;">
                    <embed src="${item.foto_preview_url}" type="application/pdf" width="100%" height="100%" />
                </div>`;
        }

        return '';
    }

    reklameList.forEach(function(item) {
        // Determine marker color: Terbit (green), Aktif (teal), Kedaluwarsa (red)
        let markerColor = '#198754'; // default active
        if (item.is_terbit) {
            markerColor = '#28a745'; // brighter green for published
        } else if (item.is_expired) {
            markerColor = '#dc3545';
        }

        const marker = L.circleMarker([item.latitude, item.longitude], {
            radius: 8,
            fillColor: markerColor,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.95
        }).addTo(map);

        const popup = `
            <div style="min-width: 280px; max-width: 380px;">
                <h6 class="mb-2">${escapeHtml(item.nomor_registrasi)}</h6>
                ${buildFotoBlock(item)}
                <p class="mb-2 small"><strong>Pemilik/Pemohon:</strong> ${escapeHtml(item.nama_pemilik)}</p>
                <p class="mb-2 small"><strong>Jenis:</strong> ${escapeHtml(item.jenis_reklame)}</p>
                <p class="mb-2 small"><strong>Alamat/Lokasi:</strong> ${escapeHtml(item.lokasi_pemasangan)}</p>
                <p class="mb-2 small"><strong>Ukuran:</strong> ${escapeHtml(item.ukuran)}</p>
                <p class="mb-2 small"><strong>Masa Berlaku:</strong> ${escapeHtml(item.masa_berlaku)}</p>
                ${item.tanggal_terbit ? `<p class="mb-2 small"><strong>Tanggal Terbit:</strong> ${escapeHtml(item.tanggal_terbit)}</p>` : ''}
                <p class="mb-2 small"><strong>Status:</strong> <span class="badge" style="background-color: ${markerColor};">${escapeHtml(item.status_text)}</span></p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="${item.detail_url}" class="btn btn-sm btn-primary" target="_blank" rel="noopener">Lihat Detail</a>
                    <a href="${item.google_maps_url}" class="btn btn-sm btn-success" target="_blank" rel="noopener">Google Maps</a>
                </div>
            </div>
        `;

        marker.bindPopup(popup, { maxWidth: 400, minWidth: 280 });
    });

    if (reklameList.length > 0) {
        const bounds = L.latLngBounds(reklameList.map(item => [item.latitude, item.longitude]));
        map.fitBounds(bounds, { padding: [40, 40] });
    }

    // Add legend to map
    const legend = L.control({ position: 'bottomright' });

    legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.backgroundColor = 'white';
        div.style.padding = '10px 15px';
        div.style.border = '2px solid #ccc';
        div.style.borderRadius = '5px';
        div.style.fontSize = '12px';
        div.style.boxShadow = '0 0 8px rgba(0,0,0,0.2)';

        const labels = [
            { color: '#198754', label: 'Aktif' },
            { color: '#dc3545', label: 'Kedaluwarsa' }
        ];

        let html = '<strong style="display:block; margin-bottom:8px;">Status Reklame</strong>';
        labels.forEach(function(item) {
            html += `<div style="display:flex; align-items:center; margin-bottom:5px;">
                        <span style="display:inline-block; width:14px; height:14px; background-color:${item.color}; border:2px solid white; border-radius:50%; margin-right:8px;"></span>
                        <span>${item.label}</span>
                     </div>`;
        });

        div.innerHTML = html;
        return div;
    };

    legend.addTo(map);
</script>
@endpush
