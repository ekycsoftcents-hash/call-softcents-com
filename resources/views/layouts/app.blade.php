<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php($brand = $whiteLabelBranding ?? null)
    <title>{{ $title ?? $brand?->brand_name ?? config('app.name') }}</title>
    @if ($brand?->favicon_url)
        <link rel="icon" href="{{ $brand->favicon_url }}">
    @endif
    @vite(['resources/css/reseller.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 text-slate-950 antialiased">
    {{ $slot ?? '' }}
    @yield('content')
</body>
</html>
