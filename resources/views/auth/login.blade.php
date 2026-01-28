@extends('layouts.app')

@section('title', 'Login - Sistem Reklame')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #1a5490 0%, #0f3a60 100%);
        min-height: 100vh;
    }
    
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 85vh;
        padding: 2rem;
    }
</style>

<div class="login-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="h4" style="color: #1a5490;">
                                <i class="bi bi-building"></i> DPMPTSP
                            </h2>
                            <p class="text-muted small">Sistem Pendaftaran Reklame</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>

                        <div class="text-center">
                            <p class="small mb-0">
                                Belum punya akun? <a href="{{ route('register') }}" style="color: #1a5490;">Daftar di sini</a>
                            </p>
                        </div>

                        <hr>

                        <div class="alert alert-info small mb-0">
                            <strong>Akun Demo:</strong><br>
                            Email: pemohon@dpmptsp.local<br>
                            Password: password123
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
