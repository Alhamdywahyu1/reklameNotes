@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@push('styles')
<style>
    .profile-avatar-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        font-weight: 700;
        margin: 0 auto 1rem;
        box-shadow: 0 6px 20px rgba(2, 132, 199, 0.35);
        position: relative;
    }
    .profile-avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .profile-header-card {
        background: linear-gradient(135deg, #0a3b4e 0%, #0369a1 100%);
        border-radius: 1rem;
        color: white;
        padding: 1.5rem 1rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .profile-header-card .role-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        max-width: 100%;
        word-break: break-word;
    }
    .nav-tabs-profile .nav-link {
        color: #64748b;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border: none;
        border-bottom: 3px solid transparent;
        border-radius: 0;
        transition: all 0.2s;
    }
    .nav-tabs-profile .nav-link:hover {
        color: #0284c7;
        border-bottom-color: rgba(2, 132, 199, 0.3);
    }
    .nav-tabs-profile .nav-link.active {
        color: #0284c7;
        border-bottom-color: #0284c7;
        background: transparent;
    }
    .nav-tabs-profile {
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 1.75rem;
    }
    .form-label {
        font-weight: 600;
        color: #374151;
    }
    .password-strength {
        height: 4px;
        border-radius: 2px;
        transition: all 0.3s;
        margin-top: 6px;
    }
</style>
@endpush

@section('content')
<div class="header-page">
    <h1><i class="bi bi-person-gear"></i> Pengaturan Akun</h1>
    <p class="text-muted">Kelola informasi profil dan keamanan akun Anda</p>
</div>

<div class="row g-4">

    {{-- Kolom Kiri: Kartu Profil --}}
    <div class="col-xl-3 col-lg-4">
        <div class="profile-header-card">
            <div class="profile-avatar-wrapper">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" alt="Avatar">
                @else
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                @endif
            </div>
            <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
            <p class="mb-2" style="opacity:0.8; font-size:0.88rem;">{{ auth()->user()->email }}</p>
            <span class="role-badge">{{ auth()->user()->role?->name ?? 'Pengguna' }}</span>
        </div>

        <div class="card">
            <div class="card-body small">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <div>
                        <div class="text-muted" style="font-size:0.78rem;">Bergabung sejak</div>
                        <div class="fw-semibold">{{ auth()->user()->created_at->translatedFormat('d F Y') }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-shield-check text-{{ auth()->user()->hasVerifiedEmail() ? 'success' : 'warning' }} fs-5"></i>
                    <div>
                        <div class="text-muted" style="font-size:0.78rem;">Status Email</div>
                        <div class="fw-semibold">
                            @if(auth()->user()->hasVerifiedEmail())
                                <span class="text-success">Terverifikasi</span>
                            @else
                                <span class="text-warning">Belum Terverifikasi</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if(auth()->user()->last_login_at)
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-in-right text-secondary fs-5"></i>
                    <div>
                        <div class="text-muted" style="font-size:0.78rem;">Login terakhir</div>
                        <div class="fw-semibold">{{ auth()->user()->last_login_at->diffForHumans() }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Form Tab --}}
    <div class="col-xl-9 col-lg-8">
        <div class="card">
            <div class="card-body pb-0 pt-3 px-4">
                <ul class="nav nav-tabs-profile" id="profileTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ !session('password_success') ? 'active' : '' }}"
                            id="info-tab" data-bs-toggle="tab" data-bs-target="#tab-info"
                            type="button" role="tab">
                            <i class="bi bi-person"></i> Informasi Akun
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ session('password_success') ? 'active' : '' }}"
                            id="password-tab" data-bs-toggle="tab" data-bs-target="#tab-password"
                            type="button" role="tab">
                            <i class="bi bi-lock"></i> Ganti Password
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body px-4 pt-0">
                <div class="tab-content" id="profileTabContent">

                    {{-- Tab: Informasi Akun --}}
                    <div class="tab-pane fade {{ !session('password_success') ? 'show active' : '' }}"
                        id="tab-info" role="tabpanel">

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show mt-3">
                                <i class="bi bi-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any() && !session('password_success'))
                            <div class="alert alert-danger mt-3">
                                <i class="bi bi-exclamation-circle"></i>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST" class="mt-3">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    @if($user->email !== old('email', $user->email))
                                        <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Mengubah email akan memerlukan verifikasi ulang.</small>
                                    @endif
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Nomor Telepon</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" id="phone" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $user->phone) }}"
                                            placeholder="Contoh: 08123456789">
                                    </div>
                                    @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="nik" class="form-label">NIK (Nomor Induk Kependudukan)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-credit-card-2-front"></i></span>
                                        <input type="text" id="nik" name="nik"
                                            class="form-control @error('nik') is-invalid @enderror"
                                            value="{{ old('nik', $user->nik) }}"
                                            placeholder="16 digit NIK" maxlength="20">
                                    </div>
                                    @error('nik')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea id="address" name="address"
                                        class="form-control @error('address') is-invalid @enderror"
                                        rows="3" placeholder="Jl. Contoh No.1, Kelurahan, Kecamatan, Kota">{{ old('address', $user->address) }}</textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Tab: Ganti Password --}}
                    <div class="tab-pane fade {{ session('password_success') ? 'show active' : '' }}"
                        id="tab-password" role="tabpanel">

                        @if(session('password_success'))
                            <div class="alert alert-success alert-dismissible fade show mt-3">
                                <i class="bi bi-check-circle"></i> {{ session('password_success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->has('current_password') || $errors->has('password'))
                            <div class="alert alert-danger mt-3">
                                <i class="bi bi-exclamation-circle"></i>
                                <ul class="mb-0 mt-1">
                                    @foreach($errors->get('current_password') as $e)<li>{{ $e }}</li>@endforeach
                                    @foreach($errors->get('password') as $e)<li>{{ $e }}</li>@endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('profile.password') }}" method="POST" class="mt-3" id="formPassword">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info">
                                <i class="bi bi-shield-lock"></i>
                                <strong>Tips Keamanan:</strong> Gunakan kombinasi huruf besar, huruf kecil, dan angka. Minimal 8 karakter.
                            </div>

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        placeholder="Masukkan password saat ini">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('current_password')">
                                        <i class="bi bi-eye" id="icon-current_password"></i>
                                    </button>
                                </div>
                                @error('current_password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Minimal 8 karakter"
                                        oninput="checkStrength(this.value)">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password')">
                                        <i class="bi bi-eye" id="icon-password"></i>
                                    </button>
                                </div>
                                <div class="password-strength bg-secondary" id="strengthBar" style="width:0%"></div>
                                <small class="text-muted" id="strengthLabel"></small>
                                @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control"
                                        placeholder="Ulangi password baru">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password_confirmation')">
                                        <i class="bi bi-eye" id="icon-password_confirmation"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-lock-fill"></i> Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>

                </div>{{-- end tab-content --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePass(id) {
        const input = document.getElementById(id);
        const icon  = document.getElementById('icon-' + id);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        }
    }

    function checkStrength(val) {
        const bar   = document.getElementById('strengthBar');
        const label = document.getElementById('strengthLabel');
        let score   = 0;

        if (val.length >= 8)            score++;
        if (/[A-Z]/.test(val))          score++;
        if (/[0-9]/.test(val))          score++;
        if (/[^A-Za-z0-9]/.test(val))   score++;

        const map = {
            0: { w: '10%',  cls: 'bg-danger',  txt: 'Sangat lemah' },
            1: { w: '25%',  cls: 'bg-danger',  txt: 'Lemah' },
            2: { w: '50%',  cls: 'bg-warning', txt: 'Cukup' },
            3: { w: '75%',  cls: 'bg-info',    txt: 'Kuat' },
            4: { w: '100%', cls: 'bg-success', txt: 'Sangat Kuat' },
        };

        const m = map[score];
        bar.style.width = m.w;
        bar.className   = 'password-strength ' + m.cls;
        label.textContent = val.length ? m.txt : '';
    }
</script>
@endpush
