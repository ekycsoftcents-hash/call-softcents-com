@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50">
        <header class="border-b border-slate-200 bg-white"><div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"><a href="{{ route('admin.dashboard') }}" class="text-lg font-bold">Admin Portal</a><div class="flex items-center gap-4 text-sm"><span class="text-slate-500">{{ auth()->user()->name }}</span><form method="POST" action="{{ url('/logout') }}">@csrf<button class="rounded-lg border border-slate-200 px-3 py-2 hover:bg-slate-50">Logout</button></form></div></div></header>
        <div class="mx-auto flex max-w-7xl gap-8 px-4 py-8 sm:px-6 lg:px-8">
            <aside class="hidden w-48 shrink-0 space-y-1 md:block"><a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Dashboard</a><a href="{{ route('admin.resellers') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Resellers</a><a href="{{ route('admin.clients') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Clients</a><a href="{{ route('admin.servers') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">FusionPBX Servers</a><a href="{{ route('admin.callers') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Caller Profiles</a><a href="{{ route('admin.calls') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Calls</a><a href="{{ route('admin.deposits') }}" class="block rounded-lg px-3 py-2 text-sm hover:bg-white">Deposits</a></aside>
            <main class="min-w-0 flex-1">@if (session('success'))<div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif @if ($errors->any())<div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>@endif @yield('admin-content')</main>
        </div>
    </div>
@endsection
