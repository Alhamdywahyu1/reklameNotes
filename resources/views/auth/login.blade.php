@extends('layouts.app')

@section('title', 'Login - Sistem Reklame')

@section('content')
<style>
    body {
        background: linear-gradient(145deg, #f0faff 0%, #e6f7ff 45%, #d9f0fe 100%);
        min-height: 100vh;
    }
    
    .login-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem;
    }

    .login-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 20px 60px rgba(2, 132, 199, 0.15);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        border: 1px solid rgba(2, 132, 199, 0.1);
    }

    .login-header {
        background: linear-gradient(135deg, #075985 0%, #0369a1 100%);
        padding: 2.5rem 2rem;
        color: white;
        text-align: center;
        border-bottom: none;
    }

    .login-header .logo-section {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .login-header img {
        height: 50px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border: 2px solid #b9e6f5;
    }

    .login-header h2 {
        font-size: 1.7rem;
        font-weight: 700;
        margin: 0;
    }

    .login-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.85;
        font-size: 0.95rem;
        color: #e0f2fe;
    }

    .login-body {
        padding: 2.5rem 2rem;
    }

    .form-group-login {
        margin-bottom: 1.5rem;
    }

    .form-group-login label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: #0a3b4e;
        font-size: 0.95rem;
    }

    .form-group-login input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.75rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-group-login input:focus {
        outline: none;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
    }

    .form-group-login input.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #ef4444;
    }

    .form-check-modern {
        display: flex;
        align-items: center;
        margin: 1.5rem 0;
    }

    .form-check-modern input {
        width: 18px;
        height: 18px;
        margin-right: 0.5rem;
        cursor: pointer;
        accent-color: #0284c7;
    }

    .form-check-modern label {
        margin: 0;
        cursor: pointer;
        font-weight: 500;
        color: #475569;
    }

    .btn-login {
        width: 100%;
        padding: 0.85rem;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: white;
        border: none;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.25s ease;
        margin: 1.5rem 0 1rem 0;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(2, 132, 199, 0.3);
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
    }

    .login-divider {
        display: flex;
        align-items: center;
        margin: 2rem 0;
        color: #cbd5e1;
        font-size: 0.9rem;
    }

    .login-divider::before,
    .login-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .login-divider span {
        margin: 0 1rem;
        color: #64748b;
    }

    .login-footer {
        text-align: center;
        padding-top: 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

    .login-footer p {
        margin: 0;
        color: #475569;
        font-size: 0.95rem;
    }

    .login-footer a {
        color: #0284c7;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-footer a:hover {
        color: #0369a1;
        text-decoration: underline;
    }

    .forgot-password {
        text-align: center;
        margin-top: 1rem;
    }

    .forgot-password a {
        color: #0284c7;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .forgot-password a:hover {
        text-decoration: underline;
        color: #075985;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <!-- Login Header -->
        <div class="login-header">
            <div class="logo-section">
                <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo DPMPTSP">
                <div>
                    <h2>DPMPTSP</h2>
                </div>
            </div>
            <p>Sistem Pendaftaran Reklame</p>
        </div>

        <!-- Login Body -->
        <div class="login-body">
            <form method="POST" action="{{ route('login', [], false) }}">
                @csrf

                <!-- Email -->
                <div class="form-group-login">
                    <label for="email"><i class="bi bi-envelope"></i> Email</label>
                    <input type="email" class="@error('email') is-invalid @enderror" 
                        id="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email Anda" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group-login">
                    <label for="password"><i class="bi bi-lock"></i> Password</label>
                    <input type="password" class="@error('password') is-invalid @enderror" 
                        id="password" name="password" placeholder="Masukkan password Anda" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-check-modern">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>

                <!-- Forgot Password -->
                <div class="forgot-password">
                    <a href="{{ route('password.request', [], false) }}">Lupa password?</a>
                </div>
            </form>

            <div class="login-divider">
                <span>Atau</span>
            </div>

            <!-- Google Login -->
            <a href="{{ route('auth.google', [], false) }}" style="display: flex; align-items: center; justify-content: center; gap: 0.75rem; width: 100%; padding: 0.75rem; border: 1.5px solid #e2e8f0; border-radius: 2rem; text-decoration: none; color: #334155; font-weight: 600; font-size: 0.95rem; transition: all 0.25s ease; background: #fff; cursor: pointer;"
                onmouseover="this.style.borderColor='#0284c7'; this.style.boxShadow='0 4px 12px rgba(2,132,199,0.1)'; this.style.transform='translateY(-1px)'"
                onmouseout="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'; this.style.transform='none'">
                <svg width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Login dengan Google
            </a>

            <div style="height: 1rem;"></div>

            <!-- Register Link -->
            <div class="login-footer">
                <p>Belum punya akun?</p>
                <a href="{{ route('register', [], false) }}"><i class="bi bi-person-plus"></i> Daftar di sini</a>
            </div>
        </div>

        <!-- Demo Account Info -->
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1.5rem 2rem; border-radius: 0 0 16px 16px;">
            <p style="font-weight: 600; color: #0c2340; margin-bottom: 0.75rem; font-size: 0.9rem;">
                <i class="bi bi-info-circle"></i> Akun Demo Tersedia
            </p>
            <p style="color: #0c2340; font-size: 0.85rem; margin: 0.25rem 0;">
                <strong>Email:</strong> pemohon@dpmptsp.local
            </p>
            <p style="color: #0c2340; font-size: 0.85rem; margin: 0;">
                <strong>Password:</strong> password123
            </p>
        </div>
    </div>
</div>
@endsection
