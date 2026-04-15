<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoginHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::where('user_id', auth()->id())
            ->whereIn('action', ['LOGIN', 'LOGOUT'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('profile.login-history', compact('logs'));
    }
}
