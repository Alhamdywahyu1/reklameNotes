@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<style>
    body {
        background: linear-gradient(145deg, #f0faff 0%, #e6f7ff 45%, #d9f0fe 100%);
        min-height: 100vh;
    }

    .verify-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem;
    }

    .verify-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 20px 60px rgba(2, 132, 199, 0.15);
        overflow: hidden;
        max-width: 480px;
        width: 100%;
        border: 1px solid rgba(2, 132, 199, 0.1);
    }

    .verify-header {
        background: linear-gradient(135deg, #075985 0%, #0369a1 100%);
        padding: 2rem;
        color: white;
        text-align: center;
    }

    .verify-header img {
        height: 50px;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border: 2px solid #b9e6f5;
        margin-bottom: 0.75rem;
    }

    .verify-body {
        padding: 2.5rem 2rem;
        text-align: center;
    }

    .verify-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .verify-icon i {
        font-size: 2.5rem;
        color: #0369a1;
    }

    .btn-verify {
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
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);
    }

    .btn-verify:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(2, 132, 199, 0.3);
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%);
        color: white;
    }
</style>

<div class="verify-wrapper">
    <div class="verify-card">
        <div class="verify-header">
            <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo" onerror="this.style.display='none'">
            <h4 style="margin: 0; font-weight: 700;">Verifikasi Email</h4>
            <p style="margin: 0.25rem 0 0; opacity: 0.85; font-size: 0.9rem;">DPMPTSP Kabupaten Bangkalan</p>
        </div>

        <div class="verify-body">
            <div class="verify-icon">
                <i class="bi bi-envelope-check"></i>
            </div>

            <h5 style="font-weight: 700; color: #0a3b4e; margin-bottom: 0.75rem;">Cek Email Anda</h5>

            <p style="color: #475569; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                Kami telah mengirimkan link verifikasi ke email <strong>{{ auth()->user()->email }}</strong>.
                Silakan klik link tersebut untuk mengaktifkan akun Anda.
            </p>

            @if (session('success'))
                <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 0.75rem; padding: 0.75rem 1rem; margin-bottom: 1.25rem; color: #166534; font-size: 0.9rem;">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-verify">
                    <i class="bi bi-envelope-arrow-up"></i> Kirim Ulang Link Verifikasi
                </button>
            </form>

            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                <form method="POST" action="{{ route('logout', [], false) }}">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: #0284c7; font-weight: 600; cursor: pointer; font-size: 0.9rem;">
                        <i class="bi bi-box-arrow-left"></i> Logout & Kembali
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
