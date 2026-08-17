<x-filament-panels::page>
    @php
        $reseller = auth()->user();
        $branding = $reseller?->branding;
        $clients = $reseller?->clients()->where('type', \App\Enums\UserType::User)->latest()->limit(10)->get() ?? collect();
    @endphp

    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-950 dark:text-white">
                        {{ $branding?->brand_name ?? $reseller?->company_name ?? $reseller?->name }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage your clients, white-label brand and calling operation from one portal.
                    </p>
                </div>
                @if ($branding?->logo_url)
                    <img src="{{ $branding->logo_url }}" alt="{{ $branding->brand_name }}" class="max-h-12 max-w-48 object-contain">
                @endif
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Clients</div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $reseller?->clients()->where('type', \App\Enums\UserType::User)->count() ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Active Clients</div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ $reseller?->clients()->where('type', \App\Enums\UserType::User)->where('status', \App\Enums\UserStatus::Approved)->count() ?? 0 }}</div>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Brand Domain</div>
                <div class="mt-2 truncate text-lg font-semibold text-gray-950 dark:text-white">{{ $branding?->custom_domain ?? ($branding?->subdomain ? $branding->subdomain.'.yourdomain.com' : 'Not configured') }}</div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h3 class="font-semibold text-gray-950 dark:text-white">Recent Clients</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500 dark:bg-gray-800/50 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Created</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse ($clients as $client)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-950 dark:text-white">{{ $client->name }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $client->email }}</td>
                                <td class="px-6 py-4">{{ $client->status?->getLabel() ?? $client->status }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $client->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No clients have been added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>
