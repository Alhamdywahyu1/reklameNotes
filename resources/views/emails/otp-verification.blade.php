<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f6f9; margin: 0; padding: 0; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); padding: 40px 30px; text-align: center; }
        .header img { height: 60px; margin-bottom: 15px; }
        .header h1 { color: white; margin: 0; font-size: 22px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 14px; }
        .body { padding: 40px 30px; }
        .greeting { font-size: 16px; color: #334155; margin-bottom: 20px; }
        .otp-box { text-align: center; margin: 30px 0; }
        .otp-code { display: inline-block; letter-spacing: 12px; font-size: 48px; font-weight: 800; color: #0284c7; background: #f0f9ff; border: 2px dashed #0284c7; border-radius: 12px; padding: 20px 30px; font-family: 'Courier New', monospace; }
        .info { background: #f8fafc; border-left: 4px solid #0284c7; border-radius: 4px; padding: 15px 20px; margin: 25px 0; font-size: 14px; color: #475569; }
        .info strong { color: #0284c7; }
        .warning { background: #fff7ed; border-left: 4px solid #f97316; border-radius: 4px; padding: 12px 20px; font-size: 13px; color: #9a3412; margin-top: 15px; }
        .footer { background: #f8fafc; padding: 25px 30px; text-align: center; border-top: 1px solid #e2e8f0; }
        .footer p { color: #94a3b8; font-size: 13px; margin: 5px 0; }
        .footer a { color: #0284c7; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>DPMPTSP Kota Bitung</h1>
            <p>Sistem Pendaftaran Izin Reklame</p>
        </div>

        <div class="body">
            <p class="greeting">Halo, <strong>{{ $userName }}</strong>! 👋</p>
            <p style="color: #475569; font-size: 15px;">
                Terima kasih telah mendaftar. Masukkan kode OTP berikut untuk memverifikasi akun kamu:
            </p>

            <div class="otp-box">
                <div class="otp-code">{{ $otpCode }}</div>
            </div>

            <div class="info">
                ⏱️ Kode berlaku selama <strong>10 menit</strong> sejak email ini dikirim.<br>
                Jangan berikan kode ini kepada siapapun.
            </div>

            <div class="warning">
                ⚠️ Jika kamu tidak mendaftar di aplikasi ini, abaikan email ini.
            </div>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem DPMPTSP Kota Bitung.</p>
            <p>Jangan membalas email ini. Hubungi <a href="mailto:admin@dpmptsp-bitung.go.id">admin@dpmptsp-bitung.go.id</a> untuk bantuan.</p>
        </div>
    </div>
</body>
</html>
