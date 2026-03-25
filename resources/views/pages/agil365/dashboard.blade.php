@extends('layouts.app')

@section('content')

{{-- ════════════════════════════════════════════════════════
     ADMIN / GERENTE DASHBOARD
     ════════════════════════════════════════════════════════ --}}
@if($viewType === 'admin')

{{-- Header --}}
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard Global</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Vista completa del equipo — {{ now()->format('d/m/Y') }} · {{ auth()->user()->name }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-success-50 text-success-700 rounded-full dark:bg-success-500/10 dark:text-success-400">
            <span class="w-1.5 h-1.5 rounded-full bg-success-500 animate-pulse"></span>
            Sistema activo
        </span>
        @can('proyectos.crear')
        <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Nuevo Proyecto
        </a>
        @endcan
    </div>
</div>

{{-- Alert Banner --}}
@if($atRiskProjects > 0)
<div class="mb-6 p-4 bg-error-50 border border-error-200 rounded-xl flex items-start gap-3 dark:bg-error-500/10 dark:border-error-500/20">
    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-error-100 flex items-center justify-center dark:bg-error-500/20">
        <svg class="text-error-600 dark:text-error-400" width="16" height="16" fill="none" viewBox="0 0 24 24">
            <path d="M12 9v4M12 17h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>
    <div class="flex-1">
        <p class="text-sm font-semibold text-error-700 dark:text-error-400">⚠ {{ $atRiskProjects }} {{ $atRiskProjects === 1 ? 'proyecto' : 'proyectos' }} en zona de riesgo</p>
        <p class="text-xs text-error-600 dark:text-error-500 mt-0.5">Proyectos con bajo avance y tiempo restante crítico.</p>
    </div>
    <a href="{{ route('alertas') }}" class="flex-shrink-0 text-xs font-medium text-error-600 hover:text-error-700 dark:text-error-400 underline">Ver alertas</a>
</div>
@endif

@if(session('success'))
<div class="mb-4 p-4 bg-success-50 border border-success-200 rounded-xl dark:bg-success-500/10 dark:border-success-500/20">
    <p class="text-sm font-medium text-success-700 dark:text-success-400">{{ session('success') }}</p>
</div>
@endif

{{-- ── KPI Row 1: Proyectos ── --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6 mb-4">
    @php
    $kpis = [
        ['value' => $totalProjects,      'label' => 'Total proyectos',   'badge' => 'Total',        'icon_color' => 'brand',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['value' => $activeProjects,     'label' => 'En proceso',        'badge' => 'Activos',      'icon_color' => 'blue-light',  'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        ['value' => $supportProjects,    'label' => 'En soporte',        'badge' => 'Soporte',      'icon_color' => 'warning',    'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['value' => $completedProjects,  'label' => 'Completados',       'badge' => 'Finalizados',  'icon_color' => 'success',    'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['value' => $atRiskProjects,     'label' => 'En riesgo',         'badge' => 'Riesgo',       'icon_color' => 'error',      'icon' => 'M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01'],
        ['value' => $avgProgress . '%',  'label' => 'Avance promedio',   'badge' => 'Avance',       'icon_color' => 'purple',     'icon' => 'M16 8v8m-4-5v5m-4-2v2M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
    ];
    @endphp
    @foreach($kpis as $kpi)
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 hover:shadow-theme-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 rounded-xl bg-{{ $kpi['icon_color'] }}-50 dark:bg-{{ $kpi['icon_color'] }}-500/10 flex items-center justify-center">
                <svg class="text-{{ $kpi['icon_color'] }}-500" width="18" height="18" fill="none" viewBox="0 0 24 24">
                    <path d="{{ $kpi['icon'] }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kpi['value'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $kpi['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── KPI Row 2: Tareas ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @php
    $taskKpis = [
        ['value'=>$pendingTasks,      'label'=>'Tareas pendientes',    'color'=>'warning'],
        ['value'=>$inProgressTasks,   'label'=>'En progreso',          'color'=>'blue-light'],
        ['value'=>$completedTasks,    'label'=>'Tareas completadas',   'color'=>'success'],
        ['value'=>$overdueTasks,      'label'=>'Tareas vencidas',      'color'=>'error'],
    ];
    @endphp
    @foreach($taskKpis as $tk)
    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 flex items-center gap-3">
        <div class="w-2 h-10 rounded-full bg-{{ $tk['color'] }}-500 flex-shrink-0"></div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $tk['value'] }}</p>
            <p class="text-xs text-gray-400">{{ $tk['label'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ── Main Grid ── --}}
<div class="grid grid-cols-12 gap-6">

    {{-- Projects Table --}}
    <div class="col-span-12 xl:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Proyectos Activos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Mostrando los 10 más recientes</p>
                </div>
                <a href="{{ route('proyectos') }}" class="px-3 py-2 text-xs font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Proyecto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ingeniero</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Vence</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($projects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-mono text-gray-400">#{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}</span>
                                    @if($project->is_at_risk)
                                    <svg class="text-error-500 animate-pulse flex-shrink-0" width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $project->project_name }}</p>
                                <p class="text-xs text-gray-400">{{ $project->company->name }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 w-16">
                                        @php $pc = $project->progress_percentage; $pcol = $pc >= 80 ? 'bg-success-500' : ($pc >= 50 ? 'bg-blue-light-500' : 'bg-error-500'); @endphp
                                        <div class="{{ $pcol }} h-1.5 rounded-full" style="width:{{ $pc }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300 w-8">{{ $pc }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php $bc = match($project->status) { 'completado'=>'success','en_proceso'=>'blue-light','soporte'=>'warning','iniciado'=>'brand', default=>'error' }; @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $bc }}-50 text-{{ $bc }}-700 dark:bg-{{ $bc }}-500/10 dark:text-{{ $bc }}-400">
                                    {{ $project->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($project->primaryEngineer)
                                <div class="flex items-center gap-1.5">
                                    <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ substr($project->primaryEngineer->name, 0, 1) }}</span>
                                    </div>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ explode(' ', $project->primaryEngineer->name)[0] }}</span>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($project->end_date)
                                <span class="text-xs {{ $project->is_overdue ? 'text-error-600 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                    {{ $project->end_date->format('d/m/Y') }}
                                </span>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('proyectos.show', $project) }}" class="inline-flex items-center gap-1 text-xs font-medium text-brand-500 hover:text-brand-600">
                                    Ver <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400">
                                No hay proyectos registrados aún.
                                <a href="{{ route('proyectos.create') }}" class="text-brand-500 hover:underline ml-1">Crear uno</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ── Engineers Performance ── --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 mt-5">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Rendimiento del Equipo</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Carga de trabajo y tareas completadas por ingeniero</p>
                </div>
                <a href="{{ route('usuarios') }}" class="text-xs text-brand-500 hover:text-brand-600">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Ingeniero</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Proyectos activos</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tareas pendientes</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Tareas completadas</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Carga</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($engineers as $index => $eng)
                        @php $colors = ['bg-brand-500','bg-purple-500','bg-success-500','bg-warning-500','bg-blue-light-500','bg-red-500']; @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full {{ $colors[$index % count($colors)] }} flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">{{ $eng->initials }}</span>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $eng->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $eng->role->name ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-blue-light-600 dark:text-blue-light-400">{{ $eng->active_projects_count }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-warning-600 dark:text-warning-400">{{ $eng->pending_tasks_count }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-semibold text-success-600 dark:text-success-400">{{ $eng->completed_tasks_count }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $total = $eng->pending_tasks_count + $eng->completed_tasks_count;
                                    $pct = $total > 0 ? round(($eng->completed_tasks_count / $total) * 100) : 0;
                                    $barColor = $pct >= 70 ? 'bg-success-500' : ($pct >= 40 ? 'bg-warning-500' : 'bg-error-500');
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-20 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                        <div class="{{ $barColor }} h-1.5 rounded-full" style="width:{{ $pct }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $pct }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-8 text-center text-xs text-gray-400">Sin ingenieros registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Side Panel --}}
    <div class="col-span-12 xl:col-span-4 flex flex-col gap-5">

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-success-50 to-success-100 dark:from-success-500/10 dark:to-success-500/5 dark:border-success-500/20 p-4">
                <p class="text-xs text-success-600 dark:text-success-400 font-medium mb-1">Completados este mes</p>
                <p class="text-2xl font-bold text-success-700 dark:text-success-300">{{ $completedThisMonth }}</p>
                <p class="text-xs text-success-600/70 dark:text-success-500 mt-0.5">proyectos</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-gradient-to-br from-brand-50 to-brand-100 dark:from-brand-500/10 dark:to-brand-500/5 dark:border-brand-500/20 p-4">
                <p class="text-xs text-brand-600 dark:text-brand-400 font-medium mb-1">Tareas esta semana</p>
                <p class="text-2xl font-bold text-brand-700 dark:text-brand-300">{{ $tasksCompletedThisWeek }}</p>
                <p class="text-xs text-brand-600/70 dark:text-brand-500 mt-0.5">completadas</p>
            </div>
        </div>

        {{-- Alertas Activas --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Alertas Activas</h3>
                <a href="{{ route('alertas') }}" class="text-xs text-brand-500 hover:text-brand-600">Ver todas</a>
            </div>
            <div class="space-y-2.5">
                @forelse($alerts as $alert)
                <div class="flex items-start gap-2.5 p-3 rounded-xl {{ $alert->severity === 'error' ? 'bg-error-50 dark:bg-error-500/8' : 'bg-warning-50 dark:bg-warning-500/8' }}">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $alert->severity === 'error' ? 'bg-error-500' : 'bg-warning-400' }} animate-pulse"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium {{ $alert->severity === 'error' ? 'text-error-700 dark:text-error-400' : 'text-warning-700 dark:text-warning-400' }}">{{ $alert->message }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-xs text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5"/></svg>
                    Sin alertas activas
                </div>
                @endforelse
            </div>
        </div>

        {{-- Reuniones de Hoy --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Reuniones de Hoy</h3>
                <a href="{{ route('reuniones') }}" class="text-xs text-brand-500 hover:text-brand-600">Ver todas</a>
            </div>
            <div class="space-y-2.5">
                @forelse($todayMeetings as $meeting)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <div class="text-center bg-white dark:bg-gray-900 rounded-lg px-2 py-1 min-w-[48px] shadow-sm">
                        <p class="text-xs font-bold text-brand-500">{{ date('h:i', strtotime($meeting->meeting_time)) }}</p>
                        <p class="text-xs text-gray-400">{{ date('A', strtotime($meeting->meeting_time)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $meeting->title }}</p>
                        @if($meeting->project)
                        <p class="text-xs text-gray-400">{{ $meeting->project->company->name ?? '' }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-3">Sin reuniones para hoy.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>{{-- end admin grid --}}


{{-- ════════════════════════════════════════════════════════
     ENGINEER / SOPORTE / VIEWER DASHBOARD
     ════════════════════════════════════════════════════════ --}}
@else

{{-- Header personal --}}
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Espacio de Trabajo</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ auth()->user()->name }} · {{ now()->format('d/m/Y') }}
            @if(auth()->user()->role)
            · <span class="text-brand-500">{{ auth()->user()->role->name }}</span>
            @endif
        </p>
    </div>
    <div class="flex items-center gap-3">
        @if($myAtRiskProjects > 0)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-error-50 text-error-700 rounded-full dark:bg-error-500/10 dark:text-error-400">
            <span class="w-1.5 h-1.5 rounded-full bg-error-500 animate-pulse"></span>
            {{ $myAtRiskProjects }} proyecto(s) en riesgo
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-success-50 text-success-700 rounded-full dark:bg-success-500/10 dark:text-success-400">
            <span class="w-1.5 h-1.5 rounded-full bg-success-500 animate-pulse"></span>
            Todo al día
        </span>
        @endif
        <a href="{{ route('tareas') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            Mis Tareas
        </a>
    </div>
</div>

{{-- ── KPIs Personales ── --}}
<div class="grid grid-cols-2 gap-4 sm:grid-cols-4 xl:grid-cols-7 mb-6">
    @php
    $engKpis = [
        ['value'=>$myTotalProjects,      'label'=>'Mis proyectos',       'color'=>'brand',      'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2'],
        ['value'=>$myActiveProjects,     'label'=>'En proceso',          'color'=>'blue-light', 'icon'=>'M13 10V3L4 14h7v7l9-11h-7z'],
        ['value'=>$myCompletedProjects,  'label'=>'Completados',         'color'=>'success',    'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['value'=>$myAtRiskProjects,     'label'=>'En riesgo',           'color'=>'error',      'icon'=>'M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4M12 17h.01'],
        ['value'=>$myPendingTasks,       'label'=>'Tareas pendientes',   'color'=>'warning',    'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['value'=>$myInProgressTasks,    'label'=>'En progreso',         'color'=>'blue-light', 'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['value'=>$myTasksThisMonth,     'label'=>'Completadas (mes)',   'color'=>'success',    'icon'=>'M5 13l4 4L19 7'],
    ];
    @endphp
    @foreach($engKpis as $k)
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900 hover:shadow-theme-md transition-shadow">
        <div class="w-8 h-8 rounded-xl bg-{{ $k['color'] }}-50 dark:bg-{{ $k['color'] }}-500/10 flex items-center justify-center mb-3">
            <svg class="text-{{ $k['color'] }}-500" width="16" height="16" fill="none" viewBox="0 0 24 24">
                <path d="{{ $k['icon'] }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $k['value'] }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $k['label'] }}</p>
    </div>
    @endforeach
</div>

{{-- ── Rendimiento personal ── --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Mi rendimiento</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-brand-50 dark:bg-brand-500/10">
            <p class="text-xs text-brand-600 dark:text-brand-400 mb-1 font-medium">Avance promedio proyectos</p>
            <div class="flex items-end gap-2">
                <p class="text-3xl font-bold text-brand-700 dark:text-brand-300">{{ $myAvgProgress }}%</p>
            </div>
            <div class="mt-2 w-full bg-brand-100 dark:bg-brand-500/20 rounded-full h-1.5">
                <div class="bg-brand-500 h-1.5 rounded-full transition-all" style="width:{{ $myAvgProgress }}%"></div>
            </div>
        </div>
        <div class="p-4 rounded-xl bg-success-50 dark:bg-success-500/10">
            <p class="text-xs text-success-600 dark:text-success-400 mb-1 font-medium">Tareas esta semana</p>
            <p class="text-3xl font-bold text-success-700 dark:text-success-300">{{ $myTasksThisWeek }}</p>
            <p class="text-xs text-success-600/70 dark:text-success-500 mt-1">completadas</p>
        </div>
        <div class="p-4 rounded-xl bg-warning-50 dark:bg-warning-500/10">
            <p class="text-xs text-warning-600 dark:text-warning-400 mb-1 font-medium">Pendientes activos</p>
            <p class="text-3xl font-bold text-warning-700 dark:text-warning-300">{{ $myPendingTasks + $myInProgressTasks }}</p>
            <p class="text-xs text-warning-600/70 dark:text-warning-500 mt-1">tareas este sprint</p>
        </div>
    </div>
</div>

{{-- ── Main Grid Engineer ── --}}
<div class="grid grid-cols-12 gap-6">

    {{-- Mis Proyectos --}}
    <div class="col-span-12 xl:col-span-8 flex flex-col gap-5">

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mis Proyectos</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Proyectos donde eres ingeniero principal o de respaldo</p>
                </div>
                <a href="{{ route('proyectos') }}" class="px-3 py-2 text-xs font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Ver todos</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Proyecto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Avance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Estado</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wide">Vence</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($myProjects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @if($project->is_at_risk)
                                    <svg class="text-error-500 animate-pulse" width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $project->project_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $project->company->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @php $pc2 = $project->progress_percentage; $pcol2 = $pc2 >= 80 ? 'bg-success-500' : ($pc2 >= 50 ? 'bg-blue-light-500' : 'bg-error-500'); @endphp
                                    <div class="w-16 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                                        <div class="{{ $pcol2 }} h-1.5 rounded-full" style="width:{{ $pc2 }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $pc2 }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @php $bc2 = match($project->status) { 'completado'=>'success','en_proceso'=>'blue-light','soporte'=>'warning','iniciado'=>'brand', default=>'error' }; @endphp
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full bg-{{ $bc2 }}-50 text-{{ $bc2 }}-700 dark:bg-{{ $bc2 }}-500/10 dark:text-{{ $bc2 }}-400">{{ $project->status_label }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($project->end_date)
                                <span class="text-xs {{ $project->is_overdue ? 'text-error-600 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">{{ $project->end_date->format('d/m/Y') }}</span>
                                @else
                                <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('proyectos.show', $project) }}" class="inline-flex items-center gap-1 text-xs font-medium text-brand-500 hover:text-brand-600">
                                    Ver <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M9 18l6-6-6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-sm text-gray-400">No tienes proyectos asignados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Mis Tareas Activas --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Mis Tareas</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Tareas pendientes y en progreso</p>
                </div>
                <a href="{{ route('tareas') }}" class="px-3 py-2 text-xs font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Ver todas</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($recentTasks as $task)
                <div class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                    @php
                        $tc = match($task->priority) {
                            'critica','alta' => 'error',
                            'media' => 'warning',
                            default => 'gray'
                        };
                    @endphp
                    <div class="w-2 h-2 rounded-full bg-{{ $tc }}-500 flex-shrink-0"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">{{ $task->title }}</p>
                        <p class="text-xs text-gray-400">{{ $task->project->project_name ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 flex-shrink-0">
                        @php $sc = $task->status === 'en_progreso' ? 'blue-light' : 'warning'; @endphp
                        <span class="inline-flex px-2 py-0.5 text-xs rounded-full bg-{{ $sc }}-50 text-{{ $sc }}-700 dark:bg-{{ $sc }}-500/10 dark:text-{{ $sc }}-400">{{ $task->status_label }}</span>
                        @if($task->due_date)
                        <span class="text-xs {{ $task->is_overdue ? 'text-error-600 font-semibold' : 'text-gray-400' }}">{{ $task->due_date->format('d/m') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-xs text-gray-400">
                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5"/></svg>
                    No tienes tareas pendientes. ¡Todo al día!
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Side Panel Engineer --}}
    <div class="col-span-12 xl:col-span-4 flex flex-col gap-5">

        {{-- Tareas urgentes --}}
        @if($urgentTasks->count() > 0)
        <div class="rounded-2xl border border-error-200 bg-error-50 dark:border-error-500/20 dark:bg-error-500/5 p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg class="text-error-500" width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <h3 class="text-sm font-semibold text-error-700 dark:text-error-400">Tareas vencidas</h3>
            </div>
            <div class="space-y-2">
                @foreach($urgentTasks as $ut)
                <div class="flex items-center justify-between p-2.5 bg-white dark:bg-gray-900 rounded-xl">
                    <p class="text-xs font-medium text-gray-800 dark:text-gray-200 truncate flex-1 pr-2">{{ $ut->title }}</p>
                    <span class="text-xs text-error-600 dark:text-error-400 flex-shrink-0">{{ $ut->due_date->format('d/m') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Alertas de Mis Proyectos --}}
        @if($alerts->count() > 0)
        <div class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-500/20 dark:bg-warning-500/5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-warning-900 dark:text-warning-400">Alertas de Proyectos</h3>
                <a href="{{ route('alertas') }}" class="text-xs text-warning-700 hover:text-warning-800 dark:hover:text-warning-300">Ver todas</a>
            </div>
            <div class="space-y-2.5">
                @foreach($alerts as $alert)
                <div class="flex items-start gap-2.5 p-3 rounded-xl {{ $alert->severity === 'error' ? 'bg-error-50 dark:bg-error-500/8' : 'bg-warning-50 dark:bg-warning-500/8' }}">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $alert->severity === 'error' ? 'bg-error-500' : 'bg-warning-400' }} animate-pulse"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium {{ $alert->severity === 'error' ? 'text-error-700 dark:text-error-400' : 'text-warning-700 dark:text-warning-400' }}">{{ $alert->message }}</p>
                        <p class="text-[10px] text-error-600/70 dark:text-error-500/70 mt-1 flex justify-between">
                            <span>{{ $alert->created_at->diffForHumans() }}</span>
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Reuniones de hoy --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Reuniones de hoy</h3>
                <a href="{{ route('reuniones') }}" class="text-xs text-brand-500 hover:text-brand-600">Ver todas</a>
            </div>
            <div class="space-y-2.5">
                @forelse($todayMeetings as $meeting)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <div class="text-center bg-white dark:bg-gray-900 rounded-lg px-2 py-1 min-w-[48px] shadow-sm">
                        <p class="text-xs font-bold text-brand-500">{{ date('h:i', strtotime($meeting->meeting_time)) }}</p>
                        <p class="text-xs text-gray-400">{{ date('A', strtotime($meeting->meeting_time)) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-800 dark:text-gray-200">{{ $meeting->title }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $meeting->project->project_name ?? '' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-3">Sin reuniones para hoy.</p>
                @endforelse
            </div>
        </div>

        {{-- Mis pendientes asignados --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Mis Pendientes</h3>
                <a href="{{ route('pendientes') }}" class="text-xs text-brand-500 hover:text-brand-600">Ver todos</a>
            </div>
            <div class="space-y-2.5">
                @forelse($myPendingItems as $item)
                <div class="flex items-start gap-2.5">
                    <div class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 flex-shrink-0 mt-0.5"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate">{{ $item->title }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $item->project->project_name ?? '' }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-3">Sin pendientes asignados.</p>
                @endforelse
            </div>
        </div>
    </div>

</div>{{-- end engineer grid --}}

@endif

@endsection
