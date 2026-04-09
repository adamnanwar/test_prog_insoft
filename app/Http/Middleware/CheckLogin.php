<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    // Redirect ke login jika belum login
    public function handle(Request $request, Closure $next)
    {
        if (!session('is_logged_in')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
