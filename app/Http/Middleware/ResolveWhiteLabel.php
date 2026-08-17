<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ResellerBranding;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

final class ResolveWhiteLabel
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $branding = ResellerBranding::query()
            ->where('is_active', true)
            ->where(function ($query) use ($host): void {
                $query->whereRaw('LOWER(custom_domain) = ?', [$host]);

                $subdomain = str($host)->before('.')->value();
                if ($subdomain !== $host) {
                    $query->orWhereRaw('LOWER(subdomain) = ?', [$subdomain]);
                }
            })
            ->first();

        if (! $branding && auth()->check()) {
            $user = auth()->user();
            $reseller = $user->isReseller() ? $user : $user->reseller;
            $branding = $reseller?->branding;
        }

        if ($branding) {
            app()->instance(ResellerBranding::class, $branding);
            View::share('whiteLabelBranding', $branding);
        }

        return $next($request);
    }
}
