{{-- Master Document Template - Surat IZIN Reklame
    Modes: 'pdf' (server PDF), 'browser-print' (surat.blade), 'web-preview' (preview.blade)
    Usage in controller:
        - PDF: Pdf::loadView('print.document', ['permohonan' => $p, 'mode' => 'pdf', ...])
        - Browser Print: view('print.document', ['permohonan' => $p, 'mode' => 'browser-print', ...])
        - Web Preview: view('print.document', ['permohonan' => $p, 'mode' => 'web-preview', ...])
--}}

@php
    $mode = $mode ?? 'pdf';
    $isPdf = $mode === 'pdf';
    $isBrowserPrint = $mode === 'browser-print';
    $isWebPreview = $mode === 'web-preview';
    $isWeb = $isWebPreview || $isBrowserPrint;
@endphp

@if($isWebPreview)
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
    <div class="header-page d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-printer"></i> Print Preview</h1>
            <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
        </div>
        <div class="d-flex gap-2">
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
@elseif($isBrowserPrint)
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
    <div class="container-fluid">
        <div class="row mb-4 no-print">
            <div class="col-md-8">
                <h2 class="h3 fw-bold">Surat Izin Reklame</h2>
                <p class="text-muted">{{ $permohonan->nomor_registrasi }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-inline-flex flex-wrap justify-content-end gap-2">
                    <button onclick="printAndTrack()" class="btn btn-primary">
                        <i class="bi bi-printer"></i> Cetak Surat
                    </button>
                    @if(auth()->user() && auth()->user()->role?->name === 'operator')
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
        <div class="alert alert-success mt-4 no-print">
            <i class="bi bi-check-circle"></i>
            <strong>Status Permohonan:</strong> DISETUJUI - Silakan cetak surat ini sebagai Surat Izin Reklame resmi.
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
        function printAndTrack() {
            window.print();
            setTimeout(() => {
                fetch('{{ route("print.track-surat", $permohonan) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
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
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }, 1000);
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
@else
    {{-- PDF Mode (tidak extend layout) --}}
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        @include('print.partials.document-styles')
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.5;
                margin: 0;
                padding: 0;
                color: #000;
            }
            .print-document {
                page-break-after: avoid;
                overflow: hidden;
            }
            @page {
                margin: 1cm;
                padding: 0;
                size: A4 portrait;
            }
            @media print {
                body {
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100%;
                    height: 100%;
                }
                html {
                    margin: 0 !important;
                    padding: 0 !important;
                    width: 100%;
                    height: 100%;
                }
                .no-print {
                    display: none !important;
                }
                * {
                    page-break-inside: avoid;
                }
            }
        </style>
    </head>
    <body>
        <div class="print-document">
            @include('print.partials.document-content')
        </div>
    </body>
    </html>
@endif
