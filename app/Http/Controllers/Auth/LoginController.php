<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // ✅ Validate input
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        // ✅ Step 1: Login using web guard (temporary)
        if (! Auth::guard('web')->attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Invalid credentials',
            ]);
        }

        // ✅ Step 2: Regenerate session (VERY IMPORTANT)
        $request->session()->regenerate();

        $user = Auth::guard('web')->user();
        

        // ✅ Step 3: Logout from web guard
        Auth::guard('web')->logout();

        // ✅ Step 4: Login into role-based guard
        switch ($user->role) {
            case 'admin':
                Auth::guard('admin')->login($user);
                return redirect()->intended('/admin');

            case 'manager':
                Auth::guard('manager')->login($user);
                return redirect()->intended('/manager');

            case 'cashier':
                Auth::guard('cashier')->login($user);
                return redirect()->intended('/cashier');

            default:
                abort(403, 'Unauthorized role');
        }
    }
}