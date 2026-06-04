@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Hasil Pencarian Permohonan</h2>
    <form method="GET" action="{{ route('search') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Cari nomor registrasi, NIK, atau nama pemohon" value="{{ $query }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>
    </form>
    @if(blank($query))
        <div class="alert alert-info">Masukkan kata kunci untuk mulai mencari permohonan.</div>
    @elseif($permohonan->count())
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nomor Registrasi</th>
                        <th>Nama Pemohon</th>
                        <th>NIK</th>
                        <th>Status</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permohonan as $item)
                        <tr>
                            <td>{{ $item->nomor_registrasi }}</td>
                            <td>{{ $item->nama_pemohon }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('permohonan.show', $item) }}" class="btn btn-info btn-sm">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-center">
            {{ $permohonan->links() }}
        </div>
    @else
        <div class="alert alert-warning">Tidak ada data ditemukan untuk kata kunci <strong>{{ $query }}</strong>.</div>
    @endif
</div>
@endsection
