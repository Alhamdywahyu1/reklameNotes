@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-bell"></i> Notifikasi</h1>
            <p class="text-muted">Kelola semua notifikasi Anda</p>
        </div>
        @if ($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-as-read') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-all"></i> Tandai Semua Terbaca
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Unread Count -->
@if ($unreadCount > 0)
    <div class="alert alert-info mb-4">
        <i class="bi bi-exclamation-circle"></i>
        Anda memiliki <strong>{{ $unreadCount }} notifikasi</strong> yang belum dibaca
    </div>
@endif

<!-- Notifications List -->
<div class="row">
    <div class="col-lg-10 mx-auto">
        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                <div class="card mb-3 border-left-{{ $notification->type === 'PENGAJUAN_BARU' ? 'warning' : ($notification->type === 'PERMOHONAN_DITOLAK' ? 'danger' : ($notification->type === 'SURAT_DIPRINT' ? 'info' : 'success')) }}" 
                     style="border-left: 4px solid {{ $notification->type === 'PENGAJUAN_BARU' ? '#ffc107' : ($notification->type === 'PERMOHONAN_DITOLAK' ? '#dc3545' : ($notification->type === 'SURAT_DIPRINT' ? '#0d6efd' : '#28a745')) }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    @if ($notification->type === 'PENGAJUAN_BARU')
                                        <span class="badge bg-warning me-2">
                                            <i class="bi bi-plus-circle"></i> Pengajuan Baru
                                        </span>
                                    @elseif ($notification->type === 'PERMOHONAN_DITOLAK')
                                        <span class="badge bg-danger me-2">
                                            <i class="bi bi-x-circle"></i> Ditolak
                                        </span>
                                    @elseif ($notification->type === 'SURAT_DIPRINT')
                                        <span class="badge bg-info me-2">
                                            <i class="bi bi-printer"></i> Surat Siap
                                        </span>
                                    @else
                                        <span class="badge bg-success me-2">
                                            <i class="bi bi-check-circle"></i> Status Berubah
                                        </span>
                                    @endif
                                    @if ($notification->isUnread())
                                        <span class="badge bg-primary">Baru</span>
                                    @endif
                                </div>
                                <h6 class="card-title mb-1">{{ $notification->title }}</h6>
                                <p class="card-text text-muted mb-2">{{ $notification->message }}</p>
                                <small class="text-muted d-block mb-3">
                                    <i class="bi bi-clock"></i> {{ $notification->created_at->diffForHumans() }}
                                </small>
                                @if ($notification->permohonan_id)
                                    <a href="{{ route('permohonan.show', $notification->permohonan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right"></i> Lihat Permohonan
                                    </a>
                                @endif
                            </div>
                            <div class="dropdown ms-3">
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if ($notification->isUnread())
                                        <li>
                                            <form action="{{ route('notifications.mark-as-read', $notification) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-check"></i> Tandai Terbaca
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <form action="{{ route('notifications.destroy', $notification) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Tidak ada notifikasi
            </div>
        @endif
    </div>
</div>

<style>
    .border-left-warning {
        border-left: 4px solid #ffc107 !important;
    }
    
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }

    .border-left-info {
        border-left: 4px solid #0d6efd !important;
    }
</style>
@endsection
