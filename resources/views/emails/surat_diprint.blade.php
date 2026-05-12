@component('mail::message')
# Surat Persetujuan Reklame Anda Siap

Halo {{ $permohonan->nama_pemohon }},

Kami dengan gembira memberi tahu bahwa surat persetujuan reklame Anda telah disiapkan oleh **{{ $operatorName }}**.

## Detail Permohonan

- **Nomor Registrasi**: {{ $permohonan->nomor_registrasi }}
- **Jenis Reklame**: {{ $permohonan->jenis_reklame }}
- **Ukuran**: {{ $permohonan->ukuran_reklame }}
- **Jumlah**: {{ $permohonan->jumlah_reklame }} Unit
- **Lokasi**: {{ $permohonan->lokasi_pemasangan }}

## Apa Selanjutnya?

Surat persetujuan Anda siap untuk diambil di kantor kami. Silakan hubungi operator untuk pengambilan surat dengan membawa dokumen identitas Anda.

Anda juga dapat melihat informasi lebih lanjut dan riwayat permohonan Anda melalui portal kami.

---

Dengan hormat,  
**Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu**  
Kabupaten Bangkalan

Jl. Kartini No.4, Rw. 03, Keraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119  
Telp. (031) 3095020

@endcomponent
