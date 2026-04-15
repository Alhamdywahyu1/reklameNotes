@extends('layouts.app')

@section('title', 'Surat Izin Reklame - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="container-fluid">
    <div class="row mb-4 no-print">
        <div class="col-md-8">
            <h2 class="h3 fw-bold">Surat Izin Reklame</h2>
            <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
        </div>
        <div class="col-md-4 text-end">
            <button onclick="printAndTrack()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Surat
            </button>
            <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm print-area">
        <div class="card-body p-5">

            {{-- === WATERMARK === --}}
            <div class="watermark">
                @if(file_exists(public_path('logo_bangkalan.png')))
                    <img src="{{ public_path('logo_bangkalan.png') }}" alt="Watermark">
                @endif
            </div>

            {{-- === KOP SURAT === --}}
            <table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 10px;">
                <tr>
                    <td style="width: 100px; vertical-align: middle; text-align: center;">
                        <img src="{{ public_path('logo_bangkalan.png') }}" alt="Logo" style="width: 80px; height: auto;" onerror="this.style.display='none'">
                    </td>
                    <td style="text-align: center; vertical-align: middle;">
                        <p style="margin: 0; font-size: 13px; font-weight: bold;">PEMERINTAH KABUPATEN BANGKALAN</p>
                        <p style="margin: 0; font-size: 16px; font-weight: bold;">DINAS PENANAMAN MODAL</p>
                        <p style="margin: 0; font-size: 16px; font-weight: bold;">DAN PELAYANAN TERPADU SATU PINTU</p>
                        <p style="margin: 2px 0 0 0; font-size: 10px;">Jl. Kartini No. 4, Kraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119</p>
                        <p style="margin: 0; font-size: 10px;">(031) 3095020 Laman https://dpmptsp.bangkalankab.go.id/</p>
                        <p style="margin: 0; font-size: 10px;">Pos-el: dpmptsp@bangkalankab.go.id</p>
                    </td>
                    <td style="width: 100px;"></td>
                </tr>
            </table>

            {{-- === JUDUL SURAT === --}}
            <div style="text-align: center; margin: 20px 0 5px 0;">
                <p style="margin: 0; font-size: 14px; font-weight: bold; text-decoration: underline; letter-spacing: 1px;">
                    S U R A T &nbsp; I Z I N &nbsp; R E K L A M E
                </p>
                <p style="margin: 5px 0 0 0; font-size: 12px;">
                    Nomor : 500.16.7.4/{{ str_pad($permohonan->id, 4, '0', STR_PAD_LEFT) }}/433.114/{{ \Carbon\Carbon::parse($finalApproval->tanggal_approval ?? now())->format('m') }}/{{ \Carbon\Carbon::parse($finalApproval->tanggal_approval ?? now())->format('Y') }}
                </p>
            </div>

            {{-- === DASAR HUKUM === --}}
            <div style="margin: 20px 0 15px 0; font-size: 12px; line-height: 1.6;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 70px; vertical-align: top; font-weight: bold;">DASAR</td>
                        <td style="width: 15px; vertical-align: top;">:</td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top; padding-right: 5px;">1.</td>
                                    <td>Peraturan Daerah Kabupaten Bangkalan Nomor : 1 Tahun 2024 tentang Pajak Daerah dan Retribusi Daerah;</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding-right: 5px;">2.</td>
                                    <td>Peraturan Bupati Bangkalan Nomor : 56 tahun 2011 tentang Tata Cara Penyelenggaraan Reklame;</td>
                                </tr>
                                <tr>
                                    <td style="vertical-align: top; padding-right: 5px;">3.</td>
                                    <td>Peraturan Bupati Bangkalan Nomor : 44 Tahun 2021 Tentang Pendelegasian Kewenangan Penyelenggaraan Perizinan Berusaha Kepada Kepala Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kabupaten Bangkalan.</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- === MEMBERIKAN IZIN KEPADA === --}}
            <div style="text-align: center; margin: 15px 0; font-size: 12px; font-weight: bold;">
                DENGAN INI MEMBERIKAN IZIN KEPADA :
            </div>

            {{-- === DATA PEMOHON === --}}
            <div style="font-size: 12px; line-height: 1.8; margin: 0 0 5px 30px;">
                <table style="width: 95%;">
                    <tr>
                        <td style="width: 15px; vertical-align: top;">1.</td>
                        <td style="width: 280px; vertical-align: top;">Nama Orang / Badan / Organisasi</td>
                        <td style="width: 15px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; font-weight: bold;">{{ strtoupper($permohonan->nama_pemohon) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">2.</td>
                        <td style="vertical-align: top;">Tempat Tinggal / Alamat</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->alamat_pemohon }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">3.</td>
                        <td style="vertical-align: top;">Pekerjaan</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->pekerjaan ?? '-' }}</td>
                    </tr>
                </table>
            </div>

            <div style="font-size: 12px; margin: 10px 0 5px 0;">
                Untuk menyelenggarakan Reklame :
            </div>

            {{-- === DATA REKLAME === --}}
            <div style="font-size: 12px; line-height: 1.8; margin: 0 0 0 30px;">
                <table style="width: 95%;">
                    <tr>
                        <td style="width: 15px; vertical-align: top;">1.</td>
                        <td style="width: 280px; vertical-align: top;">Jenis / Klasifikasi Reklame</td>
                        <td style="width: 15px; vertical-align: top;">:</td>
                        <td style="vertical-align: top; font-weight: bold;">{{ strtoupper($permohonan->nama_reklame ?? 'BILLBOARD') }} / {{ strtoupper($permohonan->jenis_reklame) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">2.</td>
                        <td style="vertical-align: top;">Luas dan Jumlah</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->ukuran_reklame }} / {{ $permohonan->jumlah_reklame }} Buah</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">3.</td>
                        <td style="vertical-align: top;">Narasi</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top; font-weight: bold;">{{ strtoupper($permohonan->narasi_reklame) }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">4.</td>
                        <td style="vertical-align: top;">Lokasi Pemancangan Reklame</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->lokasi_pemasangan }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"></td>
                        <td style="vertical-align: top;">Klasifikasi Lokasi</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->klasifikasi_lokasi ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"></td>
                        <td style="vertical-align: top;">Reklame untuk Keperluan</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->keperluan_reklame ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"></td>
                        <td style="vertical-align: top;">Reklame memakai berapa warna</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="vertical-align: top;">{{ $permohonan->jumlah_warna ? $permohonan->jumlah_warna . ' Warna' : 'Multi Warna' }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">5.</td>
                        <td colspan="3" style="vertical-align: top;">
                            Syarat &ndash; syarat :
                            <div style="margin-left: 15px; margin-top: 3px;">
                                <table>
                                    <tr>
                                        <td style="vertical-align: top; padding-right: 5px;">a.</td>
                                        <td>Tidak boleh mengganggu pemandangan umum / Lalu lintas (Kendaraan, Pejalan Kaki), norma keagamaan, kesusilaan, keamanan dan kesehatan.</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding-right: 5px;">b.</td>
                                        <td>Tidak boleh merubah, menambah sebagian reklame.</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; padding-right: 5px;">c.</td>
                                        <td>Surat keputusan ini dicabut apabila yang bersangkutan tidak memenuhi ketentuan peraturan Perundang-undangan yang berlaku.</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- === MASA BERLAKU === --}}
            <div style="font-size: 12px; line-height: 1.8; margin: 10px 0 0 30px;">
                <table style="width: 95%;">
                    <tr>
                        <td style="width: 15px; vertical-align: top;">6.</td>
                        <td style="width: 280px; vertical-align: top;">Masa berlakunya izin Reklame</td>
                        <td style="width: 15px; vertical-align: top;">:</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left: 20px;">Dari Tanggal</td>
                        <td>:</td>
                        <td style="font-weight: bold;">{{ $permohonan->tanggal_berlaku ? \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td style="padding-left: 20px;">Sampai Tanggal</td>
                        <td>:</td>
                        <td style="font-weight: bold;">{{ $permohonan->tanggal_berakhir ? \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </div>

            {{-- === TTD & PENGESAHAN === --}}
            <div style="margin-top: 25px; font-size: 12px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%;"></td>
                        <td style="text-align: center;">
                            <p style="margin: 0;">Ditetapkan di : Bangkalan</p>
                            <p style="margin: 0;">Pada Tanggal &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($finalApproval->tanggal_approval ?? now())->translatedFormat('d F Y') }}</p>
                            <br>
                            <p style="margin: 0; font-weight: bold; font-size: 11px;">KEPALA DINAS PENANAMAN MODAL</p>
                            <p style="margin: 0; font-weight: bold; font-size: 11px;">DAN PELAYANAN TERPADU SATU PINTU</p>
                            <p style="margin: 0; font-weight: bold; font-size: 11px;">KABUPATEN BANGKALAN</p>
                            <div style="height: 80px;"></div>
                            <p style="margin: 0; font-weight: bold; text-decoration: underline;">
                                RIZAL MORRIS, A.P., M.Si.
                            </p>
                            <p style="margin: 0; font-size: 11px;">Pembina Utama Muda</p>
                            <p style="margin: 0; font-size: 11px;">NIP. 19740924 199311 1 002</p>
                        </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>

    <div class="alert alert-success mt-4 no-print">
        <i class="bi bi-check-circle"></i>
        <strong>Status Permohonan:</strong> DISETUJUI - Silakan cetak surat ini sebagai Surat Izin Reklame resmi.
    </div>
</div>

<style media="print">
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
    }
    
    .print-area {
        box-shadow: none !important;
        border: none !important;
    }
    
    .navbar, .sidebar, .alert, .btn {
        display: none !important;
    }
    
    @page {
        size: A4;
        margin: 1.5cm 2cm;
    }
    
    .card-body {
        padding: 0 !important;
    }
</style>

<style>
    .print-area {
        max-width: 900px;
        margin: 0 auto;
        font-family: 'Times New Roman', Times, serif;
        position: relative;
    }
    
    .watermark {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.08;
        z-index: -1;
        width: 400px;
        height: 400px;
    }
    
    .watermark img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    @media print {
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', Times, serif;
        }
        
        .container-fluid {
            max-width: 100%;
            padding: 0;
        }
    }
</style>

<script>
    function printAndTrack() {
        window.print();
        
        setTimeout(() => {
            fetch('{{ route("print.track-surat", $permohonan) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show mt-4 no-print';
                alertDiv.role = 'alert';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle"></i>
                    <strong>Berhasil!</strong> ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.alert-success').parentNode.insertBefore(alertDiv, document.querySelector('.alert-success'));
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }, 1000);
    }
</script>
@endsection
