@extends('admin.layout')

@section('admin-content')
    <div><h1 class="text-2xl font-bold">{{ $title }}</h1><p class="mt-1 text-sm text-slate-600">Platform records and operational status.</p></div>
    <div class="mt-6 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr>@foreach ($columns as $column)<th class="px-5 py-3">{{ $column }}</th>@endforeach</tr></thead><tbody class="divide-y divide-slate-100">@forelse ($rows as $row)<tr>@foreach ($row as $value)<td class="px-5 py-4">{{ $value }}</td>@endforeach</tr>@empty<tr><td colspan="{{ count($columns) }}" class="px-5 py-10 text-center text-slate-500">No records found.</td></tr>@endforelse</tbody></table></div><div class="border-t border-slate-200 px-5 py-3">{{ $rows->links() }}</div></div>
@endsection
