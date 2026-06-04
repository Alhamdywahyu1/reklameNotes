@extends('layouts.app')

@section('title', 'Manajemen User')

@php
    $roleBadgeClasses = [
        'pemohon' => 'bg-secondary',
        'operator' => 'bg-primary',
        'kepala_seksi' => 'bg-info text-dark',
        'kepala_bidang' => 'bg-success',
        'satpol_pp' => 'bg-danger',
        'admin' => 'bg-dark',
    ];
@endphp

@section('content')
<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-people"></i> Manajemen Akun Petugas</h1>
            <p class="text-muted">Kelola akun petugas sistem (Operator, Kepala Seksi, Kepala Bidang, Satpol PP)</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Akun Petugas
        </a>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-ul"></i> Daftar User</h5>
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" style="width: 250px;" placeholder="Cari nama atau email..." value="{{ $search }}">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="bi bi-search"></i> Cari
            </button>
            @if (!empty($search))
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </form>
    </div>
    <div class="card-body">
        @if (!empty($search))
            <div class="alert alert-info mb-3">
                <i class="bi bi-info-circle"></i> Hasil pencarian untuk "<strong>{{ $search }}</strong>" menampilkan {{ $users->total() }} user
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleSlug = $user->role?->slug;
                                    $badgeClass = $roleBadgeClasses[$roleSlug] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $user->role?->name ?? 'No Role' }}</span>
                                @if ($roleSlug === 'satpol_pp')
                                    <small class="text-muted d-block mt-1">Akses pengawasan lapangan</small>
                                @endif
                            </td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                @if ($user->id !== 1 && $user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Yakin ingin menghapus user ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox"></i> Tidak ada user
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $users->links() }}
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="bi bi-diagram-3"></i> Ringkasan Role Petugas</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle"></i>
            Halaman ini hanya menampilkan dan mengelola akun <strong>petugas</strong>.
            Akun pemohon (pengguna umum) dikelola sendiri melalui pendaftaran mandiri.
        </div>
        <div class="row g-3 small">
            <div class="col-md-3">
                <span class="badge bg-primary mb-2">Operator</span>
                <div class="text-muted">Verifikasi awal, cetak dokumen, dan pengelolaan data kedaluwarsa.</div>
            </div>
            <div class="col-md-3">
                <span class="badge bg-info text-dark mb-2">Kepala Seksi</span>
                <div class="text-muted">Melakukan approval tahap lanjutan.</div>
            </div>
            <div class="col-md-3">
                <span class="badge bg-success mb-2">Kepala Bidang</span>
                <div class="text-muted">Memberikan persetujuan final terhadap permohonan.</div>
            </div>
            <div class="col-md-3">
                <span class="badge bg-danger mb-2">Satpol PP</span>
                <div class="text-muted">Pemantauan reklame lapangan melalui peta pengawasan.</div>
            </div>
        </div>
    </div>
</div>
@endsection
