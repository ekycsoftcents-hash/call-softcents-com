<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\UserStatus;
use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\ResellerBranding;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

final class ResellerPortalController extends Controller
{
    public function branding(Request $request): JsonResponse
    {
        $reseller = $this->reseller($request);

        return response()->json([
            'data' => $reseller->branding,
        ]);
    }

    public function updateBranding(Request $request): JsonResponse
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
            'is_active' => ['sometimes', 'boolean'],
            'settings' => ['nullable', 'array'],
        ]);

        $branding = $reseller->branding()->updateOrCreate(
            ['reseller_id' => $reseller->id],
            $data
        );

        return response()->json(['data' => $branding]);
    }

    public function clients(Request $request): JsonResponse
    {
        $reseller = $this->reseller($request);

        return response()->json([
            'data' => $reseller->clients()
                ->where('type', UserType::User)
                ->latest()
                ->paginate($request->integer('per_page', 25)),
        ]);
    }

    public function createClient(Request $request): JsonResponse
    {
        $reseller = $this->reseller($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:40'],
            'password' => ['required', 'string', 'min:8'],
            'company_name' => ['nullable', 'string', 'max:255'],
        ]);

        $client = User::create([
            ...$data,
            'password' => Hash::make($data['password']),
            'reseller_id' => $reseller->id,
            'type' => UserType::User,
            'status' => UserStatus::Approved,
        ]);

        return response()->json(['data' => $client], 201);
    }

    private function reseller(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User && $user->isReseller(), 403, 'Only resellers can access this portal.');

        return $user;
    }
}
