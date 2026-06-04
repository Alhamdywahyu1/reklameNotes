<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Pengingat Masa Berlaku Reklame</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #1f2937;">
	<h2>Pengingat Masa Berlaku Reklame</h2>

	<p>Halo {{ $permohonan->nama_pemohon }},</p>

	<p>
		Masa berlaku izin reklame Anda akan berakhir dalam
		<strong>{{ $sisaHari }} hari lagi</strong>.
	</p>

	<p><strong>Detail Izin Reklame:</strong></p>
	<ul>
		<li><strong>Nomor Registrasi:</strong> {{ $permohonan->nomor_registrasi }}</li>
		<li><strong>Jenis Reklame:</strong> {{ $permohonan->jenis_reklame }}</li>
		<li><strong>Lokasi Pemasangan:</strong> {{ $permohonan->lokasi_pemasangan }}</li>
		<li><strong>Tanggal Berakhir:</strong> {{ optional($permohonan->tanggal_berakhir)->translatedFormat('d F Y') }}</li>
	</ul>

	<p>
		Silakan segera melakukan perpanjangan agar izin reklame tetap aktif.
	</p>

	<p>Terima kasih,<br>{{ config('app.name') }}</p>
</body>
</html>
