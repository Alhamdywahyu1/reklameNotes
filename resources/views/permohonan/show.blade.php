@extends('layouts.app')

@section('title', 'Detail Permohonan #' . $permohonan->nomor_registrasi)

@section('content')
<!-- Alert untuk Pemohon jika sudah disetujui -->
@if (auth()->user()->hasRole('pemohon') && $permohonan->status === 'Disetujui Kepala Bidang')
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-start">
            <i class="bi bi-check-circle me-3" style="font-size: 1.5rem;"></i>
            <div>
                <h5 class="alert-heading"><strong>Permohonan Anda Telah DISETUJUI!</strong></h5>
                <p class="mb-2">Selamat, permohonan pemasangan reklame Anda telah mendapat persetujuan final dari Kepala Bidang.</p>
                <p class="mb-2"><strong>Langkah Selanjutnya:</strong></p>
                <p class="mb-0">
                    Mohon menemui <strong>Operator/Petugas di kantor DPMPTSP</strong> untuk:
                    <ul class="mt-2 mb-0">
                        <li>Verifikasi final dokumen</li>
                        <li>Mengambil surat persetujuan asli</li>
                        <li>Membayar retribusi (jika ada)</li>
                        <li>Menerima surat jaminan pemasangan</li>
                    </ul>
                </p>
                <hr class="my-2">
                <p class="small mb-0">
                    <i class="bi bi-clock-history"></i> 
                    <strong>Jam Layanan:</strong> Senin - Jumat, 08:00 - 16:00 WIB<br>
                    <strong>Alamat:</strong> Jl. Kartini No.4, Rw. 03, Keraton, Kec. Bangkalan, Kabupaten Bangkalan, Jawa Timur 69119, Indonesia.  | <strong>Telp:</strong> (031) 3095020
                </p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

<div class="header-page">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="bi bi-file-earmark-check"></i> {{ $permohonan->nomor_registrasi }}</h1>
            <p class="text-muted">
                Dibuat: {{ $permohonan->created_at->format('d M Y H:i') }} |
                Status: <span class="badge" style="background-color: 
                    @if($permohonan->status === 'Disetujui Kepala Bidang') #28a745
                    @elseif($permohonan->status === 'Ditolak Operator' || $permohonan->status === 'Ditolak Kepala Seksi') #dc3545
                    @else #17a2b8 @endif
                    ">
                    {{ $permohonan->status }}
                </span>
                @if($permohonan->status === 'Disetujui Kepala Bidang')
                    | Status Kedaluwarsa: 
                    @php $statusKedaluarsa = $permohonan->getStatusKedaluarsa(); @endphp
                    <span class="badge" style="background-color: 
                        @if($statusKedaluarsa === 'Aktif') #198754
                        @elseif($statusKedaluarsa === 'Kedaluwarsa') #dc3545
                        @else #666 @endif
                        ">
                        {{ $statusKedaluarsa }}
                    </span>
                @endif
            </p>
        </div>
        @if (auth()->user()->hasRole('pemohon') && $permohonan->user_id === auth()->id())
            @if ($permohonan->canBeEditedByUser())
                <div>
                    <a href="{{ route('permohonan.edit', $permohonan) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    @if ($permohonan->status === 'Draft')
                        <form action="{{ route('permohonan.submit', $permohonan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin mengajukan permohonan ini?')">
                                <i class="bi bi-check-circle"></i> Ajukan
                            </button>
                        </form>
                    @elseif (str_contains($permohonan->status, 'Ditolak'))
                        <form action="{{ route('permohonan.submit', $permohonan) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info" onclick="return confirm('Yakin ingin mengirim revisi permohonan ini?')">
                                <i class="bi bi-arrow-repeat"></i> Kirim Revisi
                            </button>
                        </form>
                    @endif
                </div>
            @else
                <div class="alert alert-warning d-inline-block" role="alert" style="max-width: 400px;">
                    <i class="bi bi-lock"></i> <strong>Data Terkunci</strong><br>
                    <small>{{ $permohonan->getEditRestrictionReason() }}</small>
                </div>
            @endif
        @elseif (auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang']))
            <div>
                @if ($permohonan->canBeApprovedByOperator() && auth()->user()->hasRole('operator'))
                    <a href="{{ route('approval.verify', $permohonan) }}" class="btn btn-primary">
                        <i class="bi bi-check2-circle"></i> Verifikasi
                    </a>
                @elseif ($permohonan->canBeApprovedByKepalaSeksi() && auth()->user()->hasRole('kepala_seksi'))
                    <a href="{{ route('approval.approve-seksi', $permohonan) }}" class="btn btn-primary">
                        <i class="bi bi-check2-circle"></i> Approve
                    </a>
                @elseif ($permohonan->canBeApprovedByKepalaBidang() && auth()->user()->hasRole('kepala_bidang'))
                    <a href="{{ route('approval.approve-bidang', $permohonan) }}" class="btn btn-primary">
                        <i class="bi bi-check2-circle"></i> Final Approval
                    </a>
                @endif
                @if ($permohonan->isPrintable() && auth()->user()->hasAnyRole(['operator', 'admin']))
                    <a href="{{ route('print.preview', $permohonan) }}" class="btn btn-success">
                        <i class="bi bi-printer"></i> Print
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Data Pemohon -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-person"></i> Data Pemohon</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nama Pemohon</label>
                        <p class="mb-0"><strong>{{ $permohonan->nama_pemohon }}</strong></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">NIK</label>
                        <p class="mb-0"><strong>{{ $permohonan->nik }}</strong></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Alamat</label>
                        <p class="mb-0">{{ $permohonan->alamat_pemohon }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Nomor Telepon</label>
                        <p class="mb-0">{{ $permohonan->nomor_telepon }}</p>
                    </div>
                    @if ($permohonan->npwp)
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">NPWP</label>
                            <p class="mb-0">{{ $permohonan->npwp }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Data Reklame -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-signpost-split"></i> Data Reklame</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Jenis Reklame</label>
                        <p class="mb-0"><strong>{{ $permohonan->jenis_reklame }}</strong></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Jumlah Reklame</label>
                        <p class="mb-0"><strong>{{ $permohonan->jumlah_reklame }}</strong></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Ukuran Reklame</label>
                        <p class="mb-0">{{ $permohonan->ukuran_reklame }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Narasi Reklame</label>
                        <p class="mb-0">{{ $permohonan->narasi_reklame }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="text-muted small">Lokasi Pemasangan</label>
                        <p class="mb-0">{{ $permohonan->lokasi_pemasangan }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Masa Berlaku (jika disetujui) -->
        @if ($permohonan->status === 'Disetujui Kepala Bidang')
            <div class="card mb-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Masa Berlaku Surat</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Berlaku</label>
                            <p class="mb-0"><strong>{{ $permohonan->tanggal_berlaku ? \Carbon\Carbon::parse($permohonan->tanggal_berlaku)->format('d F Y') : '-' }}</strong></p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Tanggal Berakhir</label>
                            <p class="mb-0"><strong>{{ $permohonan->tanggal_berakhir ? \Carbon\Carbon::parse($permohonan->tanggal_berakhir)->format('d F Y') : '-' }}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="text-muted small">Status Kedaluwarsa</label>
                            @php $statusKedaluarsa = $permohonan->getStatusKedaluarsa(); @endphp
                            <p class="mb-0">
                                @if($statusKedaluarsa === 'Aktif')
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> AKTIF</span>
                                    <small class="text-muted d-block mt-2">Surat masih berlaku dan dapat digunakan.</small>
                                @elseif($statusKedaluarsa === 'Kedaluwarsa')
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-circle"></i> KEDALUWARSA</span>
                                    <small class="text-muted d-block mt-2">Surat sudah tidak berlaku dan perlu pembaruan.</small>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> DICABUT</span>
                                    <small class="text-muted d-block mt-2">Surat telah dicabut oleh pihak berwenang.</small>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Persyaratan Dokumen -->
        <div class="card mb-3">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-earmark-check"></i> Persyaratan Dokumen</h5>
                <div>
                    {{-- Upload button untuk Pemohon --}}
                    @if (auth()->user()->hasRole('pemohon') && $permohonan->user_id === auth()->id())
                        <a href="{{ route('document-requirements.create', $permohonan) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-upload"></i> Upload Dokumen
                        </a>
                    @endif

                    {{-- Check button untuk Staff --}}
                    @if (auth()->user()->hasAnyRole(['operator', 'kepala_seksi', 'kepala_bidang', 'admin']))
                        <a href="{{ route('document-requirements.check', $permohonan) }}" class="btn btn-sm btn-success">
                            <i class="bi bi-check-circle"></i> Periksa Dokumen
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Persyaratan</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Dokumen</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($persyaratan as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        {{ $item->jenis_persyaratan }}
                                        @if ($item->jenis_persyaratan === 'Surat Kuasa')
                                            <span class="badge bg-secondary ms-1">Opsional</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            // Logika status:
                                            // - Draft: permohonan masih draft (belum diajukan)
                                            // - Lengkap: sudah diverifikasi dan disetujui
                                            // - Ditolak: sudah diverifikasi dan ditolak
                                            // - Dalam Peninjauan: permohonan sudah diajukan dan file ada tapi belum dicheck
                                            // - Belum Lengkap: file belum diupload
                                            if ($permohonan->status === 'Draft') {
                                                $displayStatus = 'Draft';
                                                $statusClass = 'bg-secondary';
                                            } elseif ($item->status === 'Lengkap') {
                                                $displayStatus = 'Lengkap';
                                                $statusClass = 'bg-success';
                                            } elseif ($item->status === 'Ditolak') {
                                                $displayStatus = 'Ditolak';
                                                $statusClass = 'bg-danger';
                                            } elseif ($item->file_dokumen) {
                                                $displayStatus = 'Dalam Peninjauan';
                                                $statusClass = 'bg-info';
                                            } else {
                                                $displayStatus = 'Belum Lengkap';
                                                $statusClass = 'bg-warning text-dark';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $displayStatus }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->keterangan)
                                            <small class="text-muted">{{ $item->keterangan }}</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($item->file_dokumen)
                                            @php
                                                $extension = strtolower(pathinfo($item->file_dokumen, PATHINFO_EXTENSION));
                                                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif']);
                                            @endphp
                                            <div class="btn-group btn-group-sm">
                                                @if($isImage)
                                                    <a href="{{ route('document-requirements.preview', $item) }}" class="btn btn-outline-info" title="Lihat dokumen" target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('document-requirements.download', $item) }}" class="btn btn-outline-primary" title="Download dokumen">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Catatan penolakan jika ada --}}
                @php
                    $rejected = $persyaratan->where('status', 'Ditolak')->where('catatan_penolakan', '!=', null);
                @endphp
                @if ($rejected->count() > 0)
                    <div class="alert alert-danger mt-3">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Dokumen Ditolak</h6>
                        @foreach ($rejected as $item)
                            <div class="mb-2">
                                <strong>{{ $item->jenis_persyaratan }}:</strong><br>
                                {{ $item->catatan_penolakan }}
                            </div>
                        @endforeach
                        <p class="mb-0 mt-3">Silakan perbaiki dokumen yang ditolak dan upload kembali melalui tombol <strong>Upload Dokumen</strong> di atas.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Surat Pernyataan - Download PDF -->
        <div class="card mb-3">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Surat Pernyataan (Download PDF)</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Unduh formulir surat pernyataan dalam format PDF:</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ asset('fom reklame-halaman-halaman-1.pdf') }}" class="btn btn-success" download>
                        <i class="bi bi-download me-1"></i> Data Isian Pemohon
                    </a>
                    <a href="{{ asset('fom reklame-halaman-halaman-3.pdf') }}" class="btn btn-success" download>
                        <i class="bi bi-download me-1"></i> Surat Pernyataan
                    </a>
                </div>
            </div>
        </div>

        <!-- Riwayat Approval -->
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Approval</h5>
            </div>
            <div class="card-body">
                @if ($approvals->count() > 0)
                    <div class="timeline">
                        @foreach ($approvals as $item)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $item->role->name }}</strong>
                                        <p class="small text-muted mb-1">Oleh: {{ $item->user->name }}</p>
                                        <p class="small text-muted mb-0">{{ $item->tanggal_approval->format('d M Y H:i') }}</p>
                                    </div>
                                    <div>
                                        <span class="badge {{ $item->keputusan === 'Disetujui' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->keputusan }}
                                        </span>
                                    </div>
                                </div>
                                @if ($item->keterangan)
                                    <p class="small mt-2 mb-0">{{ $item->keterangan }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">Belum ada approval</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div style="position: sticky; top: 20px; z-index: 10;">
            <div class="card">
                <div class="card-body">
                    <h5><i class="bi bi-info-circle"></i> Informasi</h5>
                    <hr>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status</small>
                        <span class="badge" style="background-color: 
                            @if($permohonan->status === 'Disetujui Kepala Bidang') #28a745
                            @elseif($permohonan->status === 'Ditolak Operator' || $permohonan->status === 'Ditolak Kepala Seksi') #dc3545
                            @else #17a2b8 @endif
                            ">
                            {{ $permohonan->status }}
                        </span>
                    </div>

                    @if ($permohonan->keterangan_penolakan)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Keterangan Penolakan</small>
                            <span class="text-danger">{{ $permohonan->keterangan_penolakan }}</span>
                        </div>
                    @endif

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Nomor Registrasi</small>
                        <strong>{{ $permohonan->nomor_registrasi }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Pemohon</small>
                        <span>{{ $permohonan->nama_pemohon }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Tanggal Dibuat</small>
                        <span>{{ $permohonan->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Terakhir Diperbarui</small>
                        <span>{{ $permohonan->updated_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

        <!-- Audit Trail / Activity Log -->
        <div class="card mt-3">
            <div class="card-body">
                <h5><i class="bi bi-clock-history"></i> Riwayat Perubahan</h5>
                <hr>
                @php
                    $activityLogs = \App\Models\ActivityLog::where('model_type', 'PermohonanReklame')
                        ->where('model_id', $permohonan->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                @if ($activityLogs->count() > 0)
                    <div class="timeline-simple">
                        @foreach ($activityLogs as $log)
                            <div class="timeline-item mb-3 pb-3" style="border-bottom: 1px solid #eee;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div style="flex: 1;">
                                        <p class="small mb-1">
                                            <strong>
                                                @if ($log->action === 'APPROVAL_OPERATOR')
                                                    <i class="bi bi-check-circle text-info"></i> Verifikasi Operator
                                                @elseif ($log->action === 'APPROVAL_KEPALA_SEKSI')
                                                    <i class="bi bi-check-circle text-info"></i> Approval Kepala Seksi
                                                @elseif ($log->action === 'APPROVAL_KEPALA_BIDANG')
                                                    <i class="bi bi-check-circle text-success"></i> Approval Kepala Bidang
                                                @elseif ($log->action === 'UPDATE')
                                                    <i class="bi bi-pencil text-warning"></i> Update Data
                                                @elseif ($log->action === 'SUBMIT')
                                                    <i class="bi bi-arrow-up text-primary"></i> Pengajuan
                                                @else
                                                    <i class="bi bi-activity"></i> {{ $log->action }}
                                                @endif
                                            </strong>
                                        </p>
                                        <p class="small text-muted mb-1">
                                            {{ $log->description }}
                                        </p>
                                        @if ($log->new_values && isset($log->new_values['keputusan']))
                                            <p class="small mb-1">
                                                <strong>Keputusan:</strong> 
                                                <span class="badge" style="background-color: {{ $log->new_values['keputusan'] === 'Disetujui' ? '#28a745' : '#dc3545' }}">
                                                    {{ $log->new_values['keputusan'] }}
                                                </span>
                                            </p>
                                        @endif
                                        <small class="text-muted d-block">
                                            {{ $log->created_at->format('d M Y H:i') }} | IP: {{ $log->ip_address ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small">Belum ada riwayat perubahan</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
