@extends('layouts.app')

@section('content')
    @php($brand = $branding)
    <div class="min-h-screen" style="--brand-primary: {{ $brand?->primary_color ?? '#2563eb' }}">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('reseller.dashboard') }}" class="flex items-center gap-3 font-semibold">
                    @if ($brand?->logo_url)
                        <img src="{{ $brand->logo_url }}" alt="{{ $brand->brand_name }}" class="max-h-9 max-w-40 object-contain">
                    @endif
                    <span>{{ $brand?->brand_name ?? $reseller->company_name ?? $reseller->name }}</span>
                </a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('reseller.branding') }}" class="text-slate-600 hover:text-slate-950">Branding</a>
                    <span class="text-slate-500">{{ $reseller->name }}</span>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button class="rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Logout</button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            <div>
                <h1 class="text-2xl font-bold tracking-tight">Reseller Portal</h1>
                <p class="mt-1 text-sm text-slate-600">Manage clients and your white-label calling brand.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Total Clients</div><div class="mt-2 text-3xl font-bold">{{ $reseller->clients()->where('type', \App\Enums\UserType::User)->count() }}</div></div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Active Clients</div><div class="mt-2 text-3xl font-bold">{{ $reseller->clients()->where('type', \App\Enums\UserType::User)->where('status', \App\Enums\UserStatus::Approved)->count() }}</div></div>
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><div class="text-sm text-slate-500">Available Balance</div><div class="mt-2 text-3xl font-bold">{{ number_format((float) $reseller->balance, 2) }}</div></div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
                <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><h2 class="font-semibold">Clients</h2><span class="text-sm text-slate-500">{{ $clients->total() }} total</span></div>
                    <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Status</th></tr></thead><tbody class="divide-y divide-slate-100">
                        @forelse ($clients as $client)
                            <tr><td class="px-5 py-4 font-medium">{{ $client->name }}</td><td class="px-5 py-4 text-slate-600">{{ $client->email }}</td><td class="px-5 py-4">{{ $client->status?->getLabel() ?? $client->status }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-10 text-center text-slate-500">No clients yet.</td></tr>
                        @endforelse
                    </tbody></table></div>
                    <div class="border-t border-slate-200 px-5 py-3">{{ $clients->links() }}</div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="font-semibold">Add Client</h2><form method="POST" action="{{ route('reseller.clients.store') }}" class="mt-4 space-y-3">@csrf
                    <input name="name" value="{{ old('name') }}" required placeholder="Full name" class="w-full rounded-lg border-slate-300 text-sm">
                    <input name="email" type="email" value="{{ old('email') }}" required placeholder="Email" class="w-full rounded-lg border-slate-300 text-sm">
                    <input name="phone" value="{{ old('phone') }}" placeholder="Phone" class="w-full rounded-lg border-slate-300 text-sm">
                    <input name="company_name" value="{{ old('company_name') }}" placeholder="Company" class="w-full rounded-lg border-slate-300 text-sm">
                    <input name="password" type="password" required minlength="8" placeholder="Temporary password" class="w-full rounded-lg border-slate-300 text-sm">
                    <button class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Create Client</button>
                </form></section>
            </div>
        </main>
    </div>
@endsection
