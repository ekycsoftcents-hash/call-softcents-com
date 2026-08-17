<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Models\ResellerBranding;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

final class ResellerPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $reseller = $this->reseller($request);

        return view('reseller.dashboard', [
            'reseller' => $reseller,
            'branding' => $reseller->branding,
            'clients' => $reseller->clients()->where('type', UserType::User)->latest()->paginate(15),
        ]);
    }

    public function branding(Request $request): View
    {
        $reseller = $this->reseller($request);

        return view('reseller.branding', [
            'reseller' => $reseller,
            'branding' => $reseller->branding ?? new ResellerBranding(['brand_name' => $reseller->company_name ?? $reseller->name]),
        ]);
    }

    public function updateBranding(Request $request): RedirectResponse
    {
        $reseller = $this->reseller($request);
        $data = $request->validate([
            'brand_name' => ['required', 'string', 'max:150'],
            'logo_url' => ['nullable', 'url', 'max:2048'],
            'favicon_url' => ['nullable', 'url', 'max:2048'],
            'primary_color' => ['required', 'string', 'max:32'],
            'secondary_color' => ['required', 'string', 'max:32'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'support_phone' => ['nullable', 'string', 'max:40'],
            'custom_domain' => ['nullable', 'string', 'max:255', Rule::unique('reseller_brandings', 'custom_domain')->ignore($reseller->branding?->id)],
            'subdomain' => ['nullable', 'alpha_dash', 'max:80', Rule::unique('reseller_brandings', 'subdomain')->ignore($reseller->branding?->id)],
        ]);

        $reseller->branding()->updateOrCreate(['reseller_id' => $reseller->id], $data);

        return Redirect::route('reseller.branding')->with('success', 'White-label branding updated.');
    }

    public function storeClient(Request $request): RedirectResponse
    {
        $reseller = $this->reseller($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'reseller_id' => $reseller->id,
            'type' => UserType::User,
            'status' => UserStatus::Approved,
        ]);

        return Redirect::route('reseller.dashboard')->with('success', 'Client created successfully.');
    }

    private function reseller(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isReseller(), 403);

        return $user;
    }
}
