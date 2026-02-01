# 📊 ARCHITECTURE & FLOW DIAGRAMS

---

## 1. System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                     PRINT SURAT & NOTIFICATION SYSTEM               │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐                ┌──────────────────┐
│   OPERATOR   │                │   PEMOHON        │
└──────────────┘                └──────────────────┘
       │                                │
       │ 1. LOGIN & OPEN PRINT PAGE    │
       ├──────────────────────────────▶│ (403 ERROR)
       │                                │
       │ 2. CLICK "CETAK SURAT"        │
       │                                │
       ▼                                │
┌─────────────────────┐                │
│  Print Dialog       │                │
│  (Browser)          │                │
│                     │                │
│  [Print] [Cancel]   │                │
└─────────────────────┘                │
       │                                │
       │ 3. CLOSE DIALOG (1 SEC DELAY) │
       │                                │
       ▼                                │
┌──────────────────────────────────────┐
│  PrintController::trackPrintSurat()  │
│                                      │
│  1. Authorization check ✓            │
│  2. Dispatch Event                   │
└──────────────────────────────────────┘
       │
       │ 4. EVENT DISPATCHED
       ▼
┌──────────────────────────────────────┐
│  SuratDiprintOlehOperator Event      │
│                                      │
│  Properties:                         │
│  - permohonan (Object)               │
│  - operatorId (int)                  │
└──────────────────────────────────────┘
       │
       │ 5. LISTENER HANDLES EVENT
       ▼
┌──────────────────────────────────────┐
│  CreateNotificationSuratDiprint      │
│  (Event Listener)                    │
│                                      │
│  Executes:                           │
│  1. Create Notification Record       │
│  2. Send Email (SuratDiprintMail)    │
│  3. Log Activity                     │
└──────────────────────────────────────┘
       │
       │ 6. RESULTS
       ├─────────────────────────────────────▶  📧 EMAIL TO PEMOHON
       │
       ├─────────────────────────────────────▶  📝 NOTIFICATION IN DB
       │
       └─────────────────────────────────────▶  📋 ACTIVITY LOG
                                                      │
                                                      ▼
                                               ┌──────────────┐
                                               │ PEMOHON      │
                                               └──────────────┘
                                                      │
                                      ┌───────────────┼───────────────┐
                                      │               │               │
                                      ▼               ▼               ▼
                                  📧 EMAIL       💬 NOTIFICATION  📋 ACTIVITY
                                  RECEIVED      CREATED IN DB      LOGGED
```

---

## 2. Access Control Flow

```
USER REQUEST: GET /print/{id}/surat
        │
        ▼
    MIDDLEWARE CHECK
    ├─ Is user authenticated? 
    │  ├─ NO ──▶ REDIRECT TO LOGIN
    │  └─ YES ──▶ Continue
    │
    ├─ Has role middleware?
    │  ├─ NO ROLE ──▶ 403 FORBIDDEN ❌
    │  └─ ROLE (operator|admin) ──▶ Continue
    │
    ▼
CONTROLLER AUTHORIZATION
├─ Is user operator OR admin?
│  ├─ NO ──▶ abort(403, 'Hanya Operator...') ❌
│  └─ YES ──▶ Continue
│
├─ Is permohonan printable?
│  ├─ NO ──▶ abort(403, 'Surat hanya dapat dicetak...') ❌
│  └─ YES ──▶ Continue
│
▼
✅ ALLOW ACCESS - RENDER PRINT SURAT PAGE

┌─────────────────────────────────────────┐
│ RESULT:                                 │
├─────────────────────────────────────────┤
│ Pemohon:   ❌ 403 Forbidden             │
│ Operator:  ✅ 200 OK (Surat rendered)   │
│ Admin:     ✅ 200 OK (Surat rendered)   │
└─────────────────────────────────────────┘
```

---

## 3. Event Flow Sequence

```
Timeline:

T+0s:   Operator clicks "Cetak Surat" button
        └─▶ printAndTrack() JavaScript function called
        
T+0.1s: window.print() executed
        └─▶ Browser print dialog opens
        
T+0.5s: Operator confirms/cancels print dialog
        └─▶ Print job sent to printer (or canceled)
        
T+1s:   setTimeout callback executed
        └─▶ fetch() POST request sent to backend
        
T+1.1s: POST /print/{id}/track-surat arrives at controller
        ├─▶ Authorization check passed
        ├─▶ Event SuratDiprintOlehOperator::dispatch()
        └─▶ Return JSON: {message: "Surat berhasil dicetak..."}
        
T+1.2s: Browser receives response
        ├─▶ JavaScript parses JSON
        ├─▶ Creates success alert
        └─▶ Shows to operator: "Berhasil!"
        
T+1.3s: Listener CreateNotificationSuratDiprint executes
        ├─▶ INSERT notification record
        ├─▶ SEND email via Mail::to()->send()
        └─▶ INSERT activity log record
        
T+2s:   Email sent (or queued if async)
        ├─▶ SMTP connection established
        ├─▶ Email transmitted
        └─▶ Email arrives in pemohon inbox

T+5s:   Pemohon checks email
        └─▶ Sees: "Surat Persetujuan Reklame Anda Telah Siap"

T+30s:  Pemohon logs in to system
        ├─▶ Checks notifications
        └─▶ Sees: "Surat Persetujuan Siap" (blue badge)
```

---

## 4. Database Transactions

```
BEFORE:
┌─────────────────────┐
│ notifications table │
└─────────────────────┘

AFTER trackPrintSurat():

INSERT INTO notifications (
    user_id,           ◄─ pemohon_id
    type,              ◄─ 'SURAT_DIPRINT'
    title,             ◄─ 'Surat Persetujuan Siap'
    message,           ◄─ 'Surat persetujuan reklame...'
    permohonan_id,     ◄─ {id}
    read_at,           ◄─ NULL
    created_at         ◄─ now()
) VALUES (...)

INSERT INTO activity_logs (
    user_id,           ◄─ operator_id
    action,            ◄─ 'PRINT_SURAT'
    model_type,        ◄─ 'PermohonanReklame'
    model_id,          ◄─ {id}
    description,       ◄─ 'Mencetak surat persetujuan {nomor}'
    ip_address,        ◄─ '192.168.x.x'
    user_agent,        ◄─ 'Mozilla/5.0...'
    created_at         ◄─ now()
) VALUES (...)

RESULT IN DATABASE:
┌─────────────────────────────────────────────┐
│ notifications                               │
├─────────────────────────────────────────────┤
│ id: 1                                       │
│ user_id: 2 (pemohon)                        │
│ type: SURAT_DIPRINT                         │
│ title: Surat Persetujuan Siap               │
│ message: Surat persetujuan reklame Anda...  │
│ permohonan_id: 5                            │
│ read_at: NULL                               │
│ created_at: 2026-02-01 10:30:45             │
└─────────────────────────────────────────────┘

┌─────────────────────────────────────────────┐
│ activity_logs                               │
├─────────────────────────────────────────────┤
│ id: 456                                     │
│ user_id: 3 (operator)                       │
│ action: PRINT_SURAT                         │
│ model_type: PermohonanReklame                │
│ model_id: 5                                 │
│ description: Mencetak surat persetujuan...  │
│ ip_address: 127.0.0.1                       │
│ user_agent: Mozilla/5.0...                  │
│ created_at: 2026-02-01 10:30:45             │
└─────────────────────────────────────────────┘
```

---

## 5. Email Template Flow

```
EVENT: SuratDiprintOlehOperator dispatched
   │
   ▼
LISTENER: CreateNotificationSuratDiprint runs
   │
   ├─▶ Mail::to($pemohon->email)->send(
   │       new SuratDiprintMail($permohonan, $operator->name)
   │   )
   │
   ▼
MAIL CLASS: SuratDiprintMail
   │
   ├─ envelope()
   │  └─ Subject: "Surat Persetujuan Reklame Anda Telah Siap - [NOMOR]"
   │
   ├─ content()
   │  └─ View: emails.surat_diprint
   │     Data: {permohonan, operatorName}
   │
   └─ attachments()
      └─ (empty - no attachments)
   │
   ▼
BLADE TEMPLATE: resources/views/emails/surat_diprint.blade.php
   │
   ├─ @component('mail::message')
   │  │
   │  ├─ # Surat Persetujuan Reklame Anda Siap
   │  │
   │  ├─ Halo {{ $permohonan->nama_pemohon }},
   │  │
   │  ├─ Detail Permohonan:
   │  │  ├─ Nomor Registrasi: {{ $permohonan->nomor_registrasi }}
   │  │  ├─ Jenis Reklame: {{ $permohonan->jenis_reklame }}
   │  │  ├─ Ukuran: {{ $permohonan->ukuran_reklame }}
   │  │  ├─ Jumlah: {{ $permohonan->jumlah_reklame }} Unit
   │  │  └─ Lokasi: {{ $permohonan->lokasi_pemasangan }}
   │  │
   │  ├─ Apa Selanjutnya?
   │  │  └─ Silakan ambil surat di kantor...
   │  │
   │  └─ Kontak: Alamat & Telepon Kantor
   │
   └─ @endcomponent
   │
   ▼
EMAIL RENDERED (HTML)
   │
   ▼
SENT VIA SMTP
   │
   ├─ TO: pemohon@email.com
   ├─ FROM: noreply@example.com
   ├─ SUBJECT: Surat Persetujuan Reklame Anda Telah Siap - [NOMOR]
   └─ BODY: Rendered HTML email
   │
   ▼
📧 EMAIL ARRIVES IN INBOX
```

---

## 6. Frontend User Interface Flow

### Operator View (Halaman Surat Persetujuan)

```
┌──────────────────────────────────────────────────────────┐
│  SURAT PERSETUJUAN PEMASANGAN REKLAME                    │
│  (NO_REGISTRASI)                    [Cetak Surat] [Back] │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ╔═══════════════════════════════════════════════════╗   │
│  ║  PEMERINTAH KABUPATEN BANGKALAN                   ║   │
│  ║  DINAS PENANAMAN MODAL DAN PELAYANAN TERPADU SATU ║   │
│  ╚═══════════════════════════════════════════════════╝   │
│                                                          │
│  Nomor Surat: [NOMOR]/DPMPTSP/2026                       │
│  Tanggal: 01 Februari 2026                               │
│  Perihal: Surat Persetujuan Pemasangan Reklame           │
│                                                          │
│  [Surat Content...]                                      │
│  [Tanda Tangan Section]                                  │
│                                                          │
└──────────────────────────────────────────────────────────┘

Operator clicks "Cetak Surat":
         │
         ▼
    printAndTrack() function
         │
         ▼
    ┌─────────────────────┐
    │ Print Dialog        │
    │ [  Printer Select ] │
    │ [Print] [Cancel]    │
    └─────────────────────┘
         │
         ▼ (After close/print)
    ┌──────────────────────────────────────────┐
    │ ✅ Berhasil!                             │
    │ Surat berhasil dicetak. Notifikasi       │
    │ telah dikirim ke pemohon.                │
    │ [X]                                      │
    └──────────────────────────────────────────┘
         │
         └─ Auto-dismiss after 5 seconds
```

### Pemohon View (Halaman Notifikasi)

```
┌──────────────────────────────────────────────────────────┐
│  🔔 NOTIFIKASI                                [✓ Baca]   │
│                                                          │
│  Anda memiliki 1 notifikasi yang belum dibaca            │
├──────────────────────────────────────────────────────────┤
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ 🖨️ [Surat Siap]  [Baru]        5 menit lalu     │⋮│ │
│  │                                                    │ │ │
│  │ Surat Persetujuan Siap                             │ │ │
│  │ Surat persetujuan reklame Anda (REG-001) telah      │ │ │
│  │ disiapkan oleh [Nama Operator] dan siap untuk       │ │ │
│  │ diambil.                                            │ │ │
│  │                                                    │ │ │
│  │ [Lihat Permohonan]  [⋮ More actions]              │ │ │
│  └────────────────────────────────────────────────────┘ │
│       ▲ Left border: Blue (#0d6efd)                     │
│                                                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │ ✓ [Status Berubah]               10 hari lalu    │⋮│ │
│  │ (other old notifications...)                       │ │ │
│  └────────────────────────────────────────────────────┘ │
│                                                          │
│  [Previous]  [1]  [Next]                                │
│                                                          │
└──────────────────────────────────────────────────────────┘

Actions available:
├─ Tandai Terbaca (remove "Baru" badge)
├─ Lihat Permohonan (navigate to details)
└─ Hapus (remove notification)
```

---

## 7. Error Handling Flow

```
USER ATTEMPTS TO ACCESS: /print/{id}/surat

                    ┌─────────────────┐
                    │  REQUEST ROUTE  │
                    └────────┬────────┘
                             │
                    ┌────────▼────────┐
                    │ MIDDLEWARE CHECK│
                    │ (auth, role)    │
                    └────────┬────────┘
                             │
                        ┌────┴────┐
                        │          │
            ❌ Failed   │  ✅ Pass │
                        │          │
                    ┌───▼─────┐    │
                    │ REDIRECT │    │
                    │ TO LOGIN │    │
                    └──────────┘    │
                                    │
                        ┌───────────▼──────────┐
                        │ CONTROLLER::printSurat|
                        └────────────┬─────────┘
                                     │
                                ┌────┴────┐
                                │          │
                    ❌ User not oper|  ✅ Operator
                                │          │
                        ┌───────▼─────┐   │
                        │   abort(403) │   │
                        │   "Hanya Op" │   │
                        └──────────────┘   │
                                           │
                                ┌──────────▼──────────┐
                                │ Check isPrintable() │
                                └──────────┬──────────┘
                                           │
                                       ┌───┴───┐
                                       │       │
                    ❌ Not approved   │  ✅ Approved
                                       │       │
                                ┌──────▼─────┐ │
                                │abort(403)  │ │
                                │"Dokumen... │ │
                                └────────────┘ │
                                               │
                                    ┌──────────▼──────────┐
                                    │ RENDER SURAT VIEW   │
                                    │ ✅ 200 OK           │
                                    └─────────────────────┘
```

---

## 8. State Transitions (Notification)

```
INITIAL STATE (After print):
┌─────────────────────┐
│ Notification       │
├─────────────────────┤
│ read_at: NULL       │  ◄─ Unread
│ status: NEW         │
└─────────────────────┘
        │
        │ User clicks "Tandai Terbaca"
        ▼
┌─────────────────────┐
│ Notification       │
├─────────────────────┤
│ read_at: now()      │  ◄─ Read
│ status: READ        │
└─────────────────────┘
        │
        │ User clicks "Hapus"
        ▼
┌─────────────────────┐
│ Notification       │
├─────────────────────┤
│ DELETED FROM UI     │  ◄─ But DB record persists
│ (soft or hard)      │     for audit trail
└─────────────────────┘
```

---

## 9. Component Interaction Diagram

```
                    ┌─────────────────┐
                    │   PrintController│
                    └────────┬─────────┘
                             │
        ┌────────────────────┼────────────────────┐
        │                    │                    │
        ▼                    ▼                    ▼
   printSurat()      trackPrintSurat()    ✅ Event Dispatch
   (show form)       (handle tracking)         │
        │                    │                 │
        │                    ├──────────────┬──┘
        │                    │              │
        │                    ▼              ▼
        │            ┌──────────────────────────┐
        │            │  SuratDiprintOlehOperator│
        │            │  (Event)                 │
        │            └──────────┬───────────────┘
        │                       │
        │                       │ Event::listen()
        │                       ▼
        │            ┌──────────────────────────────┐
        │            │CreateNotificationSuratDiprint│
        │            │(Listener)                    │
        │            └──────┬───────────────┬───────┘
        │                   │               │
        │                   ▼               ▼
        │            ┌────────────────┐  ┌─────────────┐
        │            │Notification   │  │Mail::send() │
        │            │::create()      │  │(SuratDigint │
        │            └────────────────┘  │Mail)        │
        │                                 └──────┬──────┘
        │                                        │
        │                    ┌───────────────────┘
        │                    │
        ▼                    ▼
    ┌────────────┐   ┌────────────────┐
    │ Surat View │   │Email Template  │
    │(Blade)     │   │(surat_diprint) │
    └─────┬──────┘   └────────┬───────┘
          │                   │
          │ Print button      │ Email body
          │ JS function       │ rendered
          ▼                   ▼
    [printAndTrack()] ──▶ 📧 Email

```

---

## 10. Notification Badge Types

```
TYPE: PENGAJUAN_BARU
Badge Color: Yellow (#ffc107)
Icon: plus-circle
Left Border: Yellow
Use: When new application created

TYPE: PERMOHONAN_DITOLAK  
Badge Color: Red (#dc3545)
Icon: x-circle
Left Border: Red
Use: When application rejected

TYPE: SURAT_DIPRINT ✨ NEW
Badge Color: Blue (#0d6efd)
Icon: printer (bi-printer)
Left Border: Blue
Use: When surat is printed

TYPE: (Default)
Badge Color: Green (#28a745)
Icon: check-circle
Left Border: Green
Use: When status changed

VISUAL:
┌─────────────────────────────────────────┐
│ 📋 [TYPE]  [Baru]   [Waktu lalu]  ⋮   │
│ Title Notifikasi                        │
│ Pesan notifikasi yang lebih panjang...  │
└─────────────────────────────────────────┘
  ◀─ Left border color (4px)
```

---

**End of Architecture & Flow Diagrams**

