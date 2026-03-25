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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Tareas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Panel de asignación y seguimiento de tareas</p>
    </div>
    <button onclick="document.getElementById('modal-nueva-tarea').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Nueva Tarea
    </button>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('tareas') }}" id="filter-form">
<div class="mb-5 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[200px] max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar tarea..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20" onchange="this.form.submit()">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <select name="project_id" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todos los proyectos</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
        @endforeach
    </select>
    <select name="engineer_id" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todos los ingenieros</option>
        @foreach($engineers as $eng)
            <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
        @endforeach
    </select>
    <select name="priority" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Toda prioridad</option>
        <option value="alta" {{ request('priority') === 'alta' ? 'selected' : '' }}>Alta</option>
        <option value="media" {{ request('priority') === 'media' ? 'selected' : '' }}>Media</option>
        <option value="baja" {{ request('priority') === 'baja' ? 'selected' : '' }}>Baja</option>
        <option value="critica" {{ request('priority') === 'critica' ? 'selected' : '' }}>Crítica</option>
    </select>
    @if(request()->anyFilled(['search','project_id','engineer_id','priority']))
        <a href="{{ route('tareas') }}" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Limpiar</a>
    @endif
</div>
</form>

{{-- Kanban Columns --}}
<div class="flex xl:grid xl:grid-cols-4 gap-5 overflow-x-auto pb-4 snap-x">
    @php
        $columns = [
            'pendiente'   => ['title' => 'Pendiente',  'color' => 'bg-gray-400',          'textColor' => 'text-gray-600 dark:text-gray-400',           'bgHeader' => 'bg-gray-50 dark:bg-gray-800/50'],
            'en_progreso' => ['title' => 'En Progreso', 'color' => 'bg-blue-light-500',   'textColor' => 'text-blue-light-600 dark:text-blue-light-400', 'bgHeader' => 'bg-blue-light-50 dark:bg-blue-light-500/10'],
            'completada'  => ['title' => 'Completada',  'color' => 'bg-success-500',       'textColor' => 'text-success-600 dark:text-success-400',       'bgHeader' => 'bg-success-50 dark:bg-success-500/10'],
            'bloqueada'   => ['title' => 'Cancelado',   'color' => 'bg-error-500',         'textColor' => 'text-error-600 dark:text-error-400',           'bgHeader' => 'bg-error-50 dark:bg-error-500/10'],
        ];
        $grouped = $tasks->groupBy('status');
    @endphp

    @foreach($columns as $key => $col)
    @php $colTasks = $grouped->get($key, collect()); @endphp
    <div class="flex flex-col w-80 flex-shrink-0 xl:w-auto snap-center">
        {{-- Column Header --}}
        <div class="{{ $col['bgHeader'] }} rounded-xl p-3.5 mb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full {{ $col['color'] }}"></div>
                <span class="text-xs font-semibold {{ $col['textColor'] }}">{{ $col['title'] }}</span>
            </div>
            <span class="w-5 h-5 flex items-center justify-center text-xs font-bold {{ $col['textColor'] }} bg-white dark:bg-gray-900 rounded-full shadow-sm">{{ $colTasks->count() }}</span>
        </div>

        {{-- Tasks --}}
        <div class="space-y-3 flex-1 task-list min-h-[50px] transition-colors rounded-xl p-1" data-status="{{ $key }}" ondragover="allowDrop(event)" ondragleave="dragLeave(event)" ondrop="dropTask(event, '{{ $key }}')">
            @forelse($colTasks as $task)
            @php
                $prioClass = match($task->priority) {
                    'alta','critica' => 'bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400',
                    'media' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',
                    default => 'bg-gray-100 text-gray-500',
                };
            @endphp
            @php $isCompleted = $task->status === 'completada'; @endphp
            <div id="task-item-{{ $task->id }}"
                 data-task-id="{{ $task->id }}"
                 data-locked="{{ $isCompleted ? 'true' : 'false' }}"
                 @if(!$isCompleted) draggable="true" ondragstart="dragStart(event)" ondragend="dragEnd(event)" @endif
                 class="rounded-xl border {{ $isCompleted ? 'border-success-200 dark:border-success-500/30 bg-success-50/40 dark:bg-success-500/5' : 'border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 cursor-grab active:cursor-grabbing hover:shadow-theme-sm' }} p-4 shadow-sm transition-all group">
                <div class="flex items-start justify-between mb-3">
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $prioClass }}">{{ ucfirst($task->priority) }}</span>
                    <div class="flex items-center gap-1 {{ $isCompleted ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-all">
                        @if($isCompleted)
                            {{-- Lock icon for completed tasks --}}
                            <span title="Tarea completada y bloqueada" class="p-1 rounded text-success-500" style="cursor:default">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M19 11H5a2 2 0 00-2 2v7a2 2 0 002 2h14a2 2 0 002-2v-7a2 2 0 00-2-2zM7 11V7a5 5 0 0110 0v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </span>
                        @else
                            <button onclick="openEditTask({{ $task->id }}, '{{ addslashes($task->title) }}', '{{ $task->status }}', '{{ $task->priority }}', '{{ $task->assigned_engineer_id }}', {{ $task->progress ?? 0 }})"
                                class="p-1 rounded text-gray-400 hover:text-brand-500 transition-all" title="Editar">
                                <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5"/></svg>
                            </button>
                            <form method="POST" action="{{ route('tareas.destroy', $task) }}" onsubmit="return confirm('¿Eliminar esta tarea?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 rounded text-gray-400 hover:text-error-500 transition-all" title="Eliminar">
                                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="1.5"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-2 leading-snug {{ $key === 'completada' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                <p class="text-xs text-brand-500 mb-3">📁 {{ $task->project->project_name ?? '—' }}</p>

                @if($task->description)
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 line-clamp-2">{{ $task->description }}</p>
                @endif

                @if($key === 'bloqueada' && $task->description)
                <div class="mb-3 px-2.5 py-2 bg-error-50 dark:bg-error-500/10 rounded-lg">
                    <p class="text-xs text-error-600 dark:text-error-400">🚫 {{ $task->description }}</p>
                </div>
                @endif

                @if($task->progress > 0)
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-gray-400">Avance</span>
                        <span class="text-xs font-semibold {{ $task->progress === 100 ? 'text-success-500' : 'text-blue-light-500' }}">{{ $task->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                        <div class="{{ $task->progress === 100 ? 'bg-success-500' : 'bg-blue-light-500' }} h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-1.5">
                        @if($task->assignedEngineer)
                        <div class="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center">
                            <span class="text-white text-xs">{{ substr($task->assignedEngineer->name, 0, 1) }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ explode(' ', $task->assignedEngineer->name)[0] }}</span>
                        @else <span class="text-xs text-gray-400">Sin asignar</span> @endif
                    </div>
                    @if($key === 'completada')
                    <div class="flex items-center gap-1 text-[11px] text-success-600 dark:text-success-400 font-medium">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        {{ \Carbon\Carbon::parse($task->updated_at)->format('d/m/y H:i') }}
                    </div>
                    @elseif($task->due_date)
                    <div class="flex items-center gap-1 text-xs {{ $task->due_date < now()->format('Y-m-d') ? 'text-error-500' : 'text-gray-400' }}">
                        <svg width="10" height="10" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="1.5"/><line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="1.5"/></svg>
                        {{ \Carbon\Carbon::parse($task->due_date)->format('d/m') }}
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-4 text-center empty-state pointer-events-none">
                <p class="text-xs text-gray-400">Sin tareas</p>
            </div>
            @endforelse

            {{-- Add Task Button --}}
            <button onclick="document.getElementById('modal-nueva-tarea').classList.remove('hidden')"
                class="w-full py-2.5 text-xs font-medium text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex items-center justify-center gap-1.5">
                <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Agregar tarea
            </button>
        </div>
    </div>
    @endforeach
</div>

@push('modals')
{{-- MODAL: Nueva Tarea --}}
<div id="modal-nueva-tarea" class="hidden fixed flex items-center justify-center p-4 sm:p-5" style="top: 0; left: 0; right: 0; bottom: 0; z-index: 999999;">
    <div class="absolute bg-black/50 backdrop-blur-sm" style="top: 0; left: 0; right: 0; bottom: 0;" onclick="document.getElementById('modal-nueva-tarea').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full flex flex-col" style="max-width: 32rem; max-height: 90vh;">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Nueva Tarea</h3>
            <button onclick="document.getElementById('modal-nueva-tarea').classList.add('hidden')" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg dark:hover:text-gray-200 dark:hover:bg-gray-800 transition-colors">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Body -->
        <div class="overflow-y-auto p-4 md:p-5 custom-scrollbar">
            <form id="form-nueva-tarea" method="POST" action="{{ route('tareas.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Proyecto *</label>
                        <select name="project_id" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                            <option value="">Seleccionar proyecto...</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Título *</label>
                        <input type="text" name="title" required placeholder="Nombre de la tarea" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Descripción</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none transition-all"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prioridad</label>
                            <select name="priority" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                                <option value="baja">Baja</option>
                                <option value="media" selected>Media</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado</label>
                            <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                                <option value="pendiente" selected>Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="bloqueada">Cancelado</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero</label>
                        <select name="assigned_engineer_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha inicio</label>
                            <input type="date" name="start_date" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha límite</label>
                            <input type="date" name="due_date" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Progreso (%)</label>
                        <input type="number" name="progress" value="0" min="0" max="100" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                </div>
            </form>
        </div>
        <!-- Footer -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-100 dark:border-gray-800 shrink-0 bg-gray-50/50 dark:bg-gray-800/20 rounded-b-2xl">
            <button type="button" onclick="document.getElementById('modal-nueva-tarea').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">Cancelar</button>
            <button type="submit" form="form-nueva-tarea" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-brand-500 rounded-xl hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-all shadow-sm shadow-brand-500/20">Crear Tarea</button>
        </div>
    </div>
</div>

{{-- MODAL: Editar Tarea --}}
<div id="modal-edit-tarea" class="hidden fixed flex items-center justify-center p-4 sm:p-5" style="top: 0; left: 0; right: 0; bottom: 0; z-index: 999999;">
    <div class="absolute bg-black/50 backdrop-blur-sm" style="top: 0; left: 0; right: 0; bottom: 0;" onclick="document.getElementById('modal-edit-tarea').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full flex flex-col" style="max-width: 28rem; max-height: 90vh;">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 md:p-5 border-b border-gray-100 dark:border-gray-800 shrink-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Editar Tarea</h3>
            <button onclick="document.getElementById('modal-edit-tarea').classList.add('hidden')" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg dark:hover:text-gray-200 dark:hover:bg-gray-800 transition-colors">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <!-- Body -->
        <div class="overflow-y-auto p-4 md:p-5 custom-scrollbar">
            <form id="edit-tarea-form" method="POST" action="">
                @csrf @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Título *</label>
                        <input type="text" name="title" id="edit-task-title" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado</label>
                            <select name="status" id="edit-task-status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                                <option value="pendiente">Pendiente</option>
                                <option value="en_progreso">En Progreso</option>
                                <option value="completada">Completada</option>
                                <option value="bloqueada">Cancelado</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Prioridad</label>
                            <select name="priority" id="edit-task-priority" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                                <option value="baja">Baja</option>
                                <option value="media">Media</option>
                                <option value="alta">Alta</option>
                                <option value="critica">Crítica</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero</label>
                        <select name="assigned_engineer_id" id="edit-task-engineer" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                            <option value="">Sin asignar</option>
                            @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Progreso (%)</label>
                        <input type="number" name="progress" id="edit-task-progress" min="0" max="100" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 transition-all">
                    </div>
                </div>
            </form>
        </div>
        <!-- Footer -->
        <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 p-4 md:p-5 border-t border-gray-100 dark:border-gray-800 shrink-0 bg-gray-50/50 dark:bg-gray-800/20 rounded-b-2xl">
            <button type="button" onclick="document.getElementById('modal-edit-tarea').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">Cancelar</button>
            <button type="submit" form="edit-tarea-form" class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-white bg-brand-500 rounded-xl hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand-500/50 transition-all shadow-sm shadow-brand-500/20">Guardar</button>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
function openEditTask(id, title, status, priority, engineerId, progress) {
    document.getElementById('edit-tarea-form').action = '/tareas/' + id;
    document.getElementById('edit-task-title').value = title;
    document.getElementById('edit-task-status').value = status;
    document.getElementById('edit-task-priority').value = priority;
    document.getElementById('edit-task-engineer').value = engineerId || '';
    document.getElementById('edit-task-progress').value = progress;
    document.getElementById('modal-edit-tarea').classList.remove('hidden');
}

// ─── Toast Notification ─────────────────────────────────────────────────────
function showToast(message, type = 'error') {
    const existing = document.getElementById('kanban-toast');
    if (existing) existing.remove();

    const colors = {
        error:   'bg-error-500 text-white',
        success: 'bg-success-500 text-white',
        info:    'bg-brand-500 text-white',
    };
    const toast = document.createElement('div');
    toast.id = 'kanban-toast';
    toast.className = `fixed bottom-6 right-6 z-[9999] px-5 py-3 rounded-xl shadow-xl text-sm font-medium flex items-center gap-2 transition-all duration-300 ${colors[type] || colors.error}`;
    toast.innerHTML = `
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        ${message}
    `;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}

// ─── Drag & Drop ────────────────────────────────────────────────────────────
let draggedItem = null;

function dragStart(e) {
    const card = e.target.closest('[data-task-id]');
    // Block dragging completed tasks
    if (card && card.getAttribute('data-locked') === 'true') {
        e.preventDefault();
        showToast('Esta tarea está completada y no se puede mover.', 'error');
        return false;
    }
    draggedItem = card || e.target.closest('[draggable="true"]');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', draggedItem.id);
    setTimeout(() => draggedItem.classList.add('opacity-50'), 0);
}

function dragEnd(e) {
    if (draggedItem) {
        draggedItem.classList.remove('opacity-50');
        draggedItem = null;
    }
    document.querySelectorAll('.task-list').forEach(list => {
        list.classList.remove('bg-gray-100', 'dark:bg-gray-800/50');
    });
}

function allowDrop(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    const list = e.target.closest('.task-list');
    if (list) list.classList.add('bg-gray-100', 'dark:bg-gray-800/50');
}

function dragLeave(e) {
    const list = e.target.closest('.task-list');
    if (list) list.classList.remove('bg-gray-100', 'dark:bg-gray-800/50');
}

function dropTask(e, newStatus) {
    e.preventDefault();
    const list = e.target.closest('.task-list');
    if (list) list.classList.remove('bg-gray-100', 'dark:bg-gray-800/50');

    const itemId = e.dataTransfer.getData('text/plain');
    const droppedEl = document.getElementById(itemId);

    if (!droppedEl || !list) return;

    // Double-check: locked cards cannot be moved (source is locked)
    if (droppedEl.getAttribute('data-locked') === 'true') {
        showToast('Las tareas completadas no pueden moverse.', 'error');
        return;
    }

    const taskId = droppedEl.getAttribute('data-task-id');
    const currentStatus = droppedEl.closest('.task-list')?.getAttribute('data-status');

    // No-op if dropped in the same column
    if (currentStatus === newStatus) return;

    // Append to new list
    list.insertBefore(droppedEl, list.querySelector('.empty-state') || null);

    // Hide empty state if present
    const emptyState = list.querySelector('.empty-state');
    if (emptyState) emptyState.style.display = 'none';

    // Update header counts immediately (visually)
    updateHeaderCounts();

    // Update backend
    fetch(`{{ url('/tareas') }}/${taskId}/estado`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (data.locked) {
            showToast('Las tareas completadas no pueden cambiar de estado.', 'error');
            window.location.reload();
        } else if (!data.success) {
            showToast('Error al actualizar la tarea. Recargando...', 'error');
            window.location.reload();
        } else if (newStatus === 'completada') {
            // Reload to render the locked card with lock icon, date, and green styling
            window.location.reload();
        }
    })
    .catch(() => {
        showToast('Error de conexión. Recargando...', 'error');
        window.location.reload();
    });
}

function updateHeaderCounts() {
    document.querySelectorAll('.task-list').forEach(list => {
        const count = list.querySelectorAll('[data-task-id]').length;
        const headerCount = list.parentElement.querySelector('.rounded-full.shadow-sm');
        if (headerCount) headerCount.textContent = count;

        const emptyState = list.querySelector('.empty-state');
        if (count === 0 && emptyState) {
            emptyState.style.display = 'block';
        } else if (count === 0 && !emptyState) {
            list.insertAdjacentHTML('beforeend', `
                <div class="rounded-xl border border-dashed border-gray-200 dark:border-gray-700 p-4 text-center empty-state pointer-events-none">
                    <p class="text-xs text-gray-400">Sin tareas</p>
                </div>
            `);
        }
    });
}
</script>
@endpush
@endsection
