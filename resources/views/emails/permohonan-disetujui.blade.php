@component('mail::message')
# Selamat! Permohonan Anda Telah Disetujui

Dengan senang hati kami informasikan bahwa permohonan reklame Anda telah **disetujui** oleh Kepala Bidang.

---

## Detail Permohonan

| Item | Keterangan |
|------|-----------|
| **Nomor Registrasi** | {{ $nomor_registrasi }} |
| **Nama Pemohon** | {{ $nama_pemohon }} |
| **Jenis Reklame** | {{ $jenis_reklame }} |
| **Lokasi Pemasangan** | {{ $lokasi_pemasangan }} |
| **Durasi Pemasangan** | {{ $durasi_pemasangan }} |
| **Tanggal Approval** | {{ $tanggal_approval }} |

---

## Langkah Selanjutnya

Anda sekarang dapat mencetak dokumen persetujuan melalui sistem:

@component('mail::button', ['url' => route('print.preview', $permohonan)])
Lihat & Cetak Dokumen
@endcomponent

Jika Anda mengalami kendala, silakan hubungi bagian administrasi kami.

---

Terima kasih,  
**Tim Sistem Pendaftaran Reklame**
@endcomponent
