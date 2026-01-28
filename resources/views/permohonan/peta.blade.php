@extends('layouts.app')

@section('title', 'Peta Reklame')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-map"></i> Peta Digital Reklame</h1>
    <p class="text-muted">Visualisasi lokasi semua permohonan reklame</p>
</div>

<div class="card">
    <div class="card-body p-0">
        <div id="mapContainer" style="height: 600px; border-radius: 4px;"></div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-info-circle"></i> Informasi Peta</h6>
                <p class="small text-muted mb-2">
                    Peta menampilkan lokasi semua permohonan reklame yang telah diajukan.
                </p>
                <hr>
                <h6 class="small"><strong>Legenda:</strong></h6>
                <div class="small text-muted">
                    <div class="mb-2">
                        <span class="badge bg-secondary">Draft</span> - Permohonan masih dalam tahap draft
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-info">Diajukan</span> - Menunggu verifikasi
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-primary">Diverifikasi</span> - Dalam proses approval
                    </div>
                    <div class="mb-2">
                        <span class="badge bg-danger">Ditolak</span> - Permohonan ditolak
                    </div>
                    <div>
                        <span class="badge bg-success">Disetujui</span> - Permohonan disetujui
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list"></i> Daftar Permohonan pada Peta</h5>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @if($permohonan->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($permohonan as $item)
                            <a href="{{ route('permohonan.show', $item) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $item->nomor_registrasi }}</h6>
                                        <p class="mb-1 small text-muted">{{ $item->nama_pemohon }}</p>
                                        <small class="text-muted">{{ $item->lokasi_pemasangan }}</small>
                                    </div>
                                    @php
                                        $statusColors = [
                                            'Draft' => '#6c757d',
                                            'Diajukan' => '#0dcaf0',
                                            'Diverifikasi Operator' => '#ffc107',
                                            'Ditolak Operator' => '#dc3545',
                                            'Ditolak Kepala Seksi' => '#dc3545',
                                            'Disetujui Kepala Seksi' => '#0dcaf0',
                                            'Disetujui Kepala Bidang' => '#198754',
                                        ];
                                        $color = $statusColors[$item->status] ?? '#0d6efd';
                                    @endphp
                                    <span class="badge" style="background-color: {{ $color }};">
                                        {{ $item->status }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Belum ada permohonan reklame dengan koordinat lokasi.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<style>
    #mapContainer {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
<script>
    // Initialize map
    const map = L.map('mapContainer').setView([-6.200000, 106.816666], 12);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Create marker cluster group
    const markerClusterGroup = L.markerClusterGroup();

    // Data permohonan dari server
    const permohonanData = {!! json_encode($permohonan->map(function($item) {
        return [
            'id' => $item->id,
            'nomor_registrasi' => $item->nomor_registrasi,
            'nama_pemohon' => $item->nama_pemohon,
            'latitude' => (float) $item->latitude,
            'longitude' => (float) $item->longitude,
            'lokasi_pemasangan' => $item->lokasi_pemasangan,
            'jenis_reklame' => $item->jenis_reklame,
            'status' => $item->status,
            'url' => route('permohonan.show', $item),
        ];
    })->toArray()) !!};

    // Add markers to cluster
    permohonanData.forEach(function(item) {
        if (item.latitude && item.longitude) {
            const statusColor = getStatusColor(item.status);
            const marker = L.circleMarker([item.latitude, item.longitude], {
                radius: 8,
                fillColor: statusColor,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            });

            const popup = `
                <div style="min-width: 250px;">
                    <h6>${item.nomor_registrasi}</h6>
                    <p class="mb-2 small"><strong>Pemohon:</strong> ${item.nama_pemohon}</p>
                    <p class="mb-2 small"><strong>Jenis:</strong> ${item.jenis_reklame}</p>
                    <p class="mb-2 small"><strong>Lokasi:</strong> ${item.lokasi_pemasangan}</p>
                    <p class="mb-2 small"><strong>Status:</strong> <span class="badge" style="background-color: ${statusColor};">${item.status}</span></p>
                    <p class="mb-0 small"><strong>Koordinat:</strong> ${item.latitude.toFixed(6)}, ${item.longitude.toFixed(6)}</p>
                    <a href="${item.url}" class="btn btn-sm btn-primary mt-2" target="_blank">Lihat Detail</a>
                </div>
            `;

            marker.bindPopup(popup);
            marker.on('click', function() {
                this.openPopup();
            });

            markerClusterGroup.addLayer(marker);
        }
    });

    // Add cluster group to map
    map.addLayer(markerClusterGroup);

    // Auto fit bounds if there are markers
    if (permohonanData.length > 0) {
        const bounds = markerClusterGroup.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    // Function to get color based on status
    function getStatusColor(status) {
        const colors = {
            'Draft': '#6c757d',
            'Diajukan': '#0d6efd',
            'Diverifikasi Operator': '#0d6efd',
            'Ditolak Operator': '#dc3545',
            'Ditolak Kepala Seksi': '#dc3545',
            'Disetujui Kepala Seksi': '#0d6efd',
            'Disetujui Kepala Bidang': '#198754',
        };
        return colors[status] || '#999';
    }
</script>
@endpush
