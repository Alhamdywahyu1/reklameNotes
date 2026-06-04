@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="header-page">
    <h1><i class="bi bi-person-gear"></i> Edit User</h1>
    <p class="text-muted">{{ $user->name }} ({{ $user->email }})</p>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                            id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                            id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru (Kosongkan jika tidak ingin ubah)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role *</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id) == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">Jika diubah ke <strong>Satpol PP</strong>, user akan diarahkan ke peta pengawasan reklame saat login.</small>
                    </div>

                    @if ($user->id !== 1)
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                    @checked(old('is_active', $user->is_active))>
                                <label class="form-check-label" for="is_active">
                                    Aktifkan user
                                </label>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update User
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Info User</h6>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-6">ID:</dt>
                    <dd class="col-6"><code>{{ $user->id }}</code></dd>

                    <dt class="col-6">Dibuat:</dt>
                    <dd class="col-6">{{ $user->created_at->format('d M Y H:i') }}</dd>

                    <dt class="col-6">Diupdate:</dt>
                    <dd class="col-6">{{ $user->updated_at->format('d M Y H:i') }}</dd>

                    <dt class="col-6">Status:</dt>
                    <dd class="col-6">
                        @if ($user->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @endif
                    </dd>

                    <dt class="col-6">Role Saat Ini:</dt>
                    <dd class="col-6">
                        @php
                            $roleSlug = $user->role?->slug;
                            $badgeClass = match ($roleSlug) {
                                'pemohon' => 'bg-secondary',
                                'operator' => 'bg-primary',
                                'kepala_seksi' => 'bg-info text-dark',
                                'kepala_bidang' => 'bg-success',
                                'satpol_pp' => 'bg-danger',
                                'admin' => 'bg-dark',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $user->role?->name ?? 'No Role' }}</span>
                    </dd>
                </dl>

                @if ($user->role?->slug === 'satpol_pp')
                    <div class="alert alert-danger mb-0 mt-3">
                        <i class="bi bi-shield-check"></i> User ini difokuskan untuk pengawasan lapangan melalui halaman peta Satpol PP.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
