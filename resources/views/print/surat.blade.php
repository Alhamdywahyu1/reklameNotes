@extends('layouts.app')

@section('title', 'Surat Izin Reklame - ' . $permohonan->nomor_registrasi)

@push('styles')
@include('print.partials.document-styles')
<style>
    .print-area {
        max-width: 980px;
        margin: 0 auto;
    }

    .print-document {
        min-height: 100%;
    }

    .watermark {
        z-index: 0;
    }
</style>
@endpush

@section('content')
@php $isPdf = false; @endphp
<div class="container-fluid">

    {{-- Banner sudah dicetak (muncul di atas jika status Sudah Terbit) --}}
    @if(isset($sudahTerbit) && $sudahTerbit)
    <div class="alert alert-warning d-flex align-items-center gap-2 mb-3 no-print" id="banner-sudah-terbit" role="alert">
        <i class="bi bi-printer-fill fs-5"></i>
        <div>
            <strong>Surat ini telah dicetak sebelumnya.</strong>
            Halaman ini hanya untuk evaluasi atau cetak ulang.
            @if($permohonan->tanggal_terbit)
                &mdash; Terbit pada: <strong>{{ \Carbon\Carbon::parse($permohonan->tanggal_terbit)->translatedFormat('d F Y, H:i') }}</strong>
            @endif
        </div>
    </div>
    @endif

    <div class="row mb-4 no-print">
        <div class="col-md-8">
            <h2 class="h3 fw-bold">Surat Izin Reklame</h2>
            <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                <button onclick="printAndTrack()" class="btn btn-primary">
                    <i class="bi bi-printer"></i>
                    {{ isset($sudahTerbit) && $sudahTerbit ? 'Cetak Ulang' : 'Cetak Surat' }}
                </button>
                @if(auth()->user() && auth()->user()->role?->name === 'operator' && !(isset($sudahTerbit) && $sudahTerbit))
                    <button onclick="markSudahTerbit()" class="btn btn-success">
                        <i class="bi bi-check2-circle"></i> Sudah Terbit
                    </button>
                @endif
                <a href="{{ route('approval.dashboard') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm print-area">
        <div class="card-body p-5">
            <div class="print-document">
                @include('print.partials.document-content')
            </div>
        </div>
    </div>

    <div class="alert alert-success mt-4 no-print" id="status-alert">
        <i class="bi bi-check-circle"></i>
        <strong>Status Permohonan:</strong>
        @if(isset($sudahTerbit) && $sudahTerbit)
            SUDAH TERBIT - Dokumen resmi telah dicetak.
        @else
            DISETUJUI - Silakan cetak surat ini sebagai Surat Izin Reklame resmi.
        @endif
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
        size: 210mm 330mm;
        margin: 1.5cm 2cm;
    }

    .print-document {
        font-family: Arial, sans-serif !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        color: #000;
    }

    .card-body {
        padding: 0 !important;
    }
</style>

<script>
    async function printAndTrack() {
        try {
            const response = await fetch('{{ route("print.track-surat", $permohonan) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ printed_at: new Date().toISOString() }),
                keepalive: true,
            });

            const data = await response.json();

            // Tampilkan notifikasi di ATAS halaman
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mb-3 no-print';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i>
                <strong>Berhasil!</strong> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            // Sisipkan di paling atas container (sebelum semua elemen)
            const container = document.querySelector('.container-fluid');
            container.insertBefore(alertDiv, container.firstChild);

            window.print();
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal mencatat status cetak. Silakan coba lagi.');
        }
    }

    function markSudahTerbit() {
        if (!confirm('Tandai permohonan ini sebagai Sudah Terbit?')) return;

        fetch('{{ route("print.track-surat", $permohonan) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ manual: true })
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
            const btn = document.querySelector('button[onclick="markSudahTerbit()"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-check2-circle"></i> Sudah Terbit';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal menandai sudah terbit. Cek console untuk detail.');
        });
    }
</script>
@endsection
