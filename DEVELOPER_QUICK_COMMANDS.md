# 💻 DEVELOPER QUICK COMMANDS

Kumpulan command praktis untuk developer

---

## 🚀 Quick Start Commands

### Clear Everything & Start Fresh
```bash
php artisan cache:clear && \
php artisan config:clear && \
php artisan view:clear && \
php artisan route:clear && \
composer dump-autoload -o
```

### Fast Setup (All-in-one)
```bash
php artisan cache:clear && \
php artisan config:cache && \
php artisan route:cache && \
composer dump-autoload -o && \
echo "✅ Setup complete!"
```

### Verify Implementation
```bash
# Check all required files exist
echo "=== Checking implementation files ===" && \
test -f "app/Events/SuratDiprintOlehOperator.php" && echo "✅ Event" || echo "❌ Event missing" && \
test -f "app/Listeners/CreateNotificationSuratDiprint.php" && echo "✅ Listener" || echo "❌ Listener missing" && \
test -f "app/Mail/SuratDiprintMail.php" && echo "✅ Mail" || echo "❌ Mail missing" && \
test -f "resources/views/emails/surat_diprint.blade.php" && echo "✅ Email view" || echo "❌ Email view missing"
```

---

## 🧪 Testing Commands

### Run All Tests
```bash
php artisan test
```

### Test Specific Feature
```bash
php artisan test tests/Feature/PrintSuratTest.php
```

### Database Testing - Create Test Data
```bash
php artisan tinker

# Create test users
$pemohon = User::factory()->pemohon()->create();
$operator = User::factory()->operator()->create();

# Create approved permohonan
$permohonan = PermohonanReklame::factory()->approved()->create(['user_id' => $pemohon->id]);

# Check
User::where('role_id', 4)->get();  // pemohon users
PermohonanReklame::where('status', 'Disetujui')->get();  // approved
```

### Check Event Registration
```bash
php artisan tinker

# Verify listener registered
$listeners = app('events')->listeners;
collect($listeners)->filter(function($items, $event) {
    return str_contains($event, 'SuratDiprint');
})->dump();
```

### Test Email
```bash
php artisan tinker

# Test send email
Mail::to('test@example.com')->send(new \App\Mail\SuratDiprintMail(
    \App\Models\PermohonanReklame::first(),
    'Test Operator'
));
```

### Check Notifications
```bash
php artisan tinker

# View recent notifications
Notification::latest()->limit(5)->get();

# Check specific type
Notification::where('type', 'SURAT_DIPRINT')->get();

# Count unread
Notification::whereNull('read_at')->count();
```

---

## 📋 Route Commands

### List All Routes
```bash
php artisan route:list
```

### Find Print Surat Routes
```bash
php artisan route:list | grep -i "surat\|print"
```

### Find Track Route
```bash
php artisan route:list | grep "track-surat"
```

### Check Route Details
```bash
php artisan route:list -v  # Verbose (includes middleware)
```

---

## 🔍 Debugging Commands

### Monitor Logs (Real-time)
```bash
tail -f storage/logs/laravel.log
```

### Search Logs for Errors
```bash
grep -i "error\|exception" storage/logs/laravel.log | tail -20
```

### Search Logs for Print Tracking
```bash
grep -i "surat\|print_surat" storage/logs/laravel.log
```

### Check PHP Errors
```bash
php artisan log:tail  # Laravel 8+
```

### Debug Mode
```bash
# Enable debug in .env
APP_DEBUG=true

# Then check logs for detailed error info
tail -f storage/logs/laravel.log
```

---

## 💾 Database Commands

### Database Inspection
```bash
php artisan tinker

# Inspect notifications table
DB::table('notifications')->latest()->limit(10)->get();

# Inspect activity logs
DB::table('activity_logs')->where('action', 'PRINT_SURAT')->get();

# Count records
DB::table('notifications')->where('type', 'SURAT_DIPRINT')->count();

# Check specific user
DB::table('notifications')->where('user_id', 2)->get();
```

### Database Cleanup
```bash
php artisan tinker

# Delete test notifications
Notification::where('type', 'SURAT_DIPRINT')->delete();

# Delete activity logs
ActivityLog::where('action', 'PRINT_SURAT')->delete();

# Truncate (careful!)
DB::table('notifications')->truncate();
```

---

## 🔧 Configuration Commands

### Show Config
```bash
php artisan config:show
php artisan config:show mail  # Mail config
```

### List Available Mails
```bash
php artisan make:mail --help
```

### List Available Events
```bash
php artisan make:event --help
```

### Check Environment
```bash
php artisan env
```

---

## 📧 Email Testing Commands

### Test with Mailtrap
```bash
# In .env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password

# Test
php artisan tinker
Mail::to('test@example.com')->send(new \App\Mail\SuratDiprintMail(...));
```

### Test with MailHog
```bash
# Start MailHog (in separate terminal)
./mailhog  # or MailHog.exe on Windows

# In .env
MAIL_DRIVER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025

# Access UI: http://localhost:8025
```

### Test with Log
```bash
# In .env
MAIL_DRIVER=log

# Then check: storage/logs/laravel.log
tail -f storage/logs/laravel.log
```

---

## 🎯 Development Workflow Commands

### Start Development Server
```bash
php artisan serve

# On specific port
php artisan serve --port=8080

# On specific host
php artisan serve --host=0.0.0.0 --port=8000
```

### Queue Processing (if using async)
```bash
php artisan queue:listen

# Process single job
php artisan queue:work --once

# Monitor queue
php artisan queue:failed
```

### Build Assets (if needed)
```bash
npm run dev

# For production
npm run build
```

### Cache Optimization
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache  # Laravel 8.1+
```

---

## 🐛 Troubleshooting Commands

### Check PHP Version
```bash
php --version
```

### Check Laravel Version
```bash
php artisan --version
```

### Check Composer Packages
```bash
composer show
```

### Verify Autoload
```bash
composer dump-autoload -o
```

### Check Middleware
```bash
php artisan route:list -v | grep "print.*surat"
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan event:clear  # Laravel 8.1+
php artisan optimize:clear
```

### Full Reset
```bash
# Warning: This resets everything!
php artisan cache:clear && \
php artisan config:clear && \
php artisan view:clear && \
php artisan route:clear && \
php artisan event:clear && \
php artisan optimize:clear && \
composer dump-autoload -o && \
echo "✅ Full reset complete!"
```

---

## 📊 Analysis Commands

### Count Lines of Code
```bash
wc -l app/Events/SuratDiprintOlehOperator.php
wc -l app/Listeners/CreateNotificationSuratDiprint.php
wc -l app/Mail/SuratDiprintMail.php

# Count all modified files
wc -l app/Http/Controllers/PrintController.php
wc -l routes/web.php
wc -l resources/views/print/surat.blade.php
```

### View File Sizes
```bash
du -h app/Events/SuratDiprintOlehOperator.php
du -h app/Listeners/*.php
du -h app/Mail/SuratDiprintMail.php
```

### Check File Syntax
```bash
php -l app/Events/SuratDiprintOlehOperator.php
php -l app/Listeners/CreateNotificationSuratDiprint.php
php -l app/Mail/SuratDiprintMail.php
```

---

## 🔐 Security Commands

### Generate App Key (if needed)
```bash
php artisan key:generate
```

### Generate Encryption Key
```bash
php artisan key:generate --show
```

### Check Security Issues
```bash
# Run security check
composer audit

# Check packages
composer outdated
```

---

## 📚 Documentation Commands

### Generate API Documentation
```bash
# If using Laravel API
php artisan route:list --format=json > routes.json
```

### View Configuration
```bash
php artisan config:list  # Shows all config
php artisan config:show mail  # Specific config
```

---

## 🚀 Deployment Commands

### Pre-Deployment
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache  # Laravel 8.1+
composer dump-autoload -o
```

### Post-Deployment
```bash
# Clear caches to verify
php artisan cache:clear
php artisan config:clear

# Restart queue (if using)
php artisan queue:restart
```

---

## 💡 Useful One-Liners

### Check if All Routes Work
```bash
php artisan route:list | awk '{print $2}' | sort | uniq -c
```

### Find Routes by Method
```bash
php artisan route:list | grep POST
php artisan route:list | grep GET
```

### Count Notifications by Type
```bash
php artisan tinker
Notification::groupBy('type')->selectRaw('type, count(*) as count')->get()
```

### Find Unread Notifications
```bash
php artisan tinker
Notification::whereNull('read_at')->count()
```

### Reset User Email (for testing)
```bash
php artisan tinker
User::find(1)->update(['email' => 'test@example.com'])
```

---

## 📖 Quick Reference

| Task | Command |
|------|---------|
| Start server | `php artisan serve` |
| Clear caches | `php artisan cache:clear` |
| Run migrations | `php artisan migrate` |
| Run tinker | `php artisan tinker` |
| List routes | `php artisan route:list` |
| Check logs | `tail -f storage/logs/laravel.log` |
| Run tests | `php artisan test` |
| Create seeder | `php artisan make:seeder` |
| Create migration | `php artisan make:migration` |

---

## 🎯 Common Development Scenarios

### Scenario 1: Setup New Environment
```bash
git clone ...
cd project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Scenario 2: Test New Feature
```bash
# Make sure files exist
test -f app/Events/SuratDiprintOlehOperator.php || echo "File missing"

# Clear caches
php artisan cache:clear

# Test
php artisan tinker
# ... run tests ...
```

### Scenario 3: Deploy to Production
```bash
# Pre-deploy
php artisan config:cache
php artisan route:cache
composer dump-autoload -o

# Deploy files

# Post-deploy
php artisan cache:clear
php artisan config:clear
```

---

## 🆘 Emergency Commands

### If Application Breaks
```bash
# 1. Clear everything
php artisan optimize:clear

# 2. Check logs
tail -f storage/logs/laravel.log

# 3. Verify files
php -l app/Events/SuratDiprintOlehOperator.php

# 4. Restart
php artisan serve
```

### If Routes Not Found
```bash
php artisan route:clear
php artisan route:cache
php artisan serve
```

### If Email Not Working
```bash
# Test configuration
php artisan config:show mail

# Send test email
php artisan tinker
Mail::raw('test', fn($m) => $m->to('test@example.com'))
```

---

**Copy-paste these commands for quick development!** 💻

