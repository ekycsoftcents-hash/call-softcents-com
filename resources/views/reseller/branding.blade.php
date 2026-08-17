@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"><a href="{{ route('reseller.dashboard') }}" class="font-semibold">← Reseller Portal</a><span class="text-sm text-slate-500">White-label settings</span></div></header>
        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))<div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
            @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm"><h1 class="text-2xl font-bold">White-label Branding</h1><p class="mt-1 text-sm text-slate-600">Configure the brand your clients will see.</p>
                <form method="POST" action="{{ route('reseller.branding.update') }}" class="mt-6 grid gap-5 sm:grid-cols-2">@csrf @method('PUT')
                    <label class="text-sm font-medium">Brand name<input name="brand_name" value="{{ old('brand_name', $branding->brand_name) }}" required class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Logo URL<input name="logo_url" type="url" value="{{ old('logo_url', $branding->logo_url) }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Favicon URL<input name="favicon_url" type="url" value="{{ old('favicon_url', $branding->favicon_url) }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Support email<input name="support_email" type="email" value="{{ old('support_email', $branding->support_email) }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Support phone<input name="support_phone" value="{{ old('support_phone', $branding->support_phone) }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Custom domain<input name="custom_domain" value="{{ old('custom_domain', $branding->custom_domain) }}" placeholder="portal.example.com" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Subdomain<input name="subdomain" value="{{ old('subdomain', $branding->subdomain) }}" placeholder="your-brand" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Primary color<input name="primary_color" value="{{ old('primary_color', $branding->primary_color ?? '#2563eb') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <label class="text-sm font-medium">Secondary color<input name="secondary_color" value="{{ old('secondary_color', $branding->secondary_color ?? '#0f172a') }}" class="mt-1 w-full rounded-lg border-slate-300"></label>
                    <div class="sm:col-span-2"><button class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Save Branding</button></div>
                </form>
            </div>
        </main>
    </div>
@endsection
