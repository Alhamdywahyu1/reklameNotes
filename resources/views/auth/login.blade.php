@extends('layouts.app')

@section('title', 'Login - Sistem Reklame')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a5f 100%);
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
        border-radius: 12px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        max-width: 450px;
        width: 100%;
        border-top: 5px solid #b8860b;
    }

    .login-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2.5rem 2rem;
        color: white;
        text-align: center;
        border-bottom: 3px solid #b8860b;
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
        border: 2px solid #b8860b;
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
        color: #d4a84b;
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
        color: #0f172a;
        font-size: 0.95rem;
    }

    .form-group-login input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-group-login input:focus {
        outline: none;
        border-color: #0f172a;
        box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
    }

    .form-group-login input.is-invalid {
        border-color: #991b1b;
        box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
    }

    .invalid-feedback {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        color: #991b1b;
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
        accent-color: #0f172a;
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
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: white;
        border: 2px solid #b8860b;
        border-radius: 6px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 1.5rem 0 1rem 0;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(15, 23, 42, 0.4);
        background: linear-gradient(135deg, #b8860b 0%, #d4a84b 100%);
        border-color: #b8860b;
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
        color: #0f172a;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .login-footer a:hover {
        color: #b8860b;
        text-decoration: underline;
    }

    .forgot-password {
        text-align: center;
        margin-top: 1rem;
    }

    .forgot-password a {
        color: #b8860b;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .forgot-password a:hover {
        text-decoration: underline;
        color: #8b6914;
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
            <form method="POST" action="{{ route('login') }}">
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
                    <a href="{{ route('password.request') }}">Lupa password?</a>
                </div>
            </form>

            <div class="login-divider">
                <span>Atau</span>
            </div>

            <!-- Register Link -->
            <div class="login-footer">
                <p>Belum punya akun?</p>
                <a href="{{ route('register') }}"><i class="bi bi-person-plus"></i> Daftar di sini</a>
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
