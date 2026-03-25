@extends('layouts.app')

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400 flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif

{{-- Header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('proyectos') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
            <span class="text-xs text-gray-400">/</span>
            <a href="{{ route('proyectos') }}" class="text-xs text-gray-500 hover:text-brand-500">Proyectos</a>
            <span class="text-xs text-gray-400">/</span>
            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $project->project_name }}</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $project->project_name }}</h1>
            @php
                $statusLabels = ['iniciado'=>'Iniciado','en_proceso'=>'En proceso','soporte'=>'Soporte','completado'=>'Completado','cancelado'=>'Cancelado'];
                $statusLabel = $statusLabels[$project->status] ?? $project->status;
                $badgeClass = match($project->status) {
                    'completado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                    'en_proceso' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400',
                    'soporte'    => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                    'cancelado'  => 'bg-gray-100 text-gray-500',
                    default      => 'bg-brand-50 text-brand-600',
                };
                $dotColor = match($project->status) {
                    'completado' => 'bg-success-500', 'en_proceso' => 'bg-blue-light-500',
                    'soporte'    => 'bg-warning-400', default => 'bg-brand-400',
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium {{ $badgeClass }} rounded-full">
                <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                {{ $statusLabel }}
            </span>
            @if($project->is_at_risk)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium bg-error-50 text-error-700 rounded-full dark:bg-error-500/10 dark:text-error-400 animate-pulse">⚠ En Riesgo</span>
            @endif
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $project->company->name ?? '—' }} · {{ $project->platform ?? 'Sin plataforma' }}</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('proyectos.edit', $project) }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
            Editar Proyecto
        </a>
        <button onclick="document.getElementById('modal-nota').classList.remove('hidden')" class="px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            + Agregar Nota
        </button>
        <form method="POST" action="{{ route('proyectos.destroy', $project) }}" onsubmit="return confirm('¿Eliminar este proyecto?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2.5 text-sm font-medium bg-error-500 text-white rounded-lg hover:bg-error-600 transition-colors">
                Eliminar
            </button>
        </form>
    </div>
</div>

{{-- Progress Band --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
    <div class="flex items-center justify-between mb-3">
        <span class="text-sm font-semibold text-gray-900 dark:text-white">Progreso del Proyecto</span>
        <div class="flex items-center gap-3">
            <span class="text-2xl font-bold text-brand-500">{{ $project->progress_percentage }}%</span>
            {{-- Quick progress update --}}
            <form method="POST" action="{{ route('proyectos.progress', $project) }}" class="flex items-center gap-2">
                @csrf @method('PATCH')
                <input type="number" name="progress_percentage" value="{{ $project->progress_percentage }}" min="0" max="100" class="w-16 px-2 py-1 text-xs border border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                <button type="submit" class="px-3 py-1 text-xs bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Actualizar</button>
            </form>
        </div>
    </div>
    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3 mb-3">
        <div class="bg-gradient-to-r from-brand-400 to-brand-600 h-3 rounded-full transition-all duration-700 relative" style="width: {{ $project->progress_percentage }}%">
            <div class="absolute -right-1 -top-0.5 w-4 h-4 rounded-full bg-white border-2 border-brand-500 shadow-sm"></div>
        </div>
    </div>
    <div class="flex items-center justify-between text-xs text-gray-400">
        <span>Inicio: {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '—' }}</span>
        <span class="{{ $project->is_overdue ? 'text-error-500 font-semibold' : '' }}">Fecha límite: {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : '—' }}{{ $project->is_overdue ? ' ⚠ VENCIDO' : '' }}</span>
    </div>
</div>

{{-- Info Grid --}}
<div class="grid grid-cols-12 gap-5 mb-6">
    {{-- Left: Details + Tasks --}}
    <div class="col-span-12 xl:col-span-8 space-y-5">
        {{-- Details Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Información del Proyecto</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">Bot asociado</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $project->bot_name ?? '—' }}</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">Sitio web</span>
                    @if($project->website_url)
                        <a href="{{ $project->website_url }}" target="_blank" class="text-sm font-medium text-brand-500 hover:underline">{{ $project->website_url }}</a>
                    @else <span class="text-sm font-medium text-gray-400">—</span> @endif
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">Servidor</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $project->server_hosting ?? '—' }}</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">Plataforma</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $project->platform ?? '—' }}</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">CEO / Contacto</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $project->ceo ?? '—' }}</span>
                </div>
                <div class="flex flex-col gap-0.5">
                    <span class="text-xs text-gray-400">Ingeniero Principal</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $project->primaryEngineer->name ?? '—' }}</span>
                </div>
            </div>
            @if($project->notes)
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <span class="text-xs text-gray-400">Notas generales</span>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $project->notes }}</p>
            </div>
            @endif
        </div>

        {{-- Tasks Section --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Tareas del Proyecto ({{ $project->tasks->count() }})</h3>
                <button onclick="document.getElementById('modal-tarea').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-brand-50 text-brand-500 rounded-lg hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:hover:bg-brand-500/20">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Nueva Tarea
                </button>
            </div>
            <div class="p-5 space-y-3">
                @forelse($project->tasks as $task)
                @php
                    $stateIcon = match($task->status) {
                        'completada' => '<svg class="text-success-500" width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="#d1fae5" stroke="#12b76a" stroke-width="1.5"/><path d="M8 12l3 3 5-5" stroke="#12b76a" stroke-width="1.5" stroke-linecap="round"/></svg>',
                        'en_progreso' => '<svg class="text-blue-light-500" width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="#e0f2fe" stroke="#0ba5ec" stroke-width="1.5"/><path d="M12 8v4l3 3" stroke="#0ba5ec" stroke-width="1.5" stroke-linecap="round"/></svg>',
                        'bloqueada' => '<svg class="text-error-500" width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" fill="#fee4e2" stroke="#f04438" stroke-width="1.5"/><path d="M12 8v4M12 16h.01" stroke="#f04438" stroke-width="1.5" stroke-linecap="round"/></svg>',
                        default => '<svg class="text-gray-400" width="18" height="18" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="#d0d5dd" stroke-width="1.5"/></svg>',
                    };
                    $prioColor = match($task->priority) {
                        'alta','critica' => 'bg-error-50 text-error-600 dark:bg-error-500/10 dark:text-error-400',
                        'media' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/10 dark:text-warning-400',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                    <div class="flex-shrink-0">{!! $stateIcon !!}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 {{ $task->status === 'completada' ? 'line-through text-gray-400' : '' }}">{{ $task->title }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs px-1.5 py-0.5 rounded {{ $prioColor }}">{{ ucfirst($task->priority) }}</span>
                            <span class="text-xs text-gray-400">{{ $task->assignedEngineer->name ?? '—' }}</span>
                        </div>
                    </div>
                    @if($task->progress > 0)
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div class="w-16 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5">
                            <div class="{{ $task->progress === 100 ? 'bg-success-500' : 'bg-blue-light-500' }} h-1.5 rounded-full" style="width: {{ $task->progress }}%"></div>
                        </div>
                        <span class="text-xs font-medium text-gray-500 w-6">{{ $task->progress }}%</span>
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">No hay tareas aún. <button onclick="document.getElementById('modal-tarea').classList.remove('hidden')" class="text-brand-500 hover:underline">Agregar primera tarea</button></p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Right: Team + Notes + Stats --}}
    <div class="col-span-12 xl:col-span-4 space-y-5">
        {{-- Team --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Equipo Asignado</h3>
            <div class="space-y-3">
                @if($project->primaryEngineer)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ substr($project->primaryEngineer->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $project->primaryEngineer->name }}</p>
                        <p class="text-xs text-gray-400">Ingeniero Principal</p>
                    </div>
                </div>
                @endif
                @if($project->backupEngineer)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-purple-500 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ substr($project->backupEngineer->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $project->backupEngineer->name }}</p>
                        <p class="text-xs text-gray-400">Ingeniero Backup</p>
                    </div>
                </div>
                @endif
                @if($project->ceo)
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-warning-400 flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-xs font-bold">{{ substr($project->ceo, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $project->ceo }}</p>
                        <p class="text-xs text-gray-400">CEO Cliente</p>
                    </div>
                </div>
                @endif
                @if(!$project->primaryEngineer && !$project->backupEngineer)
                <p class="text-xs text-gray-400 text-center py-2">Sin equipo asignado</p>
                @endif
            </div>
        </div>

        {{-- Notes --}}
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notas del Proyecto ({{ $project->notes()->count() }})</h3>
            </div>
            <div class="p-5 space-y-3 max-h-72 overflow-y-auto">
                @forelse($project->notes()->latest()->get() as $note)
                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center">
                            <span class="text-white text-xs">{{ substr($note->author->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $note->author->name ?? 'Usuario' }}</span>
                        <span class="text-xs text-gray-400 ml-auto">{{ $note->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $note->note }}</p>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">No hay notas aún</p>
                @endforelse
            </div>
            <div class="p-4 border-t border-gray-100 dark:border-gray-800">
                <form method="POST" action="{{ route('proyectos.notes.store', $project) }}" class="flex gap-2">
                    @csrf
                    <input type="text" name="note" placeholder="Agregar nota..." required class="flex-1 px-3 py-2 text-xs border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    <button type="submit" class="px-3 py-2 bg-brand-500 text-white text-xs rounded-lg hover:bg-brand-600 transition-colors">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2L15 22l-4-9-9-4 19-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Stats --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Resumen de Tareas</h3>
            @php
                $completadas = $project->tasks->where('status','completada')->count();
                $enProgreso  = $project->tasks->where('status','en_progreso')->count();
                $pendientes  = $project->tasks->where('status','pendiente')->count();
                $bloqueadas  = $project->tasks->where('status','bloqueada')->count();
            @endphp
            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 rounded-xl bg-success-50 dark:bg-success-500/10">
                    <p class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $completadas }}</p>
                    <p class="text-xs text-success-600 dark:text-success-500 mt-0.5">Completadas</p>
                </div>
                <div class="p-3 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10">
                    <p class="text-2xl font-bold text-blue-light-600 dark:text-blue-light-400">{{ $enProgreso }}</p>
                    <p class="text-xs text-blue-light-600 dark:text-blue-light-500 mt-0.5">En progreso</p>
                </div>
                <div class="p-3 rounded-xl bg-gray-100 dark:bg-gray-800">
                    <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $pendientes }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">Pendientes</p>
                </div>
                <div class="p-3 rounded-xl bg-error-50 dark:bg-error-500/10">
                    <p class="text-2xl font-bold text-error-600 dark:text-error-400">{{ $bloqueadas }}</p>
                    <p class="text-xs text-error-600 dark:text-error-500 mt-0.5">Cancelados</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Agregar Nota --}}
<div id="modal-nota" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agregar Nota</h3>
            <button onclick="document.getElementById('modal-nota').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('proyectos.notes.store', $project) }}">
            @csrf
            <textarea name="note" rows="4" placeholder="Escribe tu nota aquí..." required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none"></textarea>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-nota').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Guardar nota</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Nueva Tarea --}}
<div id="modal-tarea" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva Tarea</h3>
            <button onclick="document.getElementById('modal-tarea').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('tareas.store') }}">
            @csrf
            <input type="hidden" name="project_id" value="{{ $project->id }}">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                    <input type="text" name="title" required placeholder="Nombre de la tarea" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción</label>
                    <textarea name="description" rows="2" placeholder="Descripción opcional..." class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Prioridad</label>
                        <select name="priority" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="baja">Baja</option>
                            <option value="media" selected>Media</option>
                            <option value="alta">Alta</option>
                            <option value="critica">Crítica</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                        <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="pendiente" selected>Pendiente</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="completada">Completada</option>
                            <option value="bloqueada">Cancelado</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ingeniero asignado</label>
                    <select name="assigned_engineer_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Sin asignar</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha inicio</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha límite</label>
                        <input type="date" name="due_date" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Progreso (%)</label>
                    <input type="number" name="progress" value="0" min="0" max="100" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-tarea').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Crear Tarea</button>
            </div>
        </form>
    </div>
</div>
@endsection
