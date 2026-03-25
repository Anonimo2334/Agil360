@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Proyectos Activos</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor detallado de ejecución de proyectos</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Exportar CSV
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Activos</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">En Riesgo</p>
        <p class="text-2xl font-bold text-error-600 dark:text-error-400">{{ $stats['at_risk'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Avance Promedio</p>
        <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['avg_progress'] }}%</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Atrasados (> Fecha Límite)</p>
        <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $stats['delayed'] }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="mb-4">
    <form method="GET" action="{{ route('reportes.activos') }}" class="flex items-center gap-3">
        <select name="engineer_id" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
            <option value="">Todo ingeniero</option>
            @foreach($engineers as $eng)
                <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
            @endforeach
        </select>
        @if(request()->anyFilled(['engineer_id']))
            <a href="{{ route('reportes.activos') }}" class="text-xs text-gray-500 hover:underline">Limpiar</a>
        @endif
    </form>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Proyecto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ingeniero</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Avance</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha Límite</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Riesgo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($projects as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                        <a href="{{ route('proyectos.show', $p) }}" class="hover:text-brand-500 transition-colors">{{ $p->project_name }}</a>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $p->company->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $p->primaryEngineer->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 w-16">
                                <div class="bg-brand-500 h-1.5 rounded-full" style="width: {{ $p->progress_percentage }}%"></div>
                            </div>
                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-8">{{ $p->progress_percentage }}%</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs {{ $p->is_overdue ? 'text-error-600 font-bold' : 'text-gray-500' }}">
                        {{ $p->end_date ? $p->end_date->format('d/m/Y') : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->is_at_risk)
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400 uppercase">Sí</span>
                        @else
                        <span class="text-gray-400 text-xs">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400 text-sm">No hay proyectos activos.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
