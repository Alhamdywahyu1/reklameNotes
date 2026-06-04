@extends('layouts.app')

@section('title', 'Print Preview - ' . $permohonan->nomor_registrasi)

@push('styles')
@include('print.partials.document-styles')
<style>
    .preview-sheet {
        border: 1px solid #ddd;
        background-color: #fff;
    }

    .preview-sheet .print-document {
        z-index: 1;
    }

    .preview-watermark {
        z-index: 0;
    }
</style>
@endpush

@section('content')
@php $isPdf = false; @endphp
<div class="header-page d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="bi bi-printer"></i> Print Preview</h1>
        <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
    </div>
    <div class="d-inline-flex flex-wrap justify-content-end gap-2">
        <a href="{{ route('print.surat', $permohonan) }}" class="btn btn-primary btn-lg">
            <i class="bi bi-printer"></i> Cetak Surat
        </a>
        <a href="{{ route('print.pdf', $permohonan) }}" class="btn btn-success btn-lg" target="_blank">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-5 preview-sheet">
                <div class="print-document">
                    @include('print.partials.document-content')
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
                    <li>Cetak dokumen pada kertas F4 putih berkualitas</li>
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
@endsection
