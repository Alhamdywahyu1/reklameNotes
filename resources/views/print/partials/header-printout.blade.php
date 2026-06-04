@php
    $headerPrintoutPath = base_path('source/header_printout.jpeg');
    $headerPrintoutSrc = file_exists($headerPrintoutPath)
        ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($headerPrintoutPath))
        : null;
@endphp

@if ($headerPrintoutSrc)
    <div class="print-header-image">
        <img src="{{ $headerPrintoutSrc }}" alt="Header Printout Reklame">
    </div>
@endif
