<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateDerniereActivite
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            Auth::user()->touchActivite();
        }

        return $next($request);
    }
}
