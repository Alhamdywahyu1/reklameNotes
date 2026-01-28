@component('mail::message')
# Permohonan Reklame Ditolak

Kami infokan bahwa permohonan reklame Anda telah **ditolak**. Mohon perhatikan keterangan penolakan berikut:

---

## Detail Permohonan

| Item | Keterangan |
|------|-----------|
| **Nomor Registrasi** | {{ $nomor_registrasi }} |
| **Nama Pemohon** | {{ $nama_pemohon }} |
| **Jenis Reklame** | {{ $jenis_reklame }} |
| **Lokasi Pemasangan** | {{ $lokasi_pemasangan }} |
| **Tanggal Penolakan** | {{ $tanggal_penolakan }} |

---

## Alasan Penolakan

{{ $keterangan }}

---

## Langkah Selanjutnya

Jika Anda ingin mengajukan permohonan kembali, Anda dapat memperbaiki dokumen atau informasi yang kurang dan mengajukan ulang melalui sistem.

Silakan hubungi bagian administrasi jika Anda memiliki pertanyaan atau membutuhkan klarifikasi lebih lanjut.

---

Terima kasih,  
**Tim Sistem Pendaftaran Reklame**
@endcomponent
