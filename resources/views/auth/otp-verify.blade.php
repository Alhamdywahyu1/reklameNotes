@extends('layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
<style>
    body {
        background: linear-gradient(145deg, #f0faff 0%, #e6f7ff 45%, #d9f0fe 100%);
        min-height: 100vh;
    }
    .otp-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        padding: 2rem;
    }
    .otp-card {
        background: white;
        border-radius: 1.25rem;
        box-shadow: 0 20px 60px rgba(2, 132, 199, 0.15);
        overflow: hidden;
        max-width: 460px;
        width: 100%;
        border: 1px solid rgba(2, 132, 199, 0.1);
    }
    .otp-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        padding: 2rem;
        text-align: center;
        color: white;
    }
    .otp-header .icon-wrap {
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 2rem;
    }
    .otp-header h1 { font-size: 1.5rem; font-weight: 700; margin: 0 0 0.3rem; }
    .otp-header p  { font-size: 0.9rem; opacity: 0.85; margin: 0; }
    .otp-body { padding: 2rem; }
    .otp-email-hint {
        text-align: center;
        color: #64748b;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    .otp-email-hint strong { color: #0284c7; }
    .otp-inputs {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 1.5rem 0;
    }
    .otp-inputs input {
        width: 52px;
        height: 60px;
        text-align: center;
        font-size: 1.6rem;
        font-weight: 700;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        outline: none;
        transition: all 0.2s;
        color: #0284c7;
        background: #f8fafc;
    }
    .otp-inputs input:focus {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 0 0 3px rgba(2,132,199,0.15);
        transform: scale(1.05);
    }
    .otp-inputs input.is-invalid {
        border-color: #dc3545;
        background: #fff5f5;
    }
    .btn-verify {
        width: 100%;
        padding: 0.9rem;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border: none;
        border-radius: 2rem;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 0.5rem;
    }
    .btn-verify:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(2,132,199,0.35); }
    .resend-section {
        text-align: center;
        margin-top: 1.5rem;
        font-size: 0.875rem;
        color: #64748b;
    }
    .resend-section form { display: inline; }
    .resend-section button {
        background: none;
        border: none;
        color: #0284c7;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.875rem;
        padding: 0;
        text-decoration: underline;
    }
    .resend-section button:hover { color: #0369a1; }
    .timer { font-weight: 600; color: #f97316; }
    .otp-footer {
        background: #f8fafc;
        padding: 1rem 2rem;
        text-align: center;
        border-top: 1px solid #e2e8f0;
        font-size: 0.8rem;
        color: #94a3b8;
    }
    .alert-success {
        background: #f0fdf4;
        border: 1px solid #86efac;
        color: #166534;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .alert-error {
        background: #fff5f5;
        border: 1px solid #fca5a5;
        color: #991b1b;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
</style>

<div class="otp-wrapper">
    <div class="otp-card">
        <div class="otp-header">
            <div class="icon-wrap">✉️</div>
            <h1>Verifikasi OTP</h1>
            <p>Masukkan kode 6 digit yang kami kirimkan</p>
        </div>

        <div class="otp-body">
            @if(session('success'))
                <div class="alert-success">✅ {{ session('success') }}</div>
            @endif

            @if($errors->has('otp'))
                <div class="alert-error">❌ {{ $errors->first('otp') }}</div>
            @endif

            <div class="otp-email-hint">
                Kode dikirim ke email:<br>
                <strong>{{ Str::mask(auth()->user()->email, '*', 3, strlen(auth()->user()->email) - 7) }}</strong>
            </div>

            <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                @csrf
                {{-- Hidden input yang akan menerima nilai gabungan 6 digit --}}
                <input type="hidden" name="otp" id="otpHidden">

                <div class="otp-inputs">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                    <input type="text" maxlength="1" class="otp-digit @error('otp') is-invalid @enderror" inputmode="numeric" pattern="[0-9]" autocomplete="off">
                </div>

                <button type="submit" class="btn-verify" id="submitBtn" disabled>
                    ✅ Verifikasi Sekarang
                </button>
            </form>

            <div class="resend-section">
                Tidak menerima kode?
                <form method="POST" action="{{ route('otp.resend') }}">
                    @csrf
                    <button type="submit">Kirim Ulang OTP</button>
                </form>
            </div>
        </div>

        <div class="otp-footer">
            Kode berlaku 10 menit &nbsp;|&nbsp;
            <a href="{{ route('logout') }}" style="color:#0284c7;"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>
        </div>
    </div>
</div>

<script>
    const digits = document.querySelectorAll('.otp-digit');
    const hidden  = document.getElementById('otpHidden');
    const submit  = document.getElementById('submitBtn');

    digits.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            // Hanya angka
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value && index < digits.length - 1) {
                digits[index + 1].focus();
            }
            updateHidden();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                digits[index - 1].focus();
            }
        });

        // Paste support
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const pasted = e.clipboardData.getData('text').replace(/[^0-9]/g, '');
            pasted.split('').forEach((char, i) => {
                if (digits[i]) digits[i].value = char;
            });
            updateHidden();
            const nextEmpty = [...digits].findIndex(d => !d.value);
            if (nextEmpty !== -1) digits[nextEmpty].focus();
            else digits[digits.length - 1].focus();
        });
    });

    function updateHidden() {
        const code = [...digits].map(d => d.value).join('');
        hidden.value = code;
        submit.disabled = code.length < 6;
    }

    // Auto-focus first input
    digits[0].focus();
</script>
@endsection
