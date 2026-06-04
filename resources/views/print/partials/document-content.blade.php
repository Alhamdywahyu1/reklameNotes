{{-- Document Content - Surat IZIN Reklame (Konten Inti)
    Dipakai oleh document.blade.php (master template) untuk semua mode rendering
    Usage: @include('print.partials.document-content')
--}}

{{-- WATERMARK LOGO --}}
<div class="watermark">
    @if(file_exists(public_path('logo_bangkalan.png')))
        <img src="{{ $isPdf ? public_path('logo_bangkalan.png') : asset('logo_bangkalan.png') }}" alt="Watermark">
    @endif
</div>

{{-- KOP SURAT --}}
@include('print.partials.header-printout')

{{-- JUDUL SURAT --}}
<div class="print-title">
    <p class="title-text">SURAT IZIN REKLAME</p>
    <p class="title-number">
        <span style="display: block; text-align: center;">Nomor : ${nomor_naskah}</span>
    </p>
</div>

{{-- DASAR HUKUM --}}
<div class="section-text" style="margin: 10px 0 8px 0;">
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
<div class="center-strong">
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

<p style="margin: 6px 0 4px 0;">Untuk menyelenggarakan Reklame :</p>

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
<table class="data-table" style="margin-top: 6px;">
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

{{-- TANDA TANGAN (3 SPASI) --}}
<div class="ttd-block">
    <table class="ttd-section" style="width: 100%;">
        <tr>
            <td style="width: 50%;"></td>
            <td class="ttd-right">
                <p style="margin: 0;">Ditetapkan di : Bangkalan</p>
                <p style="margin: 0;">Pada Tanggal : ${tanggal_naskah}</p>
                <br>
                <p class="jabatan" style="margin: 0;">KEPALA DINAS PENANAMAN MODAL</p>
                <p class="jabatan" style="margin: 0;">DAN PELAYANAN TERPADU SATU PINTU</p>
                <p class="jabatan" style="margin: 0;">KABUPATEN BANGKALAN</p>
                <div class="ttd-spacer" style="display:flex; align-items:center; justify-content:flex-start; text-align:left; min-height:7em; padding-left:30px;">
                    ${ttd_pengirim}
                </div>
                <p class="nama" style="margin: 0;">RIZAL MORRIS, A.P., M.Si.</p>
                <p style="margin: 0;">Pembina Utama Muda</p>
                <p class="nip" style="margin: 0;">NIP. 19740924 199311 1 002</p>
            </td>
        </tr>
    </table>
</div>
