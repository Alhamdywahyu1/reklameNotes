@component('mail::message')
# Pengingat Permohonan Reklame Menunggu Verifikasi

Halo,

Kami ingin mengingatkan Anda bahwa ada **permohonan reklame** yang telah menunggu untuk diverifikasi lebih dari 7 hari.

**Detail Permohonan:**
- **Nomor Registrasi:** {{ $permohonan->nomor_registrasi }}
- **Nama Pemohon:** {{ $permohonan->user->name }}
- **Jenis Reklame:** {{ $permohonan->jenis_reklame }}
- **Lokasi:** {{ $permohonan->lokasi }}
- **Status:** {{ $permohonan->status }}
- **Tanggal Pengajuan:** {{ $permohonan->created_at->format('d M Y H:i') }}
- **Hari Menunggu:** {{ now()->diffInDays($permohonan->created_at) }} hari

@component('mail::button', ['url' => route('approval.verify', $permohonan)])
Lihat Detail Permohonan
@endcomponent

Mohon segera lakukan verifikasi agar permohonan dapat diproses lebih lanjut.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent
