# Troubleshoot 403 Forbidden Issue - Step-by-Step Guide

## Prerequisites
Make sure you are logged in as a staff member with one of these roles:
- Operator (operator@dpmptsp.local)
- Kepala Seksi (kepala.seksi@dpmptsp.local)
- Kepala Bidang (kepala.bidang@dpmptsp.local)  
- Admin (admin@dpmptsp.local)

## Step 1: Check the Log File
The application now logs detailed information about access attempts. Follow these steps:

1. Open terminal/command prompt in the project directory
2. Run this command to see the latest logs:
   ```
   Get-Content storage/logs/laravel.log -Tail 50
   ```
3. Look for messages containing:
   - `CheckRole Middleware` - shows middleware checks
   - `DocumentRequirementController::viewForStaff` - shows if controller was reached

## Step 2: Test with a Permohonan That Exists
You need to know a valid Permohonan ID. Check the database:

```bash
php artisan tinker --execute="echo 'Permohonan IDs:'; App\Models\PermohonanReklame::pluck('id','nomor_registrasi')->each(function(\$id, \$nomor) { echo \$nomor . ' => ID: ' . \$id . PHP_EOL; });"
```

## Step 3: Try to Access the Route
Replace `{ID}` with an actual Permohonan ID from step 2:

```
http://localhost:8000/permohonan/{ID}/requirements/check
```

For example: `http://localhost:8000/permohonan/1/requirements/check`

## Step 4: Check the Logs Again
After accessing the URL, run the log command again:

```
Get-Content storage/logs/laravel.log -Tail 100
```

## Expected Results

### If Access Should Be Granted (Operator, Kepala Seksi, etc.):
Look for log lines like:
```
[2024-XX-XX XX:XX:XX] local.DEBUG: CheckRole Middleware - User ID: 2, Email: operator@dpmptsp.local
[2024-XX-XX XX:XX:XX] local.DEBUG: CheckRole Middleware - User role_id: 2, Role slug: operator
[2024-XX-XX XX:XX:XX] local.DEBUG: CheckRole Middleware - hasAnyRole result: TRUE
[2024-XX-XX XX:XX:XX] local.DEBUG: CheckRole Middleware - Access granted
[2024-XX-XX XX:XX:XX] local.DEBUG: DocumentRequirementController::viewForStaff - Called for permohonan ID: 1
[2024-XX-XX XX:XX:XX] local.DEBUG: DocumentRequirementController::viewForStaff - Access granted, fetching requirements
```

If you see these, the page should work with a 200 status code.

### If Access is Denied (403 Error):
Look for one of these patterns:

**Pattern A: Middleware denied access**
```
[2024-XX-XX XX:XX:XX] local.DEBUG: CheckRole Middleware - User role_id: NULL, Role slug: NULL
[2024-XX-XX XX:XX:XX] local.WARNING: CheckRole Middleware - Access denied for user [email] (role: NULL)
```

**Pattern B: User not authenticated**
```
[2024-XX-XX XX:XX:XX] local.WARNING: CheckRole Middleware - User not authenticated
```

**Pattern C: Controller denied access**
```
[2024-XX-XX XX:XX:XX] local.DEBUG: DocumentRequirementController::viewForStaff - User role_slug: NULL
[2024-XX-XX XX:XX:XX] local.WARNING: DocumentRequirementController::viewForStaff - Access denied for user [email]
```

## What Each Pattern Means

- **Pattern A or C with role_slug: NULL** → User has no role assigned in database
- **Pattern B** → Session not working or user logged out
- **Pattern A or C with wrong role_slug** → User has wrong role  (e.g., has 'pemohon' but needs 'operator')

## How to Fix Based on Your Pattern

### If Pattern A/C with role_slug: NULL
Run this in tinker to check:
```
php artisan tinker
App\Models\User::where('email', 'operator@dpmptsp.local')->first();
# Check if role_id is NULL
```

If role_id is NULL, assign the correct role using SQL or seeder.

### If Pattern B
Clear session and log in again:
```
php artisan cache:clear
php artisan config:clear
```

### If Pattern A/C with wrong role
The user needs to have the correct role in the database. Check which role ID they should have:
```
php artisan tinker
App\Models\Role::all(); # See all roles and their IDs
App\Models\User::where('email', 'their@email.com')->first(); # Check their role_id
```

## Next Steps
1. Follow Step 1-4 above
2. Share the log output or the pattern you see
3. Tell me exactly which URL you tried to access
4. Tell me which user account you're logged in with

This data will help identify the exact root cause!
