# Testing the 403 Fix

## Quick Test Steps

### 1. Start your Laravel application
Make sure you have the app running on `http://localhost:8000` (or your configured URL)

### 2. Test 1: Check authentication status
Visit: `http://localhost:8000/test-role-check`

**Expected output:**
```
=== Role Check Diagnostic ===

User ID: 2
User Email: operator@dpmptsp.local
User role_id (from DB): 2
Role relation loaded: YES
Role object: LOADED
Role ID: 2
Role Name: Operator
Role Slug: operator

Testing hasAnyRole with: operator, kepala_seksi, kepala_bidang, admin
Result: TRUE (PASS)
```

If this shows `Result: FALSE (FAIL)`, the user doesn't have the right role assigned.

### 3. Test 2: Check if role middleware works
Visit: `http://localhost:8000/test-role-protected`

**Expected output:**
```
SUCCESS! You passed the role:operator,kepala_seksi,kepala_bidang,admin middleware!
```

If you get a 403 error, the middleware is blocking it.

### 4. Test 3: Access the actual document checking page
Visit: `http://localhost:8000/permohonan/1/requirements/check`

(Replace `1` with an actual permohonan ID that exists in your database)

**Expected output:**
- Page should load with the document requirements list
- Status code should be 200

If you get a 403 error, check the logs (see step 5).

### 5. Check the application logs
Run this command to see the last 100 log lines:

```powershell
Get-Content storage/logs/laravel.log -Tail 100
```

Look for messages like:
- `CheckRole: User authenticated`
- `CheckRole: Role check`
- `CheckRole: Access granted` or `CheckRole: Access denied`

These logs will tell you exactly where the 403 is coming from.

## Troubleshooting

### If Test 1 shows `Result: FALSE (FAIL)`
The user doesn't have a valid role. Fix in database:
```
php artisan tinker
$user = App\Models\User::find(2);
$user->role_id = 2; // Operator role
$user->save();
```

### If Test 2 or 3 shows 403
Check the logs and look for `CheckRole: Access denied` message. It will show:
- What role the user has
- What roles are required
- Why access was denied

### If tests pass but page still doesn't load
Clear cache and config:
```
php artisan cache:clear
php artisan config:clear
```

Then try again.

## Report Back
If you still get 403 after trying these tests, please tell me:
1. What does Test 1 show?
2. What does Test 2 show?
3. What does Test 3 show?
4. What do the logs show? (last 20 lines that mention "CheckRole" or "DocumentRequirement")
5. Which user are you logged in as?
6. What is the permohonan ID you're trying to access?
