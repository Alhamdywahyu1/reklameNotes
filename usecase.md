graph TD
    subgraph System["Sistem Izin Reklame DPMPTSP"]
        %% Authentication Use Cases
        Register["📝 Register / Daftar"]
        Login["🔐 Login"]
        GoogleLogin["🔵 Login dengan Google"]
        VerifyEmail["✉️ Verifikasi Email"]
        ForgotPassword["🔑 Reset Password"]
        
        %% Pemohon Use Cases
        CreatePermohonan["➕ Buat Permohonan"]
        FillForm["📋 Isi Form Permohonan"]
        UploadDocuments["📤 Upload Dokumen"]
        ViewStatus["👀 Lihat Status"]
        EditPermohonan["✏️ Edit Permohonan"]
        DownloadSurat["💾 Download Surat Izin"]
        TrackApproval["🔍 Tracking Approval"]
        
        %% Operator Use Cases
        VerifyDocuments["✓ Verifikasi Dokumen"]
        CheckData["📊 Check Data Kelengkapan"]
        RejectDocuments["❌ Reject Dokumen"]
        ApproveDocuments["✅ Approve Dokumen"]
        SendToKepalaSeksi["➡️ Kirim ke Kepala Seksi"]
        
        %% Kepala Seksi Use Cases
        ReviewPermohonan["👁️ Review Permohonan"]
        ApproveKS["✅ Approve"]
        RejectKS["❌ Reject"]
        SendToKepalaBidang["➡️ Kirim ke Kepala Bidang"]
        
        %% Kepala Bidang Use Cases
        FinalReview["👑 Final Review"]
        FinalApprove["✅ Final Approve"]
        FinalReject["❌ Final Reject"]
        GenerateSurat["📄 Generate Surat Izin"]
        
        %% Admin Use Cases
        ManageUsers["👥 Manage Users"]
        ViewReports["📈 View Reports"]
        MonitorSystem["🖥️ Monitor System"]
        ViewLogs["📝 View Activity Logs"]
        ManageRoles["🔐 Manage Roles"]
        
        %% Notification Use Cases
        ReceiveNotification["🔔 Terima Notifikasi"]
        ViewNotification["👀 Lihat Notifikasi"]
        MarkAsRead["✓ Mark As Read"]
    end

    %% Actors
    Pemohon["👤 PEMOHON<br/>(Pengguna)"]
    Operator["👥 OPERATOR"]
    KepalaSeksi["👔 KEPALA SEKSI"]
    KepalaBidang["🎩 KEPALA BIDANG"]
    Admin["🔐 ADMIN"]
    System_External["📧 Email Service"]

    %% Associations Pemohon
    Pemohon -->|uses| Register
    Pemohon -->|uses| Login
    Pemohon -->|uses| GoogleLogin
    Pemohon -->|uses| VerifyEmail
    Pemohon -->|uses| ForgotPassword
    Pemohon -->|uses| CreatePermohonan
    Pemohon -->|uses| FillForm
    Pemohon -->|uses| UploadDocuments
    Pemohon -->|uses| ViewStatus
    Pemohon -->|uses| EditPermohonan
    Pemohon -->|uses| DownloadSurat
    Pemohon -->|uses| TrackApproval
    Pemohon -->|uses| ReceiveNotification
    Pemohon -->|uses| ViewNotification
    Pemohon -->|uses| MarkAsRead

    %% Associations Operator
    Operator -->|uses| Login
    Operator -->|uses| VerifyDocuments
    Operator -->|uses| CheckData
    Operator -->|uses| RejectDocuments
    Operator -->|uses| ApproveDocuments
    Operator -->|uses| SendToKepalaSeksi
    Operator -->|uses| ReceiveNotification
    Operator -->|uses| ViewNotification

    %% Associations Kepala Seksi
    KepalaSeksi -->|uses| Login
    KepalaSeksi -->|uses| ReviewPermohonan
    KepalaSeksi -->|uses| ApproveKS
    KepalaSeksi -->|uses| RejectKS
    KepalaSeksi -->|uses| SendToKepalaBidang
    KepalaSeksi -->|uses| ReceiveNotification
    KepalaSeksi -->|uses| ViewNotification

    %% Associations Kepala Bidang
    KepalaBidang -->|uses| Login
    KepalaBidang -->|uses| FinalReview
    KepalaBidang -->|uses| FinalApprove
    KepalaBidang -->|uses| FinalReject
    KepalaBidang -->|uses| GenerateSurat
    KepalaBidang -->|uses| ReceiveNotification
    KepalaBidang -->|uses| ViewNotification

    %% Associations Admin
    Admin -->|uses| Login
    Admin -->|uses| ManageUsers
    Admin -->|uses| ViewReports
    Admin -->|uses| MonitorSystem
    Admin -->|uses| ViewLogs
    Admin -->|uses| ManageRoles

    %% External System
    System_External -->|triggers| ReceiveNotification
    VerifyEmail -->|uses| System_External
    GenerateSurat -->|uses| System_External

    %% Relationships/Dependencies
    CreatePermohonan -->|includes| FillForm
    CreatePermohonan -->|includes| UploadDocuments
    FillForm -->|extends| EditPermohonan
    UploadDocuments -->|extends| EditPermohonan
    VerifyDocuments -->|includes| CheckData
    ApproveDocuments -->|triggers| SendToKepalaSeksi
    RejectDocuments -->|triggers| ReceiveNotification
    ApproveKS -->|triggers| SendToKepalaBidang
    RejectKS -->|triggers| ReceiveNotification
    FinalApprove -->|includes| GenerateSurat
    GenerateSurat -->|triggers| DownloadSurat
    FinalReject -->|triggers| ReceiveNotification
    FinalApprove -->|triggers| ReceiveNotification

    style System fill:#f0f4ff
    style Pemohon fill:#e3f2fd
    style Operator fill:#fff3e0
    style KepalaSeksi fill:#f3e5f5
    style KepalaBidang fill:#ffe0b2
    style Admin fill:#f5f5f5
    style System_External fill:#e8f5e9
