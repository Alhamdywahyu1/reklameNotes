<?php
// PDF template - Surat IZIN Reklame (format resmi DPMPTSP)
// Usage: Pdf::loadView('print.pdf', $data)
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 1.5cm 2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
        }
        .kop-surat {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .kop-surat td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 80px;
            text-align: center;
        }
        .kop-logo img {
            width: 70px;
            height: auto;
        }
        .kop-text {
            text-align: center;
        }
        .kop-text .line1 {
            font-size: 13px;
            font-weight: bold;
            margin: 0;
        }
        .kop-text .line2 {
            font-size: 15px;
            font-weight: bold;
            margin: 0;
        }
        .kop-text .line3 {
            font-size: 11px;
            margin: 2px 0 0 0;
        }
        .title-surat {
            text-align: center;
            margin: 18px 0 5px 0;
        }
        .title-surat h4 {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
            margin: 0;
        }
        .title-surat p {
            font-size: 12px;
            margin: 5px 0 0 0;
        }
        .dasar-section {
            margin: 15px 0 10px 0;
            font-size: 12px;
            line-height: 1.6;
        }
        .memberikan-IZIN {
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            margin: 12px 0;
        }
        .data-table {
            width: 95%;
            margin-left: 25px;
            font-size: 12px;
            line-height: 1.7;
        }
        .data-table td {
            vertical-align: top;
            padding: 1px 3px;
        }
        .num-col {
            width: 18px;
        }
        .label-col {
            width: 250px;
        }
        .sep-col {
            width: 15px;
        }
        .syarat-table {
            margin-left: 10px;
            margin-top: 3px;
        }
        .syarat-table td {
            vertical-align: top;
            padding: 1px 3px;
        }
        .ttd-section {
            margin-top: 20px;
        }
        .ttd-section td {
            vertical-align: top;
        }
        .ttd-right {
            text-align: center;
            font-size: 12px;
        }
        .ttd-right .jabatan {
            font-weight: bold;
            font-size: 11px;
        }
        .ttd-right .nama {
            font-weight: bold;
            text-decoration: underline;
        }
        .ttd-right .nip {
            font-size: 11px;
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
    </style>
</head>
<body>
    {{-- WATERMARK LOGO --}}
    <div class="watermark">
        @if(file_exists(public_path('logo_bangkalan.png')))
            <img src="{{ public_path('logo_bangkalan.png') }}" alt="Watermark">
        @endif
    </div>

    {{-- KOP SURAT --}}
    <table class="kop-surat">
        <tr>
            <td class="kop-logo">
                @if(file_exists(public_path('logo_bangkalan.png')))
                    <img src="{{ public_path('logo_bangkalan.png') }}" alt="Logo">
                @endif
            </td>
            <td class="kop-text">
                <p class="line1">PEMERINTAH KABUPATEN BANGKALAN</p>
                <p class="line2">DINAS PENANAMAN MODAL</p>
                <p class="line2">DAN PELAYANAN TERPADU SATU PINTU</p>
                <p class="line3">Jl. Kartini No. 4, Kraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119</p>
                <p class="line3">(031) 3095020 Laman https://dpmptsp.bangkalankab.go.id/</p>
                <p class="line3">Pos-el: dpmptsp@bangkalankab.go.id</p>
            </td>
            <td style="width: 80px;"></td>
        </tr>
    </table>

    {{-- JUDUL SURAT --}}
    <div class="title-surat">
        <h4>S U R A T &nbsp; I Z I N &nbsp; R E K L A M E</h4>
        <p>Nomor : 500.16.7.4/{{ str_pad($permohonan->id, 4, '0', STR_PAD_LEFT) }}/433.114/{{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->format('m') }}/{{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->format('Y') }}</p>
    </div>

    {{-- DASAR HUKUM --}}
    <div class="dasar-section">
        <table style="width: 100%;">
            <tr>
                <td style="width: 65px; vertical-align: top; font-weight: bold;">DASAR</td>
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

    {{-- MEMBERIKAN IZIN --}}
    <div class="memberikan-IZIN">
        DENGAN INI MEMBERIKAN IZIN KEPADA :
    </div>

    {{-- DATA PEMOHON --}}
    <table class="data-table">
        <tr>
            <td class="num-col">1.</td>
            <td class="label-col">Nama Orang / Badan / Organisasi</td>
            <td class="sep-col">:</td>
            <td style="font-weight: bold;">{{ strtoupper($permohonan->nama_pemohon) }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Tempat Tinggal / Alamat</td>
            <td>:</td>
            <td>{{ $permohonan->alamat_pemohon }}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Pekerjaan</td>
            <td>:</td>
            <td>{{ $permohonan->pekerjaan ?? '-' }}</td>
        </tr>
    </table>

    <p style="margin: 8px 0 5px 0; font-size: 12px;">Untuk menyelenggarakan Reklame :</p>

    {{-- DATA REKLAME --}}
    <table class="data-table">
        <tr>
            <td class="num-col">1.</td>
            <td class="label-col">Jenis / Klasifikasi Reklame</td>
            <td class="sep-col">:</td>
            <td style="font-weight: bold;">{{ strtoupper($permohonan->nama_reklame ?? 'BILLBOARD') }} / {{ strtoupper($permohonan->jenis_reklame) }}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Luas dan Jumlah</td>
            <td>:</td>
            <td>{{ $permohonan->ukuran_reklame }} / {{ $permohonan->jumlah_reklame }} Buah</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Narasi</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ strtoupper($permohonan->narasi_reklame) }}</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Lokasi Pemancangan Reklame</td>
            <td>:</td>
            <td>{{ $permohonan->lokasi_pemasangan }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Klasifikasi Lokasi</td>
            <td>:</td>
            <td>{{ $permohonan->klasifikasi_lokasi ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Reklame untuk Keperluan</td>
            <td>:</td>
            <td>{{ $permohonan->keperluan_reklame ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td>Reklame memakai berapa warna</td>
            <td>:</td>
            <td>{{ $permohonan->jumlah_warna ? $permohonan->jumlah_warna . ' Warna' : 'Multi Warna' }}</td>
        </tr>
        <tr>
            <td>5.</td>
            <td colspan="3">
                Syarat &ndash; syarat :
                <table class="syarat-table">
                    <tr>
                        <td style="padding-right: 5px;">a.</td>
                        <td>Tidak boleh mengganggu pemandangan umum / Lalu lintas (Kendaraan, Pejalan Kaki), norma keagamaan, kesusilaan, keamanan dan kesehatan.</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 5px;">b.</td>
                        <td>Tidak boleh merubah, menambah sebagian reklame.</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 5px;">c.</td>
                        <td>Surat keputusan ini dicabut apabila yang bersangkutan tidak memenuhi ketentuan peraturan Perundang-undangan yang berlaku.</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- MASA BERLAKU --}}
    <table class="data-table" style="margin-top: 8px;">
        <tr>
            <td class="num-col">6.</td>
            <td class="label-col">Masa berlakunya izin Reklame</td>
            <td class="sep-col">:</td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-left: 15px;">Dari Tanggal</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $permohonan->tanggal_berlaku ? \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="padding-left: 15px;">Sampai Tanggal</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $permohonan->tanggal_berakhir ? \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
    </table>

    {{-- TANDA TANGAN --}}
    <table class="ttd-section" style="width: 100%; margin-top: 20px;">
        <tr>
            <td style="width: 50%;"></td>
            <td class="ttd-right">
                <p style="margin: 0;">Ditetapkan di : Bangkalan</p>
                <p style="margin: 0;">Pada Tanggal &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->translatedFormat('d F Y') }}</p>
                <br>
                <p class="jabatan" style="margin: 0;">KEPALA DINAS PENANAMAN MODAL</p>
                <p class="jabatan" style="margin: 0;">DAN PELAYANAN TERPADU SATU PINTU</p>
                <p class="jabatan" style="margin: 0;">KABUPATEN BANGKALAN</p>
                <div style="height: 80px;"></div>
                <p class="nama" style="margin: 0;">RIZAL MORRIS, A.P., M.Si.</p>
                <p style="margin: 0; font-size: 11px;">Pembina Utama Muda</p>
                <p class="nip" style="margin: 0;">NIP. 19740924 199311 1 002</p>
            </td>
        </tr>
    </table>

</body>
</html>
