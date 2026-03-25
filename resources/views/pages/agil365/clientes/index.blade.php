@extends('layouts.app')

@section('content')
{{-- Flash --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400 flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clientes</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de empresas y cuentas activas</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Nuevo Cliente
    </a>
</div>

{{-- Search & Filter --}}
<form method="GET" action="{{ route('clientes') }}" class="mb-5 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[200px] max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar cliente..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20" onchange="this.form.submit()">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <select name="status" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todo estado</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
    @if(request()->anyFilled(['search','status']))
        <a href="{{ route('clientes') }}" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Limpiar</a>
    @endif
</form>

{{-- Clients Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @forelse($companies as $client)
    @php
        $projectsCount = $client->projects_count ?? 0;
        $avgProgress   = $client->projects_avg_progress_percentage ?? 0;
        $completedCount = $client->projects_completed_count ?? 0;
        $colors = ['bg-brand-500','bg-purple-500','bg-success-500','bg-warning-500','bg-error-500','bg-blue-light-500'];
        $ci = abs(crc32($client->name)) % count($colors);
    @endphp
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 hover:shadow-theme-md transition-all group">
        {{-- Header --}}
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white text-sm font-bold">{{ substr($client->name, 0, 2) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $client->name }}</h3>
                    <p class="text-xs text-gray-400">{{ $client->country ?? '—' }}</p>
                </div>
            </div>
            <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $client->is_active ? 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                {{ $client->is_active ? 'Activo' : 'Inactivo' }}
            </span>
        </div>

        {{-- Contact Info --}}
        <div class="space-y-2 mb-4">
            @if($client->contact_name)
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                <svg class="flex-shrink-0 text-gray-400" width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                {{ $client->contact_name }}
            </div>
            @endif
            @if($client->email)
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                <svg class="flex-shrink-0 text-gray-400" width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" stroke="currentColor" stroke-width="1.5"/><polyline points="22,6 12,13 2,6" stroke="currentColor" stroke-width="1.5"/></svg>
                {{ $client->email }}
            </div>
            @endif
            @if($client->phone)
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                <svg class="flex-shrink-0 text-gray-400" width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81 19.79 19.79 0 01.22 1.18 2 2 0 012.18 0a1 1 0 011 .27l2.67 2.67" stroke="currentColor" stroke-width="1.5"/></svg>
                {{ $client->phone }}
            </div>
            @endif
            @if($client->website)
            <div class="flex items-center gap-2 text-xs text-brand-500">
                <svg class="flex-shrink-0" width="13" height="13" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z" stroke="currentColor" stroke-width="1.5"/></svg>
                <a href="https://{{ $client->website }}" target="_blank" class="hover:underline">{{ $client->website }}</a>
            </div>
            @endif
        </div>

        {{-- Stats --}}
        <div class="flex items-center justify-between py-3 border-t border-gray-100 dark:border-gray-800 mb-4">
            <div class="text-center">
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $projectsCount }}</p>
                <p class="text-xs text-gray-400">Proyectos</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-brand-500">{{ round($avgProgress) }}%</p>
                <p class="text-xs text-gray-400">Promedio</p>
            </div>
            <div class="text-center">
                <p class="text-lg font-bold text-success-600">{{ $completedCount }}</p>
                <p class="text-xs text-gray-400">Completados</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('clientes.show', $client) }}" class="flex-1 py-2 text-xs font-medium text-center text-brand-500 border border-brand-200 rounded-lg hover:bg-brand-50 transition-colors dark:border-brand-500/20 dark:hover:bg-brand-500/10">
                Ver proyectos
            </a>
            <a href="{{ route('clientes.edit', $client) }}" class="p-2 text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors" title="Editar">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5"/></svg>
            </a>
            <form method="POST" action="{{ route('clientes.destroy', $client) }}" onsubmit="return confirm('¿Eliminar cliente {{ addslashes($client->name) }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-2 text-error-500 border border-error-200 rounded-lg hover:bg-error-50 dark:border-error-500/20 dark:hover:bg-error-500/10 transition-colors" title="Eliminar">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="1.5"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="col-span-3 rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <p class="text-gray-400 mb-3">No se encontraron clientes.</p>
        <a href="{{ route('clientes.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            + Nuevo Cliente
        </a>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if(isset($companies) && method_exists($companies, 'links') && $companies->hasPages())
<div class="mt-6 flex justify-center">{{ $companies->links() }}</div>
@endif
@endsection
