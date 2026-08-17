<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect('/login');
        }

        if ($request->user()->isReseller()) {
            return redirect('/reseller');
        }

        if ($request->user()->isUser()) {
            return $next($request);
        }

        return redirect('/admin');
    }
}
