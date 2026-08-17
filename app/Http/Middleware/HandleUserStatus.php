<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class HandleUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Filament::auth()->user();

        if ($user) {
            switch ($user->status) {
                case UserStatus::Pending:

                    $allowedRoutes = [
                        'filament.user.account.pending',
                        'filament.user.auth.logout',
                        'filament.user.auth.email-verification.*',
                    ];

                    if (! $request->routeIs($allowedRoutes)) {
                        return to_route('filament.user.account.pending');
                    }
                    break;

                case UserStatus::Rejected:

                    $allowedRoutes = [
                        'filament.user.account.rejected',
                        'filament.user.auth.logout',
                        'filament.user.auth.email-verification.*',
                    ];

                    if (! $request->routeIs($allowedRoutes)) {
                        return to_route('filament.user.account.rejected');
                    }
                    break;

                case UserStatus::Banned:

                    $allowedRoutes = [
                        'filament.user.account.banned',
                        'filament.user.auth.logout',
                        'filament.user.auth.email-verification.*',
                    ];

                    if (! $request->routeIs($allowedRoutes)) {
                        return to_route('filament.user.account.banned');
                    }
                    break;

                default:
                    if ($request->routeIs([
                        'filament.user.account.pending',
                        'filament.user.account.rejected',
                        'filament.user.account.banned',
                    ])) {
                        return redirect()->to(Dashboard::getUrl(panel: 'user'));
                    }

                    $next($request);
                    break;
            }
        }

        return $next($request);
    }
}
