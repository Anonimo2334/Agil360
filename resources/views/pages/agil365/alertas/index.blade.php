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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Alertas del Sistema</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoreo automático de riesgos y notificaciones</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-error-50 text-error-700 rounded-full dark:bg-error-500/10 dark:text-error-400">
            <span class="w-1.5 h-1.5 rounded-full bg-error-500 animate-pulse"></span>
            {{ $alerts->total() }} alertas activas
        </span>
    </div>
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
        $criticals = $alerts->filter(fn($a) => $a->severity === 'error')->count();
        $warnings  = $alerts->filter(fn($a) => $a->severity === 'warning')->count();
        $vencidos  = $alerts->filter(fn($a) => $a->type === 'vencido')->count();
        $summaryCards = [
            ['count' => $criticals, 'label' => 'Riesgo crítico',     'color' => 'error',   'icon' => 'M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['count' => $warnings,  'label' => 'Advertencias',       'color' => 'warning', 'icon' => 'M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z'],
            ['count' => $vencidos,  'label' => 'Proyectos vencidos', 'color' => 'error',   'icon' => 'M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z'],
            ['count' => $alerts->total(), 'label' => 'Total activas', 'color' => 'success', 'icon' => 'M20 6L9 17l-5-5'],
        ];
    @endphp
    @foreach($summaryCards as $card)
    <div class="rounded-2xl border border-{{ $card['color'] }}-200 bg-{{ $card['color'] }}-50 p-5 dark:border-{{ $card['color'] }}-500/20 dark:bg-{{ $card['color'] }}-500/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-100 dark:bg-{{ $card['color'] }}-500/20 flex items-center justify-center">
                <svg class="text-{{ $card['color'] }}-500" width="18" height="18" fill="none" viewBox="0 0 24 24">
                    <path d="{{ $card['icon'] }}" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-{{ $card['color'] }}-700 dark:text-{{ $card['color'] }}-400">{{ $card['count'] }}</p>
                <p class="text-xs text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-500">{{ $card['label'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('alertas') }}" class="mb-4 flex flex-wrap items-center gap-3">
    <select name="severity" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Toda severidad</option>
        <option value="error" {{ request('severity') === 'error' ? 'selected' : '' }}>Error / Crítico</option>
        <option value="warning" {{ request('severity') === 'warning' ? 'selected' : '' }}>Advertencia</option>
    </select>
    <select name="type" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todo tipo</option>
        <option value="riesgo" {{ request('type') === 'riesgo' ? 'selected' : '' }}>Riesgo</option>
        <option value="vencido" {{ request('type') === 'vencido' ? 'selected' : '' }}>Vencido</option>
    </select>
    @if(request()->anyFilled(['severity','type']))
        <a href="{{ route('alertas') }}" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Limpiar</a>
    @endif
</form>

{{-- Alerts List --}}
<div class="space-y-3">
    @forelse($alerts as $alert)
    <div class="rounded-2xl border bg-white dark:bg-gray-900 p-5 hover:shadow-theme-sm transition-shadow
        {{ $alert->severity === 'error' ? 'border-error-200 dark:border-error-500/20' : 'border-warning-200 dark:border-warning-500/20' }}">
        <div class="flex items-start gap-4">
            {{-- Icon --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5
                {{ $alert->severity === 'error' ? 'bg-error-100 dark:bg-error-500/20' : 'bg-warning-100 dark:bg-warning-500/20' }}">
                @if($alert->severity === 'error')
                    <svg class="text-error-500" width="18" height="18" fill="none" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" fill="currentColor" opacity="0.2"/>
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01" stroke="#f04438" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                @else
                    <svg class="text-warning-500" width="18" height="18" fill="none" viewBox="0 0 24 24">
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" fill="currentColor" opacity="0.2"/>
                        <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01" stroke="#f79009" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                @endif
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full
                        {{ $alert->severity === 'error' ? 'bg-error-100 text-error-700 dark:bg-error-500/20 dark:text-error-400' : 'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' }}">
                        {{ ucfirst($alert->type) }}
                    </span>
                    @if($alert->project)
                    <a href="{{ route('proyectos.show', $alert->project) }}" class="text-xs font-semibold text-gray-700 dark:text-gray-300 hover:text-brand-500 transition-colors">
                        {{ $alert->project->project_name }}
                    </a>
                    @endif
                    <span class="text-xs text-gray-400">· {{ $alert->created_at->diffForHumans() }}</span>
                    @if($alert->is_read)
                        <span class="text-xs text-gray-400 italic">(Leída)</span>
                    @endif
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 leading-relaxed">{{ $alert->message }}</p>
                <div class="flex flex-wrap items-center gap-3">
                    @if($alert->project)
                        <a href="{{ route('proyectos.show', $alert->project) }}" class="px-3 py-1.5 text-xs font-medium
                            {{ $alert->severity === 'error' ? 'bg-error-500 hover:bg-error-600 text-white' : 'bg-warning-400 hover:bg-warning-500 text-white' }}
                            rounded-lg transition-colors inline-block">
                            Ver proyecto
                        </a>
                    @endif
                    @if(!$alert->is_read)
                    <form method="POST" action="{{ route('alertas.read', $alert) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-500 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
                            Marcar como revisada
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('alertas.resolve', $alert) }}" class="inline">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-success-600 border border-success-200 rounded-lg hover:bg-success-50 dark:border-success-500/20 dark:hover:bg-success-500/10 transition-colors">
                            ✓ Resolver
                        </button>
                    </form>
                    <form method="POST" action="{{ route('alertas.ignore', $alert) }}" class="inline" onsubmit="return confirm('¿Ignorar esta alerta?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 text-xs font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                            Ignorar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center dark:border-gray-800 dark:bg-gray-900">
        <div class="w-16 h-16 rounded-full bg-success-50 dark:bg-success-500/10 flex items-center justify-center mx-auto mb-4">
            <svg class="text-success-500" width="28" height="28" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">¡Sin alertas activas!</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">Todos los proyectos están en buen estado.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
@if($alerts->hasPages())
<div class="mt-5 flex justify-center">{{ $alerts->links() }}</div>
@endif

@endsection
