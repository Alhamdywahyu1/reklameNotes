@extends('layouts.app')

@section('title', 'Peta Reklame')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-map"></i> Peta Digital Reklame</h1>
    <p class="text-muted">Visualisasi titik reklame yang sudah disetujui Kepala Bidang dan siap dipantau di lapangan</p>
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
                    Peta menampilkan reklame yang sudah <strong>Disetujui Kepala Bidang</strong>. Titik <strong>hijau</strong> berarti izin masih aktif, sedangkan titik <strong>merah</strong> berarti masa berlaku sudah habis dan tetap tampil sampai dihapus manual oleh operator.
                </p>
                <hr>
                <h6 class="small"><strong>Legenda:</strong></h6>
                <div class="small text-muted">
                    <div class="mb-2">
                        <span class="badge bg-success">Hijau</span> - Reklame sudah disetujui Kepala Bidang dan masih aktif
                    </div>
                    <div>
                        <span class="badge bg-danger">Merah</span> - Masa berlaku reklame sudah habis dan menunggu tindak lanjut/operator hapus manual
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
                                        @if($item->tanggal_berakhir)
                                            <div class="small text-muted mt-1">
                                                Berakhir: {{ $item->tanggal_berakhir->format('d M Y') }}
                                            </div>
                                        @endif
                                    </div>
                                    @php
                                        $isExpired = $item->isKedaluwarsa();
                                        $color = $isExpired ? '#dc3545' : '#198754';
                                        $label = $isExpired ? 'Masa Berlaku Habis' : 'Aktif';
                                    @endphp
                                    <span class="badge" style="background-color: {{ $color }};">
                                        {{ $label }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> Belum ada reklame yang disetujui Kepala Bidang dengan koordinat lokasi.
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

    .popup-detail-btn {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        opacity: 1;
    }

    .popup-detail-btn:hover,
    .popup-detail-btn:focus {
        background-color: #0b5ed7;
        border-color: #0a58ca;
        color: #fff;
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

    // Data permohonan dari server (foto reklame = dokumen "Foto kondisi & visualisasi reklame")
    const permohonanData = {!! json_encode($permohonan->map(function ($item) {
        $fotoRow = $item->persyaratanDokumen->first();
        $fotoPreviewUrl = null;
        $fotoKind = null;
        if ($fotoRow && $fotoRow->file_dokumen) {
            $fotoPreviewUrl = route('document-requirements.preview', $fotoRow);
            $ext = strtolower(pathinfo($fotoRow->file_dokumen, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $fotoKind = 'image';
            } elseif ($ext === 'pdf') {
                $fotoKind = 'pdf';
            }
        }

        return [
            'id' => $item->id,
            'nomor_registrasi' => $item->nomor_registrasi,
            'nama_pemohon' => $item->nama_pemohon,
            'latitude' => (float) $item->latitude,
            'longitude' => (float) $item->longitude,
            'lokasi_pemasangan' => $item->lokasi_pemasangan,
            'jenis_reklame' => $item->jenis_reklame,
            'status' => $item->status,
            'tanggal_berlaku' => optional($item->tanggal_berlaku)->format('Y-m-d'),
            'tanggal_berakhir' => optional($item->tanggal_berakhir)->format('Y-m-d'),
            'status_kedaluwarsa' => $item->getStatusKedaluarsa(),
            'is_kedaluwarsa' => $item->isKedaluwarsa(),
            'status_approval_label' => $item->status === 'Sudah Terbit' ? 'Telah diprint oleh operator' : $item->status,
            'url' => route('permohonan.show', $item),
            'foto_preview_url' => $fotoPreviewUrl,
            'foto_kind' => $fotoKind,
        ];
    })->values()->toArray()) !!};

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function buildFotoReklameBlock(item) {
        if (item.foto_preview_url && item.foto_kind === 'image') {
            const safeUrl = String(item.foto_preview_url).replace(/"/g, '&quot;');
            return `
                <div class="mb-2 border rounded overflow-hidden bg-light">
                    <img src="${safeUrl}" alt="Foto kondisi dan visualisasi reklame" loading="lazy"
                        style="width:100%; max-height:320px; height:auto; object-fit:contain; display:block; vertical-align:middle;" />
                </div>
                <p class="small text-muted mb-0">Foto kondisi &amp; visualisasi reklame (unggahan pemohon).</p>`;
        }
        if (item.foto_preview_url && item.foto_kind === 'pdf') {
            const safeUrl = String(item.foto_preview_url).replace(/"/g, '&quot;');
            return `
                <div class="mb-2 border rounded overflow-hidden bg-white" style="height:260px;">
                    <embed src="${safeUrl}" type="application/pdf" width="100%" height="100%" style="display:block;" />
                </div>
                <p class="small text-muted mb-0">PDF unggahan pemohon (tampil langsung). Di beberapa ponsel PDF bisa tidak tampil di sini — gunakan unduh dari halaman detail.</p>`;
        }
        return `<p class="small text-muted mb-2"><i class="bi bi-image"></i> Belum ada unggahan &ldquo;Foto kondisi &amp; visualisasi reklame&rdquo;.</p>`;
    }

    function formatTanggalIndonesia(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString + 'T00:00:00');
        if (Number.isNaN(date.getTime())) return dateString;

        return new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }).format(date);
    }

    function getMarkerColor(item) {
        return item.is_kedaluwarsa ? '#dc3545' : '#198754';
    }

    function getMarkerLabel(item) {
        return item.is_kedaluwarsa ? 'Masa Berlaku Habis' : 'Aktif';
    }

    // Add markers to cluster
    permohonanData.forEach(function(item) {
        if (item.latitude && item.longitude) {
            const statusColor = getMarkerColor(item);
            const marker = L.circleMarker([item.latitude, item.longitude], {
                radius: 8,
                fillColor: statusColor,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            });

            const popup = `
                <div style="min-width: 280px; max-width: 380px;">
                    <h6 class="mb-2">${escapeHtml(item.nomor_registrasi)}</h6>
                    ${buildFotoReklameBlock(item)}
                    <p class="mb-2 small"><strong>Pemohon:</strong> ${escapeHtml(item.nama_pemohon)}</p>
                    <p class="mb-2 small"><strong>Jenis:</strong> ${escapeHtml(item.jenis_reklame)}</p>
                    <p class="mb-2 small"><strong>Lokasi:</strong> ${escapeHtml(item.lokasi_pemasangan)}</p>
                    <p class="mb-2 small"><strong>Status Approval:</strong> ${escapeHtml(item.status_approval_label)}</p>
                    <p class="mb-2 small"><strong>Status Peta:</strong> <span class="badge" style="background-color: ${statusColor};">${getMarkerLabel(item)}</span></p>
                    <p class="mb-2 small"><strong>Tanggal Berakhir:</strong> ${formatTanggalIndonesia(item.tanggal_berakhir)}</p>
                    <p class="mb-0 small"><strong>Koordinat:</strong> ${item.latitude.toFixed(6)}, ${item.longitude.toFixed(6)}</p>
                    <a href="${String(item.url).replace(/"/g, '&quot;')}" class="btn btn-sm btn-primary popup-detail-btn mt-2" target="_blank" rel="noopener">Lihat Detail</a>
                </div>
            `;

            marker.bindPopup(popup, { maxWidth: 400, minWidth: 280 });
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

</script>
@endpush
