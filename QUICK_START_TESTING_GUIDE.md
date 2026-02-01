# 🚀 QUICK START GUIDE - Testing & Deployment

**Last Updated:** 2026-02-01

---

## 📋 Pre-Deployment Checklist

Sebelum deploy ke production, pastikan:

```bash
# 1. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# 2. Optimize application
composer dump-autoload
php artisan optimize

# 3. Verify environment
php artisan config:cache
php artisan route:cache
```

---

## 🧪 Testing Workflow

### Setup Test Data
```bash
# (Optional) Create test users if needed
php artisan tinker

# Create pemohon user
$pemohon = User::factory()->create(['role_id' => 4]); // role_id for pemohon

# Create operator user  
$operator = User::factory()->create(['role_id' => 2]); // role_id for operator

# Create approved permohonan
$permohonan = PermohonanReklame::factory()->approved()->create(['user_id' => $pemohon->id]);
```

---

## ✅ Test 1: Access Control (Operator vs Pemohon)

### Step 1: Login as Pemohon
```
1. Go to: http://localhost/login
2. Login dengan akun pemohon
3. Navigate to: /dashboard
```

### Step 2: Try to Access Print Page (Should Fail)
```
1. Go to: http://localhost/print/{permohonan_id}/surat
   (Replace {permohonan_id} with actual ID)
2. Expected Result: 
   ✗ 403 Forbidden Error
   ✗ Message: "Hanya Operator yang dapat mencetak surat persetujuan"
3. Status: ✅ PASS if error shown, ❌ FAIL if page accessible
```

### Step 3: Login as Operator
```
1. Logout dari pemohon
2. Go to: http://localhost/login
3. Login dengan akun operator
4. Navigate to: /approval/dashboard
```

### Step 4: Access Print Page (Should Success)
```
1. Find approved permohonan
2. Click button "Cetak Surat" atau navigate to:
   /print/{permohonan_id}/surat
3. Expected Result:
   ✓ Page loads successfully (200 OK)
   ✓ Surat persetujuan displayed
   ✓ Button "Cetak Surat" visible
4. Status: ✅ PASS
```

---

## 📧 Test 2: Email & Notification Trigger

### Prerequisite:
- Operator already on print surat page

### Step 1: Setup Email Testing (Development)
```
Option 1: Use Mailtrap (Recommended for testing)
- Go to mailtrap.io
- Create free account
- Get SMTP credentials
- Update .env:

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="DPMPTSP Bangkalan"

Option 2: Use MailHog (Local testing)
- Download: mailhog.io
- Run: ./MailHog (on Windows) or ./mailhog (on Linux/Mac)
- Access: http://localhost:1025 (SMTP) & http://localhost:8025 (UI)
- Update .env:

MAIL_DRIVER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="DPMPTSP Bangkalan"

Option 3: Use Log Driver (Quickest for testing)
MAIL_DRIVER=log
(Email akan logged ke storage/logs/laravel.log)
```

### Step 2: Trigger Print Action
```
1. Operator on print surat page
2. Click button "Cetak Surat"
3. Print dialog muncul
4. Close dialog (don't actually print or print)
5. Wait ~2 seconds
6. Expected Result:
   ✓ Success message appears: "Berhasil! Surat berhasil dicetak..."
7. Status: ✅ PASS if message shown
```

### Step 3: Check Email
```
If using Mailtrap:
1. Go to mailtrap.io dashboard
2. Go to Inbox
3. Should see email with:
   - Subject: "Surat Persetujuan Reklame Anda Telah Siap - [NOMOR_REGISTRASI]"
   - Sender: noreply@example.com
   - Content: Surat siap message + details

If using MailHog:
1. Go to http://localhost:8025
2. Should see email in inbox
3. Click to see full content

If using Log:
1. Run: tail -f storage/logs/laravel.log
2. Should see email content logged
3. Look for: "Mail_Message" or similar log entry

Status: ✅ PASS if email appears
```

---

## 💬 Test 3: Notification Display

### Prerequisite:
- Print action already triggered (Test 2)
- Email already sent

### Step 1: Logout Operator
```
1. Click Logout (or use incognito window)
2. Status: Logged out ✓
```

### Step 2: Login as Pemohon
```
1. Go to: http://localhost/login
2. Login dengan akun pemohon
3. Dashboard muncul
4. Status: Logged in ✓
```

### Step 3: Access Notification Page
```
1. Look for menu "Notifikasi" atau "Pesan" (usually in header/navbar)
2. Click menu notifikasi
3. Go to: http://localhost/notifications
4. Expected Result:
   ✓ Page loads (200 OK)
   ✓ Notifications list shown
5. Status: ✅ PASS
```

### Step 4: Find New Notification
```
1. On notifications page, look for new notification
2. Should see:
   - Badge: Blue "Surat Siap" with printer icon 🖨️
   - Title: "Surat Persetujuan Siap"
   - Message: "Surat persetujuan reklame Anda... siap untuk diambil"
   - Time: "moments ago" atau waktu recent
3. Status: ✅ PASS if notification visible
```

### Step 5: Test Notification Actions
```
Test 5a: Click "Lihat Permohonan"
- Should navigate to: /permohonan/{id}
- Shows permohonan details
- Status: ✅ PASS if page loads

Test 5b: Mark as Read
- Click dropdown menu (3 dots)
- Click "Tandai Terbaca"
- Notification should update
- "Baru" badge disappears
- Status: ✅ PASS if badge removed

Test 5c: Delete Notification
- Click dropdown menu (3 dots)
- Click "Hapus"
- Confirm delete
- Notification disappears from list
- Status: ✅ PASS if removed
```

---

## 🗄️ Test 4: Database Verification

### Step 1: Check Notification Record
```bash
php artisan tinker

# Check if notification created
Notification::where('type', 'SURAT_DIPRINT')->latest()->first();

# Should return something like:
=> Notification {
     id: 123,
     user_id: {pemohon_id},
     type: "SURAT_DIPRINT",
     title: "Surat Persetujuan Siap",
     message: "Surat persetujuan reklame...",
     permohonan_id: {permohonan_id},
     read_at: null,
     created_at: "2026-02-01 10:30:00",
     updated_at: "2026-02-01 10:30:00",
   }
```

### Step 2: Check Activity Log
```bash
# In tinker:
ActivityLog::where('action', 'PRINT_SURAT')->latest()->first();

# Should show:
=> ActivityLog {
     id: 456,
     user_id: {operator_id},
     action: "PRINT_SURAT",
     model_type: "PermohonanReklame",
     model_id: {permohonan_id},
     description: "Mencetak surat persetujuan {nomor_registrasi}",
     ip_address: "127.0.0.1",
     created_at: "2026-02-01 10:30:00",
   }
```

### Step 3: Verify read_at Status
```bash
# Mark notification as read and verify
$notif = Notification::find(123);
$notif->markAsRead();
$notif->read_at;  // Should show current timestamp
```

---

## 🐛 Troubleshooting Guide

### Issue 1: Operator Still Can't Access Print Page
```
Problem: Operator gets 403 error

Solution:
1. Check operator's role in database:
   SELECT * FROM users WHERE id = {operator_id};
   Verify role_id is correct

2. Check role table:
   SELECT * FROM roles WHERE id = {role_id};
   Verify slug is 'operator'

3. Clear browser cache and login again

4. Check if middleware is working:
   php artisan route:list | grep 'print.*surat'
   Should show: 'role:operator,admin'
```

### Issue 2: Email Not Sending
```
Problem: Email not received even after print

Solution:
1. Check .env configuration:
   - MAIL_DRIVER must be set
   - MAIL_FROM_ADDRESS must be valid
   - If using SMTP: HOST, PORT, USERNAME, PASSWORD correct

2. Check if event listener registered:
   php artisan tinker
   $listeners = app('events')->listeners;
   Check if SuratDiprintOlehOperator is in listeners

3. Check logs:
   tail -f storage/logs/laravel.log
   Look for any error messages about email

4. Test email manually:
   php artisan mail:send --view="emails.surat_diprint"
   Or use: Mail::to('test@example.com')->send(new SuratDiprintMail(...));

5. If using async queue:
   Check if queue is running:
   php artisan queue:listen
```

### Issue 3: Notification Not Appearing
```
Problem: Notification created but not showing in UI

Solution:
1. Check if notification exists in database:
   SELECT * FROM notifications WHERE type = 'SURAT_DIPRINT';

2. Check if read_at is null:
   Notification::whereNull('read_at')->first();

3. Refresh page (Ctrl+Shift+R for hard refresh)

4. Check browser console for JS errors:
   Press F12 → Console tab
   Look for any red error messages

5. Check if route exists:
   php artisan route:list | grep notifications
```

### Issue 4: Print Button Not Tracking
```
Problem: Print dialog appears but success message doesn't

Solution:
1. Check browser console:
   F12 → Console → Look for JavaScript errors

2. Check Network tab:
   F12 → Network → Click print → Should see POST request
   Response should be: {"message": "Surat berhasil dicetak..."}

3. Verify route exists:
   php artisan route:list | grep track-surat

4. Check CSRF token:
   In page source (Ctrl+U), search for csrf-token
   Should find: <meta name="csrf-token" content="...">

5. Check browser permissions:
   Make sure fetch API is not blocked by browser
```

---

## 📊 Quick Verification Script

```bash
#!/bin/bash

echo "🔍 Quick Verification Script"
echo "============================"
echo ""

echo "1. Checking if Event class exists..."
test -f "app/Events/SuratDiprintOlehOperator.php" && echo "✅ Event found" || echo "❌ Event missing"

echo ""
echo "2. Checking if Listener class exists..."
test -f "app/Listeners/CreateNotificationSuratDiprint.php" && echo "✅ Listener found" || echo "❌ Listener missing"

echo ""
echo "3. Checking if Mail class exists..."
test -f "app/Mail/SuratDiprintMail.php" && echo "✅ Mail class found" || echo "❌ Mail class missing"

echo ""
echo "4. Checking if Email view exists..."
test -f "resources/views/emails/surat_diprint.blade.php" && echo "✅ Email view found" || echo "❌ Email view missing"

echo ""
echo "5. Checking routes..."
php artisan route:list | grep -q "print.*track-surat" && echo "✅ Track surat route found" || echo "❌ Route missing"

echo ""
echo "Done! ✅"
```

Save as `verify-implementation.sh` and run:
```bash
chmod +x verify-implementation.sh
./verify-implementation.sh
```

---

## 🎯 Full Testing Workflow (Complete)

```
STEP 1: Access Control ✓
├─ Pemohon tries to access → 403 error ✓
└─ Operator accesses → 200 OK ✓

STEP 2: Print Tracking ✓
├─ Click print button ✓
├─ Dialog appears ✓
├─ Success message shows ✓
└─ Backend processes ✓

STEP 3: Email Notification ✓
├─ Email sent to pemohon ✓
├─ Contains correct subject ✓
├─ Contains permohonan details ✓
└─ Received in inbox ✓

STEP 4: Database Record ✓
├─ Notification created ✓
├─ Type = SURAT_DIPRINT ✓
├─ user_id = pemohon ✓
├─ Activity logged ✓
└─ All fields populated ✓

STEP 5: Notification UI ✓
├─ Notification visible on page ✓
├─ Badge shows correctly ✓
├─ Can mark as read ✓
├─ Can delete ✓
└─ Can view permohonan ✓

ALL TESTS: ✅ PASS
```

---

## 📱 Quick Reference

### Important URLs (Testing)
```
Login: http://localhost/login
Notifications: http://localhost/notifications
Print Surat: http://localhost/print/{id}/surat
Approval Dashboard: http://localhost/approval/dashboard
```

### Important Commands
```bash
# Clear caches
php artisan cache:clear

# Test specific route
php artisan route:list | grep 'track-surat'

# Check listener
php artisan tinker
>>> app('events')->listeners

# View recent logs
tail -f storage/logs/laravel.log

# Queue processing (if async)
php artisan queue:listen
```

### Important Files to Check
```
Routes: routes/web.php
Controller: app/Http/Controllers/PrintController.php
Event: app/Events/SuratDiprintOlehOperator.php
Listener: app/Listeners/CreateNotificationSuratDiprint.php
Mail: app/Mail/SuratDiprintMail.php
View: resources/views/print/surat.blade.php
Notification View: resources/views/notifications/index.blade.php
```

---

## ✅ Deployment Checklist

Before going to production:

- [ ] All 5 tests passed locally
- [ ] Email configuration verified
- [ ] Database migrations run
- [ ] Cache cleared
- [ ] Assets compiled
- [ ] Error logs monitored
- [ ] Backup created
- [ ] Rollback plan ready
- [ ] Team notified
- [ ] Monitoring enabled

---

## 🎉 Success Criteria

Implementation is successful when:

✅ Pemohon cannot access print page  
✅ Operator can access print page  
✅ Print button works without errors  
✅ Email sent to pemohon  
✅ Notification created in database  
✅ Notification visible in UI  
✅ All notification actions work  
✅ Activity log entries created  
✅ No errors in logs  
✅ Performance acceptable  

---

**Ready to test? Start with Test 1! Good luck! 🚀**

