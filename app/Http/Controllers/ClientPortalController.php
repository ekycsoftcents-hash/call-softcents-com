<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Caller;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

final class ClientPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $this->client($request);

        return view('client.dashboard', [
            'user' => $user,
            'callers' => $this->callerQuery($user)->get(),
            'recentCalls' => $user->calls()->latest()->limit(10)->get(),
            'campaigns' => $user->campaigns()->latest()->limit(10)->get(),
        ]);
    }

    public function calls(Request $request): View
    {
        $user = $this->client($request);

        return view('client.list', [
            'user' => $user,
            'title' => 'My Calls',
            'columns' => ['Destination', 'Status', 'Created'],
            'rows' => $user->calls()->latest()->paginate(25)->through(fn (Call $call) => [$call->phone_number, $call->status?->value ?? $call->status, optional($call->created_at)->format('Y-m-d H:i')]),
        ]);
    }

    public function campaigns(Request $request): View
    {
        $user = $this->client($request);

        return view('client.list', [
            'user' => $user,
            'title' => 'My Campaigns',
            'columns' => ['Name', 'Status', 'Created'],
            'rows' => $user->campaigns()->latest()->paginate(25)->through(fn (Campaign $campaign) => [$campaign->title, $campaign->status?->value ?? $campaign->status, optional($campaign->created_at)->format('Y-m-d H:i')]),
        ]);
    }

    public function callers(Request $request): View
    {
        $user = $this->client($request);

        return view('client.callers', [
            'user' => $user,
            'callers' => $this->callerQuery($user)->paginate(15),
        ]);
    }

    public function updateCaller(Request $request, Caller $caller): RedirectResponse
    {
        $user = $this->client($request);
        abort_unless($this->callerQuery($user)->whereKey($caller->id)->exists(), 404);

        $data = $request->validate([
            'caller_name' => ['required', 'string', 'max:150'],
            'caller_number' => ['required', 'string', 'max:40'],
            'sip_username' => ['required', 'string', 'max:150'],
            'sip_password' => ['nullable', 'string', 'max:255'],
            'sip_domain' => ['required', 'string', 'max:255'],
            'sip_port' => ['required', 'integer', 'between:1,65535'],
            'sip_context' => ['nullable', 'string', 'max:100'],
        ]);

        if (blank($data['sip_password'])) {
            unset($data['sip_password']);
        }

        $caller->update($data);

        return Redirect::route('client.callers')->with('success', 'Caller profile updated.');
    }

    private function client(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isClient(), 403);

        return $user;
    }

    private function callerQuery(User $user)
    {
        return Caller::query()->whereHas('users', fn ($query) => $query->whereKey($user->id));
    }
}
