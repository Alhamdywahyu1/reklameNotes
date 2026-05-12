@extends('layouts.app')

@section('title', 'Riwayat Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="mb-1 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Riwayat Login</h4>
                <p class="text-muted mb-0 small">Daftar aktivitas login & logout akun kamu</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-0">
                @if($logs->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox" style="font-size:3rem;"></i>
                        <p class="mt-2">Belum ada riwayat login.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background:#f8fafc;">
                                <tr>
                                    <th class="ps-4 py-3" style="font-size:13px; color:#64748b; font-weight:600;">AKTIVITAS</th>
                                    <th class="py-3" style="font-size:13px; color:#64748b; font-weight:600;">WAKTU</th>
                                    <th class="py-3" style="font-size:13px; color:#64748b; font-weight:600;">IP ADDRESS</th>
                                    <th class="py-3 pe-4" style="font-size:13px; color:#64748b; font-weight:600;">PERANGKAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            @if($log->action === 'LOGIN')
                                                <span class="badge rounded-pill px-3 py-2" style="background:#dcfce7; color:#166534; font-size:12px;">
                                                    <i class="bi bi-box-arrow-in-right me-1"></i> LOGIN
                                                </span>
                                            @else
                                                <span class="badge rounded-pill px-3 py-2" style="background:#fee2e2; color:#991b1b; font-size:12px;">
                                                    <i class="bi bi-box-arrow-right me-1"></i> LOGOUT
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold" style="font-size:14px;">
                                                {{ $log->created_at->format('d M Y') }}
                                            </div>
                                            <div class="text-muted" style="font-size:12px;">
                                                {{ $log->created_at->format('H:i:s') }} WIB
                                                &nbsp;·&nbsp;
                                                <span title="{{ $log->created_at }}">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <code style="font-size:13px; background:#f1f5f9; padding:3px 8px; border-radius:4px;">
                                                {{ $log->ip_address ?? '-' }}
                                            </code>
                                        </td>
                                        <td class="py-3 pe-4">
                                            @php
                                                $ua = $log->user_agent ?? '';
                                                $browser = 'Browser';
                                                $os      = 'Unknown OS';
                                                if (str_contains($ua, 'Chrome'))         $browser = '<i class="bi bi-browser-chrome"></i> Chrome';
                                                elseif (str_contains($ua, 'Firefox'))    $browser = '<i class="bi bi-browser-firefox"></i> Firefox';
                                                elseif (str_contains($ua, 'Safari'))     $browser = '<i class="bi bi-browser-safari"></i> Safari';
                                                elseif (str_contains($ua, 'Edge'))       $browser = '<i class="bi bi-browser-edge"></i> Edge';
                                                if (str_contains($ua, 'Windows'))        $os = 'Windows';
                                                elseif (str_contains($ua, 'Macintosh'))  $os = 'macOS';
                                                elseif (str_contains($ua, 'Linux'))      $os = 'Linux';
                                                elseif (str_contains($ua, 'Android'))    $os = 'Android';
                                                elseif (str_contains($ua, 'iPhone'))     $os = 'iPhone';
                                            @endphp
                                            <span style="font-size:13px;">{!! $browser !!}</span>
                                            <span class="text-muted" style="font-size:12px;"> · {{ $os }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 border-top">
                        {{ $logs->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
