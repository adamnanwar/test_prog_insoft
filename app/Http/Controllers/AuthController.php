<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Tampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login dengan kredensial hardcode
    public function login(Request $request)
    {
        if ($request->username === 'admin' && $request->password === 'admin123') {
            session(['is_logged_in' => true]);
            return redirect('/');
        }

        return back()->withErrors(['msg' => 'Username atau password salah.']);
    }

    // Logout dan redirect ke login
    public function logout()
    {
        Session::flush();
        return redirect('/login');
    }
}
