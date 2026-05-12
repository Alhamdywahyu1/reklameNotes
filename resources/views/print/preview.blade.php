@extends('layouts.app')

@section('title', 'Print Preview - ' . $permohonan->nomor_registrasi)

@section('content')
<div class="header-page d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-printer"></i> Print Preview</h1>
        <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
    </div>
    <a href="{{ route('print.pdf', $permohonan) }}" class="btn btn-success btn-lg" target="_blank">
        <i class="bi bi-file-pdf"></i> Download PDF
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-5" style="border: 1px solid #ddd; background-color: #fff; font-family: 'Times New Roman', Times, serif; position: relative;">

                {{-- WATERMARK --}}
                <div class="preview-watermark">
                    @if(file_exists(public_path('logo_bangkalan.png')))
                        <img src="{{ asset('logo_bangkalan.png') }}" alt="Watermark">
                    @endif
                </div>

                {{-- KOP SURAT --}}
                <table style="width: 100%; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 8px;">
                    <tr>
                        <td style="width: 90px; vertical-align: middle; text-align: center;">
                            <img src="{{ asset('logo_bangkalan.png') }}" alt="Logo" style="width: 70px; height: auto;" onerror="this.style.display='none'">
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <p style="margin: 0; font-size: 13px; font-weight: bold;">PEMERINTAH KABUPATEN BANGKALAN</p>
                            <p style="margin: 0; font-size: 15px; font-weight: bold;">DINAS PENANAMAN MODAL</p>
                            <p style="margin: 0; font-size: 15px; font-weight: bold;">DAN PELAYANAN TERPADU SATU PINTU</p>
                            <p style="margin: 2px 0 0 0; font-size: 10px;">Jl. Kartini No. 4, Kraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119</p>
                            <p style="margin: 0; font-size: 10px;">(031) 3095020 Laman https://dpmptsp.bangkalankab.go.id/</p>
                            <p style="margin: 0; font-size: 10px;">Pos-el: dpmptsp@bangkalankab.go.id</p>
                        </td>
                        <td style="width: 90px;"></td>
                    </tr>
                </table>

                {{-- JUDUL --}}
                <div style="text-align: center; margin: 18px 0 5px 0;">
                    <p style="margin: 0; font-size: 14px; font-weight: bold; text-decoration: underline; letter-spacing: 1px;">
                        S U R A T &nbsp; I Z I N &nbsp; R E K L A M E
                    </p>
                    <p style="margin: 5px 0 0 0; font-size: 12px;">
                        Nomor : 500.16.7.4/{{ str_pad($permohonan->id, 4, '0', STR_PAD_LEFT) }}/433.114/{{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->format('m') }}/{{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->format('Y') }}
                    </p>
                </div>

                {{-- DASAR HUKUM --}}
                <div style="margin: 15px 0 10px 0; font-size: 12px; line-height: 1.6;">
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

                <div style="text-align: center; margin: 12px 0; font-size: 12px; font-weight: bold;">
                    DENGAN INI MEMBERIKAN IZIN KEPADA :
                </div>

                {{-- DATA PEMOHON --}}
                <div style="font-size: 12px; line-height: 1.8; margin: 0 0 5px 25px;">
                    <table style="width: 95%;">
                        <tr>
                            <td style="width: 18px; vertical-align: top;">1.</td>
                            <td style="width: 250px; vertical-align: top;">Nama Orang / Badan / Organisasi</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
                            <td style="vertical-align: top; font-weight: bold;">{{ strtoupper($permohonan->nama_pemohon) }}</td>
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
                </div>

                <p style="margin: 8px 0 5px 0; font-size: 12px;">Untuk menyelenggarakan Reklame :</p>

                {{-- DATA REKLAME --}}
                <div style="font-size: 12px; line-height: 1.8; margin: 0 0 0 25px;">
                    <table style="width: 95%;">
                        <tr>
                            <td style="width: 18px; vertical-align: top;">1.</td>
                            <td style="width: 250px; vertical-align: top;">Jenis / Klasifikasi Reklame</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
                            <td style="vertical-align: top; font-weight: bold;">{{ strtoupper($permohonan->nama_reklame ?? 'BILLBOARD') }} / {{ strtoupper($permohonan->jenis_reklame) }}</td>
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

                {{-- MASA BERLAKU --}}
                <div style="font-size: 12px; line-height: 1.8; margin: 8px 0 0 25px;">
                    <table style="width: 95%;">
                        <tr>
                            <td style="width: 18px; vertical-align: top;">6.</td>
                            <td style="width: 250px; vertical-align: top;">Masa berlakunya izin Reklame</td>
                            <td style="width: 15px; vertical-align: top;">:</td>
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
                </div>

                {{-- TTD --}}
                <div style="margin-top: 20px; font-size: 12px;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%;"></td>
                            <td style="text-align: center;">
                                <p style="margin: 0;">Ditetapkan di : Bangkalan</p>
                                <p style="margin: 0;">Pada Tanggal &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($approvals->first()->tanggal_approval ?? now())->translatedFormat('d F Y') }}</p>
                                <br>
                                <p style="margin: 0; font-weight: bold; font-size: 11px;">KEPALA DINAS PENANAMAN MODAL</p>
                                <p style="margin: 0; font-weight: bold; font-size: 11px;">DAN PELAYANAN TERPADU SATU PINTU</p>
                                <p style="margin: 0; font-weight: bold; font-size: 11px;">KABUPATEN BANGKALAN</p>
                                <div style="height: 80px;"></div>
                                <p style="margin: 0; font-weight: bold; text-decoration: underline;">RIZAL MORRIS, A.P., M.Si.</p>
                                <p style="margin: 0; font-size: 11px;">Pembina Utama Muda</p>
                                <p style="margin: 0; font-size: 11px;">NIP. 19740924 199311 1 002</p>
                            </td>
                        </tr>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Panduan Print</h5>
            </div>
            <div class="card-body small">
                <ol class="small">
                    <li>Klik tombol "Download PDF" di atas</li>
                    <li>Dokumen akan diunduh dalam format PDF</li>
                    <li>Cetak dokumen pada kertas A4 putih berkualitas</li>
                    <li>Pastikan tanda tangan sudah tercetak</li>
                    <li>Arsipkan dokumen dengan baik</li>
                </ol>
                <hr>
                <p class="text-muted mb-0">
                    <strong>Catatan:</strong> Dokumen ini hanya dapat dicetak setelah mendapat persetujuan final dari Kepala Bidang.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .preview-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.08;
        z-index: 0;
        width: 400px;
        height: 400px;
        pointer-events: none;
    }
    
    .preview-watermark img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>

@endsection
