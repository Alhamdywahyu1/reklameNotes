<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermohonanReklameController;
use App\Http\Controllers\DocumentRequirementController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LoginHistoryController;
use App\Http\Controllers\SuratPernyataanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\SatpolPpController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// TEST ROUTE - No middleware at all
Route::get('/test-approval-9', function () {
    $permohonan = \App\Models\PermohonanReklame::find(9);
    if (!$permohonan) {
        return 'Permohonan ID 9 not found in database';
    }
    return 'SUCCESS! Permohonan: ' . $permohonan->nomor_registrasi . ' - Status: ' . $permohonan->status;
});

// Auth routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    // Google OAuth
    Route::get('auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

// Email Verification & OTP Routes
Route::middleware('auth')->group(function () {
    Route::get('email/verify', [VerifyEmailController::class, 'notice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
        ->middleware('signed')->name('verification.verify');
    Route::post('email/verification-notification', [VerifyEmailController::class, 'resend'])
        ->middleware('throttle:6,1')->name('verification.send');

    // OTP Routes
    Route::get('otp/verify', [OtpController::class, 'show'])->name('otp.show');
    Route::post('otp/verify', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('otp/resend', [OtpController::class, 'resend'])->middleware('throttle:3,1')->name('otp.resend');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Authenticated routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('verify_email');

    // Login History (Operator only)
    Route::middleware('role:operator')->group(function () {
        Route::get('profile/login-history', [LoginHistoryController::class, 'index'])->name('profile.login-history');
    });

    // Profile (semua user yang sudah login)
    Route::get('profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Reklame Chart (Kepala Seksi & Kepala Bidang only)
    Route::middleware('role:kepala_seksi,kepala_bidang')->group(function () {
        Route::get('dashboard/reklame-chart', [DashboardController::class, 'reklameChart'])->name('dashboard.reklame-chart');
    });

    // Permohonan Reklame Routes (untuk Pemohon only)
    Route::middleware(['role:pemohon', 'verify_email'])->group(function () {
        Route::get('permohonan', [PermohonanReklameController::class, 'index'])->name('permohonan.index');
        Route::get('permohonan/create', [PermohonanReklameController::class, 'create'])->name('permohonan.create');
        Route::post('permohonan', [PermohonanReklameController::class, 'store'])->name('permohonan.store');
        Route::get('permohonan/{permohonan}/edit', [PermohonanReklameController::class, 'edit'])->name('permohonan.edit');
        Route::put('permohonan/{permohonan}', [PermohonanReklameController::class, 'update'])->name('permohonan.update');
        Route::post('permohonan/{permohonan}/submit', [PermohonanReklameController::class, 'submit'])->name('permohonan.submit');
        Route::delete('permohonan/{permohonan}', [PermohonanReklameController::class, 'destroy'])->name('permohonan.destroy');
        
        // Surat Pernyataan Routes (untuk Pemohon only)
        Route::get('surat-pernyataan/{permohonan}/create', [SuratPernyataanController::class, 'create'])->name('surat-pernyataan.create');
        Route::post('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'store'])->name('surat-pernyataan.store');
        Route::get('surat-pernyataan/{permohonan}/edit', [SuratPernyataanController::class, 'edit'])->name('surat-pernyataan.edit');
        Route::put('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'update'])->name('surat-pernyataan.update');
        Route::delete('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'destroy'])->name('surat-pernyataan.destroy');
    });

    // Surat Pernyataan Show & Download - accessible to pemohon and staff
    Route::middleware('auth')->group(function () {
        Route::get('surat-pernyataan/{permohonan}', [SuratPernyataanController::class, 'show'])->name('surat-pernyataan.show');
        Route::get('surat-pernyataan/{permohonan}/download-pdf', [SuratPernyataanController::class, 'downloadPdf'])->name('surat-pernyataan.download-pdf');
    });

    // Permohonan Show - accessible to both pemohon and staff
    Route::get('permohonan/{permohonan}', [PermohonanReklameController::class, 'show'])->name('permohonan.show');
    
    // Download dokumen file
    Route::get('permohonan/{permohonan}/download/{fileType}', [PermohonanReklameController::class, 'downloadFile'])->name('permohonan.download');

    // Peta Digital GIS - accessible to all authenticated users
    Route::get('permohonan/peta/digital', [PermohonanReklameController::class, 'peta'])->name('permohonan.peta');

    // Approval Routes (untuk Operator, Kepala Seksi, Kepala Bidang, Admin)
    Route::group(['middleware' => 'verify_email'], function () {
        Route::get('approval/dashboard', [ApprovalController::class, 'dashboard'])->name('approval.dashboard');
        Route::get('approval/revisi', [ApprovalController::class, 'revisi'])->name('approval.revisi');

        // Operator verification
        Route::get('approval/{permohonan}/verify', [ApprovalController::class, 'verifyOperator'])->name('approval.verify');
        Route::post('approval/{permohonan}/verify', [ApprovalController::class, 'storeOperatorVerification'])->name('approval.verify.store');
        Route::patch('approval/{permohonan}/persyaratan-status', [ApprovalController::class, 'savePersyaratanStatus'])->name('approval.persyaratan-status');

        // Kepala Seksi approval
        Route::get('approval/{permohonan}/approve-seksi', [ApprovalController::class, 'approveKepalaSeksi'])->name('approval.approve-seksi');
        Route::post('approval/{permohonan}/approve-seksi', [ApprovalController::class, 'storeKepalaSeksiApproval'])->name('approval.approve-seksi.store');

        // Kepala Bidang approval
        Route::get('approval/{permohonan}/approve-bidang', [ApprovalController::class, 'approveKepalaBidang'])->name('approval.approve-bidang');
        Route::post('approval/{permohonan}/approve-bidang', [ApprovalController::class, 'storeKepalaBidangApproval'])->name('approval.approve-bidang.store');
    });

    // Approval Status (operator only)
    Route::middleware(['role:operator', 'verify_email'])->group(function () {
        Route::get('approval-status', [ApprovalController::class, 'approvalStatus'])->name('approval.status');
        Route::delete('permohonan/{permohonan}/expired', [PermohonanReklameController::class, 'destroyExpiredByOperator'])->name('permohonan.destroy-expired');
    });

    // Print routes (untuk Operator)
    Route::middleware(['role:operator,admin', 'verify_email'])->group(function () {
        Route::get('print/ready', [PrintController::class, 'readyList'])->name('print.ready');
        Route::get('print/{permohonan}/preview', [PrintController::class, 'preview'])->name('print.preview');
        Route::get('print/{permohonan}/pdf', [PrintController::class, 'generatePdf'])->name('print.pdf');
    });

    // Print surat - accessible to all staff (operator, kepala_seksi, kepala_bidang, admin)
    Route::middleware('auth')->group(function () {
        Route::get('print/{permohonan}/surat', [PrintController::class, 'printSurat'])->name('print.surat');
        Route::post('print/{permohonan}/track-surat', [PrintController::class, 'trackPrintSurat'])->name('print.track-surat');
    });

    // Notification routes
    Route::middleware('verify_email')->group(function () {
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::get('api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('api.notifications.unread-count');
        Route::get('api/notifications/latest-unread', [NotificationController::class, 'getLatestUnread'])->name('api.notifications.latest-unread');
    });

    // Search routes
    Route::get('search', [SearchController::class, 'search'])->name('search')->middleware('verify_email');
    Route::get('api/search/quick', [SearchController::class, 'quickSearch'])->name('api.search.quick')->middleware('verify_email');

    // Document Requirements routes
    Route::middleware(['role:pemohon', 'verify_email'])->group(function () {
        Route::get('permohonan/{permohonan}/requirements/create', [DocumentRequirementController::class, 'createForPemohon'])->name('document-requirements.create');
        Route::post('permohonan/{permohonan}/requirements/store-multiple', [DocumentRequirementController::class, 'storeMultiple'])->name('document-requirements.store-multiple');
    });

    Route::middleware(['role:operator,kepala_seksi,kepala_bidang,admin', 'verify_email'])->group(function () {
        Route::get('permohonan/{permohonan}/requirements/check-staff', [DocumentRequirementController::class, 'viewForStaff'])->name('document-requirements.check-staff');
    });

    Route::middleware(['role:operator,kepala_seksi,kepala_bidang,admin', 'verify_email'])->group(function () {
        Route::patch('requirements/{requirement}/status', [DocumentRequirementController::class, 'updateStatus'])->name('document-requirements.update-status');
    });

    Route::get('requirements/{requirement}/download', [DocumentRequirementController::class, 'download'])->name('document-requirements.download');
    Route::get('requirements/{requirement}/preview', [DocumentRequirementController::class, 'preview'])->name('document-requirements.preview');
    
    Route::delete('requirements/{requirement}', [DocumentRequirementController::class, 'destroy'])->name('document-requirements.destroy');

    // Admin: User Management & Reports
    Route::middleware(['role:admin', 'verify_email'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserManagementController::class);
        Route::post('users/{user}/restore', [UserManagementController::class, 'restore'])->name('users.restore');
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export-pemohon', [ReportController::class, 'exportPemohon'])->name('reports.export-pemohon');
    });

    // Satpol PP Routes
    Route::middleware(['role:satpol_pp', 'verify_email'])->group(function () {
        Route::get('/satpol-pp/map', [SatpolPpController::class, 'map'])->name('satpol-pp.map');
    });
});
