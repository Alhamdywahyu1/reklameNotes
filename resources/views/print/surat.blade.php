@extends('layouts.app')

@section('title', 'Surat Persetujuan - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="h3 fw-bold">Surat Persetujuan Pemasangan Reklame</h2>
            <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
        </div>
        <div class="col-md-4 text-end">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Surat
            </button>
            <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm print-area">
        <div class="card-body p-5">
            <!-- Header -->
            <div class="text-center mb-5 pb-4 border-bottom">
                <h3 class="fw-bold mb-2">PEMERINTAH KABUPATEN BANGKALAN</h3>
                <h3 class="fw-bold mb-3">DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h3>
                <p class="small text-muted mb-0">Jl. Kartini No.4, Rw. 03, Keraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119, Indonesia.</p>
                <p class="small text-muted">Telp. (031) 3095020</p>
            </div>

            <!-- Nomor dan Tanggal Surat -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <p><strong>Nomor Surat:</strong> {{ $permohonan->nomor_registrasi }}/DPMPTSP/{{ date('Y') }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($finalApproval->tanggal_approval)->format('d F Y') }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Perihal:</strong> <u>Surat Persetujuan Pemasangan Reklame</u></p>
                </div>
            </div>

            <!-- Kepada -->
            <div class="mb-4">
                <p><strong>Kepada Yth.:</strong></p>
                <p style="margin-left: 2rem;">
                    <strong>Nama:</strong> {{ $permohonan->nama_pemohon }}<br>
                    <strong>NIK:</strong> {{ $permohonan->nik }}<br>
                    <strong>Alamat:</strong> {{ $permohonan->alamat_pemohon }}<br>
                    <strong>No. Telepon:</strong> {{ $permohonan->nomor_telepon }}
                </p>
            </div>

            <!-- Isi Surat -->
            <div class="mb-5">
                <p>Dengan ini kami menyatakan bahwa permohonan pemasangan reklame Anda telah disetujui dengan ketentuan sebagai berikut:</p>

                <div style="margin-left: 2rem; margin-right: 2rem;">
                    <table class="table table-borderless">
                        <tr>
                            <td width="35%"><strong>Jenis Reklame</strong></td>
                            <td>: {{ $permohonan->jenis_reklame }}</td>
                        </tr>
                        <tr>
                            <td><strong>Ukuran</strong></td>
                            <td>: {{ $permohonan->ukuran_reklame }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jumlah</strong></td>
                            <td>: {{ $permohonan->jumlah_reklame }} Unit</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi Pemasangan</strong></td>
                            <td>: {{ $permohonan->lokasi_pemasangan }}</td>
                        </tr>
                        <tr>
                            <td><strong>Narasi Reklame</strong></td>
                            <td>: {{ $permohonan->narasi_reklame }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                        <tr>
                            <td><strong>Berlaku Mulai</strong></td>
                            <td>: {{ $permohonan->tanggal_berlaku ? \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Berlaku Sampai</strong></td>
                            <td>: {{ $permohonan->tanggal_berakhir ? \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status Kedaluwarsa</strong></td>
                            <td>: 
                                @php $status = $permohonan->getStatusKedaluarsa(); @endphp
                                <strong>
                                    @if($status === 'Aktif')
                                        <span style="color: #198754;">✓ AKTIF</span>
                                    @elseif($status === 'Kedaluwarsa')
                                        <span style="color: #dc3545;">✗ KEDALUWARSA</span>
                                    @else
                                        <span style="color: #666;">DICABUT</span>
                                    @endif
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="mt-4">Dengan diberikannya persetujuan ini, Anda diwajibkan untuk:</p>
                <ol style="margin-left: 2rem;">
                    <li>Melakukan pemasangan reklame sesuai dengan spesifikasi yang telah disetujui</li>
                    <li>Memelihara reklame agar tetap dalam kondisi baik dan rapi</li>
                    <li>Mematuhi semua peraturan perundang-undangan yang berlaku</li>
                    <li>Melapor ke Dinas PMPTSP jika ada perubahan atau pembongkaran reklame</li>
                    <li>Membayar retribusi reklame sesuai dengan ketentuan yang berlaku</li>
                </ol>

                <p class="mt-4">Surat persetujuan ini berlaku selama pemasangan reklame sesuai dengan permohonan Anda dan dapat dicabut kembali apabila ditemukan pelanggaran.</p>
            </div>

            <!-- Tanda Tangan -->
            <div class="row mt-5">
                <div class="col-md-8"></div>
                <div class="col-md-4 text-center">
                    <p><strong>Kepala Bidang,</strong></p>
                    <div style="height: 80px;"></div>
                    <p style="border-top: 1px solid #000; padding-top: 5px;">
                        <u>{{ $finalApproval->user->name ?? 'Nama Pejabat' }}</u><br>
                        <small>NIP. -</small>
                    </p>
                </div>
            </div>

            <!-- Catatan -->
            <div class="alert alert-info mt-5 no-print">
                <strong><i class="bi bi-info-circle"></i> Catatan untuk Pemohon:</strong>
                <ul class="mb-0 mt-2">
                    <li>Surat ini sudah dalam status APPROVED dan siap dicetak</li>
                    <li>Harap dibawa ke kantor DPMPTSP untuk verifikasi final dan pengambilan dokumen asli</li>
                    <li>Jangan lupa membawa identitas diri saat ke kantor</li>
                    <li>Jam operasional kantor: Senin - Jumat, 08:00 - 16:00 WIB</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="alert alert-success mt-4">
        <i class="bi bi-check-circle"></i>
        <strong>Status Permohonan:</strong> DISETUJUI - Silakan cetak surat ini dan bawa ke kantor DPMPTSP untuk proses selanjutnya.
    </div>
</div>

<style media="print">
    .no-print {
        display: none;
    }
    
    body {
        background: white;
    }
    
    .print-area {
        box-shadow: none !important;
        border: none !important;
    }
    
    .navbar, .sidebar, .alert {
        display: none;
    }
    
    @page {
        margin: 0.5cm;
    }
</style>

<style>
    .print-area {
        max-width: 900px;
        margin: 0 auto;
    }
    
    @media print {
        body {
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            max-width: 100%;
        }
    }
</style>
@endsection
