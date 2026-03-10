@extends('layouts.app')

@section('title', 'Chart Reklame')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-bar-chart"></i> Analytics Reklame</h1>
            <p class="text-muted">Visualisasi data perbandingan reklame permanen dan non-permanen</p>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">TOTAL DISETUJUI</h6>
                        <h2 class="text-primary mb-0">{{ $totalApproved }}</h2>
                    </div>
                    <i class="bi bi-check-circle text-primary" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">REKLAME PERMANEN</h6>
                        <h2 class="text-success mb-0">{{ $reklamePermanen }}</h2>
                    </div>
                    <i class="bi bi-building text-success" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted small mb-1">REKLAME NON-PERMANEN</h6>
                        <h2 class="text-warning mb-0">{{ $reklameNonPermanen }}</h2>
                    </div>
                    <i class="bi bi-flag text-warning" style="font-size: 2rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <!-- Pie Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm chart-card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Perbandingan Jenis Reklame</h5>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="pieChart"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Bar Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-sm chart-card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Distribusi Jenis Reklame</h5>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="barChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Top Locations -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm chart-card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-geo-alt"></i> 10 Lokasi Pemasangan Terbanyak</h5>
            </div>
            <div class="card-body">
                <div class="chart-container"><canvas id="locationChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Info -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Reklame Permanen</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Total Reklame Permanen:</strong> 
                    <span class="badge bg-success">{{ $reklamePermanen }}</span>
                </p>
                <p class="text-muted small mb-0">
                    Reklame permanen adalah reklame yang dipasang secara tetap untuk jangka waktu panjang
                    (biasanya lebih dari 1 tahun).
                </p>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Reklame Non-Permanen</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Total Reklame Non-Permanen:</strong> 
                    <span class="badge bg-warning">{{ $reklameNonPermanen }}</span>
                </p>
                <p class="text-muted small mb-0">
                    Reklame non-permanen adalah reklame sementara yang dipasang untuk jangka waktu terbatas
                    (biasanya kurang dari 1 tahun seperti spanduk, billboard sementara, dll).
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Permanen', 'Non-Permanen'],
            datasets: [{
                data: [{{ $reklamePermanen }}, {{ $reklameNonPermanen }}],
                backgroundColor: [
                    '#198754',
                    '#ffc107'
                ],
                borderColor: [
                    '#1a6f3f',
                    '#e0a800'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            resizeDelay: 200,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: {
                            size: 13
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Bar Chart
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: ['Permanen', 'Non-Permanen'],
            datasets: [{
                label: 'Jumlah Reklame',
                data: [{{ $reklamePermanen }}, {{ $reklameNonPermanen }}],
                backgroundColor: [
                    '#198754',
                    '#ffc107'
                ],
                borderColor: [
                    '#1a6f3f',
                    '#e0a800'
                ],
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            resizeDelay: 200,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Location Chart
    const locationCtx = document.getElementById('locationChart').getContext('2d');
    const locationChart = new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach ($reklameByLocation as $location)
                    "{{ substr($location->lokasi_pemasangan, 0, 20) }}{{ strlen($location->lokasi_pemasangan) > 20 ? '...' : '' }}",
                @endforeach
            ],
            datasets: [{
                label: 'Jumlah Reklame',
                data: [
                    @foreach ($reklameByLocation as $location)
                        {{ $location->total }},
                    @endforeach
                ],
                backgroundColor: '#0d6efd',
                borderColor: '#0b5ed7',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            resizeDelay: 200,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<style>
    .card {
        border-radius: 0.5rem;
    }

    .chart-card {
        transition: none !important;
        transform: none !important;
    }

    .chart-card:hover {
        transform: none !important;
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075) !important;
    }

    .chart-container {
        position: relative;
        width: 100%;
    }

    .header-page {
        margin-bottom: 2rem;
    }

    .stats-card {
        padding: 1.5rem;
    }
</style>
@endsection
