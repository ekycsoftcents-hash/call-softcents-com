<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\Call;
use App\Models\Caller;
use App\Models\Deposit;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

final class AdminPortalController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'clients' => User::where('type', UserType::User)->count(),
                'resellers' => User::where('type', UserType::Reseller)->count(),
                'calls' => Call::count(),
                'servers' => Server::where('enabled', true)->count(),
            ],
            'recentCalls' => Call::with(['user', 'campaign'])->latest()->limit(10)->get(),
        ]);
    }

    public function resellers(): View
    {
        return view('admin.resellers', ['resellers' => User::where('type', UserType::Reseller)->latest()->paginate(20)]);
    }

    public function storeReseller(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'type' => UserType::Reseller,
            'status' => UserStatus::Approved,
        ]);

        return Redirect::route('admin.resellers')->with('success', 'Reseller created successfully.');
    }

    public function clients(): View
    {
        return view('admin.clients', ['clients' => User::where('type', UserType::User)->with('reseller')->latest()->paginate(25)]);
    }

    public function servers(): View
    {
        return view('admin.table', [
            'title' => 'FusionPBX Servers',
            'columns' => ['Name', 'FusionPBX Domain', 'Event Socket Host', 'Status'],
            'rows' => Server::latest()->paginate(25)->through(fn (Server $server) => [$server->name, $server->fusionpbx_domain, $server->freeswitch_event_socket_host, $server->enabled ? 'Enabled' : 'Disabled']),
        ]);
    }

    public function callers(): View
    {
        return view('admin.table', [
            'title' => 'Caller Profiles',
            'columns' => ['Caller', 'Number', 'Gateway', 'Server'],
            'rows' => Caller::with('server')->latest()->paginate(25)->through(fn (Caller $caller) => [$caller->caller_name, $caller->caller_number, $caller->trunk_name, $caller->server?->name]),
        ]);
    }

    public function calls(): View
    {
        return view('admin.table', [
            'title' => 'Call Records',
            'columns' => ['Destination', 'Client', 'Status', 'Created'],
            'rows' => Call::with('user')->latest()->paginate(25)->through(fn (Call $call) => [$call->phone_number, $call->user?->name, $call->status?->value ?? $call->status, optional($call->created_at)->format('Y-m-d H:i')]),
        ]);
    }

    public function deposits(): View
    {
        return view('admin.table', [
            'title' => 'Deposits',
            'columns' => ['Client', 'Amount', 'Status', 'Created'],
            'rows' => Deposit::with('user')->latest()->paginate(25)->through(fn (Deposit $deposit) => [$deposit->user?->name, $deposit->amount, $deposit->status?->value ?? $deposit->status, optional($deposit->created_at)->format('Y-m-d H:i')]),
        ]);
    }
}
