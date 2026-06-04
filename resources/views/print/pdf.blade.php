<?php
// PDF template - Surat IZIN Reklame
?>
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
    @php $isPdf = true; @endphp
    <div class="print-document">
        @include('print.partials.document-content')
    </div>
</body>
</html>
