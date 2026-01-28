<?php
// This is the PDF template for printing permohonan
// Usage: Pdf::loadView('print.pdf', $data)
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 3px solid #1a5490;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }
        .header h2 {
            color: #1a5490;
            margin: 0;
            font-size: 18px;
        }
        .header h3 {
            color: #1a5490;
            margin: 5px 0;
            font-size: 16px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
            font-size: 11px;
        }
        .title {
            text-align: center;
            margin: 30px 0;
        }
        .title h4 {
            color: #1a5490;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .content {
            margin-bottom: 20px;
        }
        .content p {
            text-align: justify;
            margin: 10px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8f9fa;
        }
        .info-table tr td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .info-table tr td:first-child {
            width: 35%;
            font-weight: bold;
        }
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .approval-table tr {
            border-bottom: 1px solid #ddd;
        }
        .approval-table tr td {
            padding: 10px;
            font-size: 11px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            color: #666;
            font-size: 11px;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Kop Surat -->
    <div class="header">
        <h2>PEMERINTAH KOTA</h2>
        <h3>DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h3>
        <p>Jl. Jenderal Sudirman No. 1, Telp. (0274) 123456</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="title">
        <h4>Surat Persetujuan Pendaftaran Reklame</h4>
        <p style="color: #666; font-size: 11px;">Nomor: {{ $permohonan->nomor_registrasi }}</p>
    </div>

    <!-- Isi Dokumen -->
    <div class="content">
        <p>
            Berdasarkan permohonan yang telah diterima dan setelah dilakukan pemeriksaan/verifikasi dokumen,
            maka dengan ini diberikan persetujuan Pendaftaran Reklame dengan rincian sebagai berikut:
        </p>

        <table class="info-table">
            <tr>
                <td>Nama Pemohon</td>
                <td>{{ $permohonan->nama_pemohon }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>{{ $permohonan->nik }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>{{ $permohonan->alamat_pemohon }}</td>
            </tr>
            <tr>
                <td>Nomor Telepon</td>
                <td>{{ $permohonan->nomor_telepon }}</td>
            </tr>
            @if ($permohonan->npwp)
            <tr>
                <td>NPWP</td>
                <td>{{ $permohonan->npwp }}</td>
            </tr>
            @endif
            <tr>
                <td>Jenis Reklame</td>
                <td>{{ $permohonan->jenis_reklame }}</td>
            </tr>
            <tr>
                <td>Ukuran Reklame</td>
                <td>{{ $permohonan->ukuran_reklame }}</td>
            </tr>
            <tr>
                <td>Jumlah Reklame</td>
                <td>{{ $permohonan->jumlah_reklame }}</td>
            </tr>
            <tr>
                <td>Lokasi Pemasangan</td>
                <td>{{ $permohonan->lokasi_pemasangan }}</td>
            </tr>
        </table>

        <p>
            Permohonan tersebut telah memenuhi semua persyaratan yang telah ditentukan dan dinyatakan 
            <strong>DISETUJUI</strong> untuk dapat melanjutkan proses pendaftaran reklame.
        </p>

        <h5 style="color: #1a5490; margin-top: 25px;">Daftar Pemeriksaan Dokumen:</h5>
        <table class="approval-table">
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td>No</td>
                <td>Persyaratan</td>
                <td>Status</td>
                <td>Keterangan</td>
            </tr>
            @foreach ($persyaratan as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->jenis_persyaratan }}</td>
                <td>{{ $item->is_lengkap ? 'LENGKAP' : 'TIDAK LENGKAP' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
            </tr>
            @endforeach
        </table>

        <h5 style="color: #1a5490; margin-top: 25px;">Riwayat Approval:</h5>
        <table class="approval-table">
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td>Role</td>
                <td>Nama</td>
                <td>Keputusan</td>
                <td>Tanggal</td>
            </tr>
            @foreach ($approvals as $approval)
            <tr>
                <td>{{ $approval->role->name }}</td>
                <td>{{ $approval->user->name }}</td>
                <td>
                    <strong style="color: {{ $approval->keputusan === 'Disetujui' ? '#198754' : '#dc3545' }};">
                        {{ $approval->keputusan }}
                    </strong>
                </td>
                <td>{{ $approval->tanggal_approval->format('d M Y H:i') }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
        <p style="margin-top: 20px; color: #999;">*** Dokumen ini telah ditandatangani secara digital ***</p>
    </div>
</body>
</html>
