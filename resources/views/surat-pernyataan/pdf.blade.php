<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Pernyataan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .content {
            margin-top: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        .data-row {
            margin-bottom: 10px;
            display: flex;
        }
        .data-label {
            width: 150px;
            font-weight: bold;
        }
        .data-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            padding: 0 5px;
        }
        .list-item {
            margin-left: 20px;
            margin-bottom: 10px;
            text-align: justify;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
        }
        .page-break {
            page-break-after: always;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .stamp-box {
            width: 100px;
            height: 100px;
            border: 1px dashed #999;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin-bottom: 20px;">PEMERINTAH KABUPATEN BANGKALAN</h2>
        <h2 style="margin-bottom: 20px;">DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU PINTU</h2>
        <h3 style="text-decoration: underline;">SURAT PERNYATAAN</h3>
    </div>

    <div class="content">
        <div class="section">
            <p style="text-align: center; margin-bottom: 20px;">
                <strong>Yang bertanda tangan di bawah ini:</strong>
            </p>

            <div class="data-row">
                <div class="data-label">Nama</div>
                <div class="data-value">{{ $suratPernyataan->nama_pemohon }}</div>
            </div>

            <div class="data-row">
                <div class="data-label">Pekerjaan</div>
                <div class="data-value">{{ $suratPernyataan->pekerjaan }}</div>
            </div>

            <div class="data-row">
                <div class="data-label">Alamat</div>
                <div class="data-value">{{ $suratPernyataan->alamat_pemohon }}</div>
            </div>

            <div class="data-row">
                <div class="data-label">No KTP</div>
                <div class="data-value">{{ $suratPernyataan->no_ktp }}</div>
            </div>
        </div>

        <div class="section">
            <p style="text-align: justify; margin-bottom: 15px;">
                Sehubungan dengan permohonan izin reklame, saya berjanji:
            </p>

            <div class="list-item">
                1. Bahwa saya sanggup menaati segala peraturan / ketentuan – ketentuan yang di terapkan oleh Pemerintah Kabupaten Bangkalan;
            </div>

            <div class="list-item">
                2. Bahwa sesungguhnya dengan izin penyelenggaraan / pemasangan reklame tersebut, akan saya pergunakan sendiri dan semata-mata untuk kepentingan reklame sesuai dengan ketentuan yang berlaku;
            </div>

            <div class="list-item">
                3. Bahwa saya tidak akan mengubah konstruksi dan atau memindah tempat / lokasi yang ditentukan tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu;
            </div>

            <div class="list-item">
                4. Bahwa saya tidak akan memindah tangankan surat izin pemasangan reklame kepada pihak lain tanpa seizin dari Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu;
            </div>

            <div class="list-item">
                5. Bahwa saya bertanggung jawab sepenuhnya atas konstruksi reklame, kebersihan, ketertiban dan keindahan reklame, serta pemeliharaan reklame yang di pasang;
            </div>

            <div class="list-item">
                6. Bahwa saya bertanggung jawab sepenuhnya atas barang pihak lain dan kecelakaan terhadap orang lain yang di akibatkan oleh reklame;
            </div>

            <div class="list-item">
                7. Bahwa saya penyelenggara reklame harus membongkar dan menurunkan reklame selambat-lambatnya 7 (tujuh) hari setelah masa berlakunya berakhir dan tidak diperpanjang;
            </div>

            <div class="list-item">
                8. Bahwa apabila saya kemudian hari ternyata tidak mematuhi janji tersebut di atas baik seluruhnya maupun sebagian dalam pernyataan ini, maka saya bersedia reklame tersebut di bongkar atau dikenakan sanksi lain sesuai dengan ketentuan yang berlaku.
            </div>
        </div>

        <div class="section">
            <p style="text-align: justify;">
                Demikian surat pernyataan ini saya buat dengan sebenar-benarnya dengan penuh rasa tanggung jawab dan tanpa paksaan dari pihak manapun.
            </p>
        </div>

        <div class="signature-section" style="margin-top: 50px;">
            <div style="width: 45%;">
                <p style="text-align: center;">Materai<br>Rp. 10.000</p>
                <div class="stamp-box"></div>
            </div>
            <div style="width: 45%; text-align: center;">
                <p>Bangkalan, {{ $suratPernyataan->tanggal_pernyataan?->format('d F Y') ?? date('d F Y') }}</p>
                <p style="margin-top: 20px;">Yang membuat pernyataan</p>
                <div style="height: 60px;"></div>
                <p style="border-top: 1px solid #000; margin-top: 10px;">
                    {{ $suratPernyataan->nama_pemohon }}
                </p>
            </div>
        </div>

        <div class="footer">
            <p style="margin-top: 40px;">
                Nomor Registrasi Permohonan: {{ $permohonan->nomor_registrasi }}
            </p>
            <p>
                Dicetak pada: {{ now()->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
</body>
</html>
