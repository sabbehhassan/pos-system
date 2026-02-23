<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Step 1: Login via web guard
        if (! Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors([
                'email' => 'Invalid credentials',
            ]);
        }

        // Step 2: Regenerate session
        $request->session()->regenerate();

        $user = Auth::user();

        // Step 3: Logout ALL guards safely
        Auth::logout();

        // Step 4: Login into role-based guard
        switch (strtolower($user->role)) {
            case 'admin':
                Auth::guard('admin')->login($user);
                return redirect()->to('/admin');

            case 'manager':
                Auth::guard('manager')->login($user);
                return redirect()->to('/manager');

            case 'cashier':
                Auth::guard('cashier')->login($user);
                return redirect()->to('/cashier');

            default:
                return redirect('/login');
        }
    }
}