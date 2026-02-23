<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    public function logout(Request $request)
    {
        // 🔐 Logout from ALL guards (safe approach)
        foreach (['admin', 'manager', 'cashier', 'web'] as $guard) {
            Auth::guard($guard)->logout();
        }

        // 🔄 Destroy session completely
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}