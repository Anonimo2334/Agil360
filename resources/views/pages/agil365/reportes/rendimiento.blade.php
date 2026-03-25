@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rendimiento de Ingenieros</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Análisis de participación y avance en proyectos</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Exportar CSV
        </button>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ingeniero</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Proyectos (como principal)</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Avance Promedio</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tareas Asignadas</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Bonos Generados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($engineers as $eng)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0 text-white text-xs font-bold">{{ substr($eng->name, 0, 2) }}</div>
                            {{ $eng->name }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $eng->primary_projects_count }}</td>
                    <td class="px-4 py-3 text-center text-brand-600 dark:text-brand-400 font-bold">{{ round($eng->primary_projects_avg_progress_percentage ?? 0) }}%</td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $eng->assigned_tasks_count }}</td>
                    <td class="px-4 py-3 text-center text-success-600 dark:text-success-400 font-medium">{{ $eng->bonuses_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No hay ingenieros registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
