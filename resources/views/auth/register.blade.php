@extends('layouts.app')

@section('title', 'Daftar Akun - Sistem Reklame DPMPTSP')

@section('content')
<style>
    body {
        background: linear-gradient(145deg, #f0faff 0%, #e6f7ff 45%, #d9f0fe 100%);
        min-height: 100vh;
    }

    .register-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem;
    }

    .register-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 20px 60px rgba(2, 132, 199, 0.15);
        overflow: hidden;
        max-width: 500px;
        width: 100%;
        border: 1px solid rgba(2, 132, 199, 0.1);
    }

    .register-header {
        background: linear-gradient(135deg, #075985 0%, #0369a1 100%);
        padding: 2rem 2rem 1.5rem 2rem;
        color: white;
        text-align: center;
    }

    .register-header .logo-section {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }

    .register-header img {
        height: 50px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border: 2px solid #b9e6f5;
    }

    .register-header h2 {
        font-size: 1.7rem;
        font-weight: 700;
        margin: 0;
    }

    .register-header p {
        margin: 0.25rem 0 0 0;
        opacity: 0.85;
        font-size: 0.9rem;
        color: #e0f2fe;
    }

    .register-body {
        padding: 2rem;
    }

    .form-group-register {
        margin-bottom: 1.25rem;
    }

    .form-group-register label {
        display: block;
        margin-bottom: 0.4rem;
        font-weight: 600;
        color: #0a3b4e;
        font-size: 0.9rem;
    }

    .form-group-register input,
    .form-group-register textarea {
        width: 100%;
        padding: 0.65rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }

    .form-group-register input:focus,
    .form-group-register textarea:focus {
        outline: none;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
        background: #fff;
    }

    .form-group-register input.is-invalid,
    .form-group-register textarea.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        display: block;
        margin-top: 0.35rem;
        font-size: 0.8rem;
        color: #ef4444;
    }

    .btn-register {
        width: 100%;
        padding: 0.8rem;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: white;
        border: none;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        margin: 1rem 0 0.75rem 0;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(2, 132, 199, 0.3);
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
    }

    .register-divider {
        display: flex;
        align-items: center;
        margin: 1.25rem 0;
        color: #cbd5e1;
        font-size: 0.85rem;
    }

    .register-divider::before,
    .register-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .register-divider span {
        margin: 0 1rem;
        color: #64748b;
    }

    .register-footer {
        text-align: center;
        padding-top: 0.5rem;
    }

    .register-footer p {
        margin: 0;
        color: #475569;
        font-size: 0.9rem;
    }

    .register-footer a {
        color: #0284c7;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .register-footer a:hover {
        color: #0369a1;
        text-decoration: underline;
    }

    .form-row {
        display: flex;
        gap: 1rem;
    }

    .form-row .form-group-register {
        flex: 1;
    }

    .info-banner {
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        padding: 1rem 1.25rem;
        border-radius: 0 0 16px 16px;
    }

    .info-banner p {
        margin: 0;
        color: #0c2340;
        font-size: 0.82rem;
    }

    @media (max-width: 576px) {
        .form-row {
            flex-direction: column;
            gap: 0;
        }
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <!-- Header -->
        <div class="register-header">
            <div class="logo-section">
                <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo DPMPTSP" onerror="this.style.display='none'">
                <div>
                    <h2>DPMPTSP</h2>
                </div>
            </div>
            <p>Pendaftaran Akun Pemohon Reklame</p>
        </div>

        <!-- Body -->
        <div class="register-body">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nama Lengkap -->
                <div class="form-group-register">
                    <label for="name"><i class="bi bi-person"></i> Nama Lengkap</label>
                    <input type="text" class="@error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group-register">
                    <label for="email"><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" class="@error('email') is-invalid @enderror"
                        id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone & Alamat -->
                <div class="form-group-register">
                    <label for="phone"><i class="bi bi-telephone"></i> Nomor Telepon</label>
                    <input type="tel" class="@error('phone') is-invalid @enderror"
                        id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group-register">
                    <label for="address"><i class="bi bi-geo-alt"></i> Alamat</label>
                    <textarea class="@error('address') is-invalid @enderror"
                        id="address" name="address" rows="2" placeholder="Masukkan alamat lengkap" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-row">
                    <div class="form-group-register">
                        <label for="password"><i class="bi bi-lock"></i> Password</label>
                        <input type="password" class="@error('password') is-invalid @enderror"
                            id="password" name="password" placeholder="Min. 8 karakter" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <!-- Password Strength Meter -->
                        <div id="strength-bar-wrap" style="display:none; margin-top:6px;">
                            <div style="height:5px; border-radius:3px; background:#e2e8f0; overflow:hidden;">
                                <div id="strength-bar" style="height:100%; width:0; border-radius:3px; transition:all 0.3s;"></div>
                            </div>
                            <div id="strength-label" style="font-size:11px; margin-top:4px; font-weight:600;"></div>
                            <div id="strength-hints" style="font-size:11px; color:#64748b; margin-top:3px;"></div>
                        </div>
                    </div>

                    <div class="form-group-register">
                        <label for="password_confirmation"><i class="bi bi-lock-fill"></i> Konfirmasi</label>
                        <input type="password" class="@error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation" placeholder="Ulangi password" required autocomplete="new-password">
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="match-hint" style="font-size:11px; margin-top:4px; font-weight:600; display:none;"></div>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-register">
                    <i class="bi bi-person-plus"></i> Daftar Sekarang
                </button>
            </form>

            <div class="register-divider">
                <span>Atau</span>
            </div>

            <!-- Google Register -->
            <a href="{{ route('auth.google') }}" style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; border: 1.5px solid #e2e8f0; border-radius: 2rem; text-decoration: none; color: #334155; font-weight: 600; font-size: 0.95rem; transition: all 0.25s ease; background: #fff; cursor: pointer;"
                onmouseover="this.style.borderColor='#0284c7'; this.style.boxShadow='0 4px 12px rgba(2,132,199,0.1)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.transform='none'">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Daftar dengan Google
            </a>

            <div style="height: 1rem;"></div>

            <!-- Login Link -->
            <div class="register-footer">
                <p>Sudah punya akun?</p>
                <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right"></i> Login di sini</a>
            </div>
        </div>

        <!-- Info Banner -->
        <div class="info-banner">
            <p><i class="bi bi-info-circle"></i> <strong>Informasi:</strong> Setelah mendaftar, Anda dapat langsung membuat permohonan izin reklame melalui dashboard.</p>
        </div>
    </div>
</div>

<script>
const pwInput   = document.getElementById('password');
const confInput = document.getElementById('password_confirmation');
const bar       = document.getElementById('strength-bar');
const label     = document.getElementById('strength-label');
const hints     = document.getElementById('strength-hints');
const wrap      = document.getElementById('strength-bar-wrap');
const matchHint = document.getElementById('match-hint');

function calcStrength(pw) {
    let score = 0;
    const checks = [
        { re: /.{8,}/,          msg: '8+ karakter' },
        { re: /.{12,}/,         msg: '12+ karakter' },
        { re: /[a-z]/,          msg: 'huruf kecil' },
        { re: /[A-Z]/,          msg: 'huruf besar' },
        { re: /[0-9]/,          msg: 'angka' },
        { re: /[^A-Za-z0-9]/,  msg: 'simbol (!@#...)' },
    ];
    const missing = [];
    checks.forEach(c => {
        if (c.re.test(pw)) score++;
        else missing.push(c.msg);
    });
    return { score, missing };
}

pwInput.addEventListener('input', () => {
    const pw = pwInput.value;
    if (!pw) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    const { score, missing } = calcStrength(pw);
    const levels = [
        { pct: 0,   color: '#e2e8f0', text: '' },
        { pct: 17,  color: '#ef4444', text: '😟 Sangat Lemah' },
        { pct: 33,  color: '#f97316', text: '😐 Lemah' },
        { pct: 50,  color: '#eab308', text: '🙂 Cukup' },
        { pct: 67,  color: '#22c55e', text: '😊 Kuat' },
        { pct: 83,  color: '#16a34a', text: '💪 Sangat Kuat' },
        { pct: 100, color: '#0284c7', text: '🔒 Sempurna' },
    ];
    const lv = levels[score] || levels[0];
    bar.style.width   = lv.pct + '%';
    bar.style.background = lv.color;
    label.style.color = lv.color;
    label.textContent = lv.text;
    hints.textContent = missing.length ? 'Perlu: ' + missing.join(', ') : '✅ Semua kriteria terpenuhi!';
    checkMatch();
});

confInput.addEventListener('input', checkMatch);

function checkMatch() {
    const pw = pwInput.value;
    const cf = confInput.value;
    if (!cf) { matchHint.style.display = 'none'; return; }
    matchHint.style.display = 'block';
    if (pw === cf) {
        matchHint.style.color = '#16a34a';
        matchHint.textContent = '✅ Password cocok!';
    } else {
        matchHint.style.color = '#ef4444';
        matchHint.textContent = '❌ Password tidak cocok';
    }
}
</script>
@endsection
