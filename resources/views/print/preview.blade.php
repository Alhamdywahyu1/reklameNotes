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
            <div class="card-body p-5" style="border: 1px solid #ddd; background-color: #fff;">
                <!-- Kop Surat -->
                <div style="border-bottom: 3px solid #1a5490; padding-bottom: 20px; margin-bottom: 30px;">
                    <div class="text-center">
                        <h2 style="margin: 0; color: #1a5490;">PEMERINTAH KABUPATEN BANGKALAN</h2>
                        <h3 style="margin: 5px 0 0 0; color: #1a5490; font-size: 18px;">
                            DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU
                        </h3>
                        <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
                            Jl. Kartini No.4, Rw. 03, Keraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119, Indonesia., Telp. (031) 3095020
                        </p>
                    </div>
                </div>

                <!-- Judul Dokumen -->
                <div style="text-align: center; margin-bottom: 30px;">
                    <h4 style="color: #1a5490; text-transform: uppercase; letter-spacing: 1px;">
                        Surat Persetujuan Pendaftaran Reklame
                    </h4>
                    <p style="font-size: 12px; color: #666; margin: 10px 0 0 0;">
                        Nomor: {{ $permohonan->nomor_registrasi }}
                    </p>
                </div>

                <!-- Isi Surat -->
                <div style="margin-bottom: 30px;">
                    <p style="text-align: justify; line-height: 1.6;">
                        Berdasarkan permohonan yang telah diterima dan setelah dilakukan pemeriksaan/verifikasi dokumen,
                        maka dengan ini diberikan persetujuan Pendaftaran Reklame dengan rincian sebagai berikut:
                    </p>

                    <div style="margin: 20px 0; background-color: #f8f9fa; padding: 15px;">
                        <table style="width: 100%; font-size: 13px; line-height: 1.8;">
                            <tr>
                                <td style="width: 35%; vertical-align: top;">Nama Pemohon</td>
                                <td style="vertical-align: top;">: {{ $permohonan->nama_pemohon }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">NIK</td>
                                <td style="vertical-align: top;">: {{ $permohonan->nik }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Alamat</td>
                                <td style="vertical-align: top;">: {{ $permohonan->alamat_pemohon }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Jenis Reklame</td>
                                <td style="vertical-align: top;">: {{ $permohonan->jenis_reklame }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Ukuran</td>
                                <td style="vertical-align: top;">: {{ $permohonan->ukuran_reklame }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top;">Lokasi</td>
                                <td style="vertical-align: top;">: {{ $permohonan->lokasi_pemasangan }}</td>
                            </tr>
                        </table>
                    </div>

                    <p style="text-align: justify; line-height: 1.6;">
                        Permohonan tersebut telah memenuhi semua persyaratan yang telah ditentukan dan dinyatakan 
                        <strong>DISETUJUI</strong> untuk dapat melanjutkan proses pendaftaran reklame.
                    </p>
                </div>

                <!-- Daftar Approval -->
                <h5 style="margin: 30px 0 15px 0; color: #1a5490;">Verifikasi Approval:</h5>
                <div style="background-color: #f8f9fa; padding: 15px; font-size: 12px;">
                    @foreach ($approvals as $approval)
                        <div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">
                            <strong>{{ $approval->role->name }}</strong><br>
                            Nama: {{ $approval->user->name }}<br>
                            Keputusan: <strong style="color: @if($approval->keputusan === 'Disetujui') #198754 @else #dc3545 @endif;">
                                {{ $approval->keputusan }}
                            </strong><br>
                            Tanggal: {{ $approval->tanggal_approval->format('d M Y H:i') }}
                        </div>
                    @endforeach
                </div>

                <!-- Tanda Tangan -->
                <div style="margin-top: 40px;">
                    <p style="text-align: center; font-size: 12px; color: #666; margin: 0;">
                        Dicetak pada: {{ now()->format('d M Y H:i') }}
                    </p>
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
                    <li>Pastikan tanda tangan digital sudah tercetak</li>
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
@endsection
