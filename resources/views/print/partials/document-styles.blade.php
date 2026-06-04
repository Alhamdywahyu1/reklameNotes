<style>
    @page {
        size: 210mm 330mm;
        margin: 1.5cm 2cm;
    }

    .print-document {
        font-family: Arial, sans-serif;
        font-size: 12px;
        line-height: 1.5;
        color: #000;
        position: relative;
    }

    .print-header-image {
        width: 100%;
        margin-bottom: 8px;
    }

    .print-header-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .print-title {
        text-align: center;
        margin: 8px 0 4px 0;
    }

    .print-title .title-text {
        margin: 0;
        font-size: 12px;
        font-weight: bold;
        text-decoration: underline;
        letter-spacing: 0.5px;
    }

    .print-title .title-number {
        margin: 4px 0 0 0;
        font-size: 12px;
    }

    .section-text {
        font-size: 12px;
        line-height: 1.45;
    }

    .center-strong {
        text-align: center;
        margin: 8px 0;
        font-size: 12px;
        font-weight: bold;
    }

    .data-table {
        width: 95%;
        margin-left: 18px;
        font-size: 12px;
        line-height: 1.45;
    }

    .data-table td {
        vertical-align: top;
        padding: 0 3px;
    }

    .num-col {
        width: 18px;
    }

    .label-col {
        width: 250px;
    }

    .sep-col {
        width: 15px;
    }

    .syarat-table {
        margin-left: 10px;
        margin-top: 2px;
    }

    .syarat-table td {
        vertical-align: top;
        padding: 1px 3px;
    }

    .ttd-section {
        width: 100%;
    }

    .ttd-block {
        margin-top: 12px;
        page-break-inside: avoid;
        break-inside: avoid;
    }

    .ttd-section td {
        vertical-align: top;
    }

    .ttd-right {
        text-align: left;
        font-size: 12px;
    }

    .ttd-right .jabatan {
        font-weight: bold;
        font-size: 12px;
    }

    .ttd-right .nama {
        font-weight: bold;
        text-decoration: underline;
    }

    .ttd-right .nip {
        font-size: 12px;
    }

    .ttd-spacer {
        height: 0px;
    }

    .watermark,
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

    .watermark img,
    .preview-watermark img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
