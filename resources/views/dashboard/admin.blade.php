@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="header-page">
    <h1><i class="bi bi-grid-1x2"></i> Dashboard Admin</h1>
    <p class="text-muted">Overview sistem pendaftaran reklame</p>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-primary">
            <div class="card-body">
                <i class="bi bi-people display-4 mb-3"></i>
                <div class="number">{{ $totalUsers }}</div>
                <div class="label">Total Users</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-info">
            <div class="card-body">
                <i class="bi bi-file-earmark-text display-4 mb-3"></i>
                <div class="number">{{ $totalPermohonan }}</div>
                <div class="label">Total Permohonan</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-success">
            <div class="card-body">
                <i class="bi bi-check-circle display-4 mb-3"></i>
                <div class="number">{{ $totalDisetujui }}</div>
                <div class="label">Permohonan Disetujui</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card stats-card text-danger">
            <div class="card-body">
                <i class="bi bi-x-circle display-4 mb-3"></i>
                <div class="number">{{ $totalDitolak }}</div>
                <div class="label">Permohonan Ditolak</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Aktivitas Terbaru</h5>
            </div>
            <div class="card-body">
                @if($recentActivities->isEmpty())
                    <p class="text-muted">Tidak ada aktivitas terbaru.</p>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach($recentActivities as $activity)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $activity->user->name ?? '-' }}</strong>
                                    @if($activity->action)
                                        <span class="text-primary">{{ $activity->action }}</span>
                                    @endif
                                    @if($activity->model_type)
                                        <span class="text-muted">{{ class_basename($activity->model_type) }}</span>
                                    @endif
                                    <br>
                                    <span class="small text-muted">{{ $activity->description }}</span>
                                    <br>
                                    <small class="text-muted">{{ $activity->created_at ? $activity->created_at->format('d/m/Y H:i') : '-' }}</small>
                                </div>
                                @if($activity->log_name)
                                    <span class="badge bg-secondary">{{ $activity->log_name }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Permohonan Berdasarkan Status</h5>
            </div>
            <div class="card-body">
                <canvas id="permohonanStatusChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('permohonanStatusChart').getContext('2d');
        var permohonanByStatus = @json($permohonanByStatus);

        var labels = Object.keys(permohonanByStatus);
        var data = Object.values(permohonanByStatus);

        var backgroundColors = [];
        labels.forEach(function(status) {
            if (status.includes('Ditolak')) {
                backgroundColors.push('#dc3545'); // red
            } else if (status.includes('Disetujui')) {
                backgroundColors.push('#28a745'); // green
            } else if (status.includes('Diajukan') || status.includes('Menunggu')) {
                backgroundColors.push('#ffc107'); // yellow
            } else if (status.includes('Draft')) {
                backgroundColors.push('#6c757d'); // gray
            } else {
                backgroundColors.push('#0d6efd'); // blue default
            }
        });

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Permohonan Berdasarkan Status'
                    }
                }
            }
        });
    });
</script>
@endpush
