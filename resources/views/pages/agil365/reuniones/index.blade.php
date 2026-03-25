@extends('layouts.app')

@section('content')
<style>
/* ── Calendar panel overrides ─────────────────────────────────── */
.fc-event { cursor:pointer; border:none !important; border-radius:5px !important; font-size:.72rem; font-weight:500; padding:2px 6px !important; }
.fc-event:hover { opacity:.85; }
.fc-theme-standard td,.fc-theme-standard th { border-color:#f3f4f6; }
.fc-col-header-cell-cushion { font-size:.74rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#6b7280; text-decoration:none !important; }
.fc-daygrid-day-number { font-size:.82rem; font-weight:600; text-decoration:none !important; }
.fc-day-today { background:rgba(59,130,246,.05) !important; }
.fc .fc-toolbar-title { font-size:1.05rem; font-weight:700; }
.dark .fc-theme-standard td,.dark .fc-theme-standard th { border-color:#374151; }
.dark .fc-col-header-cell-cushion,.dark .fc-daygrid-day-number { color:#9ca3af; }
.dark .fc-toolbar-title { color:#f9fafb; }
.dark .fc-button { background:#1f2937 !important; border-color:#374151 !important; color:#d1d5db !important; }
.dark .fc-button:hover { background:#374151 !important; }
.dark .fc-button-active { background:#374151 !important; border-color:#6b7280 !important; }
.dark .fc-day-today { background:rgba(59,130,246,.08) !important; }
.dark .fc-daygrid-day { color:#d1d5db; }
.hist-row { transition:background .15s; }
.hist-row:hover { background:rgba(59,130,246,.04); }
</style>

{{-- Flash messages --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400 flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center justify-between">
    <span>✗ {{ session('error') }}</span>
</div>
@endif

{{-- Page header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reuniones y Calendario</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de reuniones internas y con clientes</p>
    </div>
    <button id="btn-nueva-reunion"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-all shadow-sm">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Nueva Reunión
    </button>
</div>

{{-- Tab bar --}}
<div class="flex gap-1 mb-5 bg-gray-100 dark:bg-gray-800 p-1 rounded-xl w-fit">
    <button id="tab-cal"  onclick="switchTab('cal')"
        class="px-4 py-2 text-sm font-medium rounded-lg transition-all bg-white dark:bg-gray-900 text-gray-900 dark:text-white shadow-sm">
        📅 Calendario
    </button>
    <button id="tab-hist" onclick="switchTab('hist')"
        class="px-4 py-2 text-sm font-medium rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700">
        📋 Historial
    </button>
</div>

{{-- ════  TAB: CALENDARIO  ════ --}}
<div id="pane-cal">
    {{-- Filter bar --}}
    <div class="flex flex-wrap gap-3 mb-4 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl items-center">
        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Filtrar:</span>
        <select id="filter-type" onchange="applyCalendarFilters()"
            class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los tipos</option>
            <option value="reunion">Solo Reuniones</option>
            <option value="tarea">Solo Tareas</option>
        </select>
        <select id="filter-project" onchange="applyCalendarFilters()"
            class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los proyectos</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
            @endforeach
        </select>
        <select id="filter-status" onchange="applyCalendarFilters()"
            class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los estados</option>
            <option value="programada">Programada</option>
            <option value="completada">Completada</option>
            <option value="cancelada">Cancelada</option>
            <option value="pendiente">Pendiente (Tarea)</option>
        </select>
        <button onclick="resetFilters()" class="px-3 py-1.5 text-xs rounded-lg border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-brand-600 transition-colors">↺ Limpiar</button>
        <div class="flex flex-wrap gap-3 text-xs text-gray-400 ml-auto items-center">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Reunión</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>Completada</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>Tarea</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>Cancelada</span>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5">
        <div id="calendar"></div>
    </div>
</div>

{{-- ════  TAB: HISTORIAL  ════ --}}
<div id="pane-hist" class="hidden">
    <div class="flex flex-wrap gap-3 mb-4 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl">
        <div class="relative flex-1 min-w-[200px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M20 20l-3-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <input id="hist-search" type="text" placeholder="Buscar reunión..." oninput="filterHistory()"
                class="w-full pl-8 pr-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
        </div>
        <select id="hist-project" onchange="filterHistory()"
            class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los proyectos</option>
            @foreach($projects as $p)
                <option value="{{ $p->id }}">{{ $p->project_name }}</option>
            @endforeach
        </select>
        <select id="hist-status" onchange="filterHistory()"
            class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los estados</option>
            <option value="programada">Programada</option>
            <option value="completada">Completada</option>
            <option value="cancelada">Cancelada</option>
        </select>
        <span class="text-xs text-gray-400 flex items-center gap-1"><span id="hist-count">{{ $allMeetings->count() }}</span> reuniones</span>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Reunión</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 hidden sm:table-cell">Proyecto</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 hidden md:table-cell">Fecha</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400 hidden lg:table-cell">Participantes</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Estado</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody id="hist-tbody" class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($allMeetings as $m)
                @php
                    $sc = match($m->status) {
                        'completada' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400',
                        'cancelada'  => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
                        default      => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400',
                    };
                @endphp
                <tr class="hist-row"
                    data-title="{{ strtolower($m->title) }}"
                    data-desc="{{ strtolower($m->description ?? '') }}"
                    data-project="{{ $m->project_id }}"
                    data-status="{{ $m->status }}">
                    <td class="px-5 py-3.5">
                        <button onclick="openDetailModal({{ $m->id }})" class="text-left group">
                            <p class="font-medium text-gray-800 dark:text-gray-200 group-hover:text-brand-600 transition-colors">{{ $m->title }}</p>
                            @if($m->location)
                            <p class="text-xs text-gray-400 mt-0.5">📍 {{ $m->location }}</p>
                            @endif
                        </button>
                    </td>
                    <td class="px-5 py-3.5 hidden sm:table-cell text-gray-600 dark:text-gray-400">{{ $m->project->project_name ?? '—' }}</td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-gray-600 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($m->meeting_date)->format('d/m/Y') }}
                        @if($m->meeting_time)
                        <span class="text-gray-400 ml-1">· {{ \Carbon\Carbon::parse($m->meeting_time)->format('h:i A') }}</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <div class="flex -space-x-2">
                            @forelse($m->participants->take(4) as $par)
                            <span title="{{ $par->name }}"
                                class="w-7 h-7 rounded-full bg-brand-500 flex items-center justify-center text-white text-[10px] font-bold border-2 border-white dark:border-gray-900">
                                {{ strtoupper(substr($par->name,0,2)) }}
                            </span>
                            @empty
                            <span class="text-xs text-gray-400">—</span>
                            @endforelse
                            @if($m->participants->count() > 4)
                            <span class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-gray-900">+{{ $m->participants->count()-4 }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $sc }}">{{ ucfirst($m->status) }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="openDetailModal({{ $m->id }})" title="Ver detalles"
                                class="p-1.5 text-gray-400 hover:text-brand-500 rounded-lg transition-colors">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/></svg>
                            </button>
                            <button onclick="openEditMeeting({{ $m->id }},'{{ addslashes($m->title) }}','{{ $m->meeting_date->format('Y-m-d') }}','{{ $m->meeting_time }}','{{ $m->project_id }}','{{ $m->status }}')"
                                title="Editar" class="p-1.5 text-gray-400 hover:text-amber-500 rounded-lg transition-colors">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.5"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5"/></svg>
                            </button>
                            <form method="POST" action="{{ route('reuniones.destroy', $m) }}" onsubmit="return confirm('¿Eliminar esta reunión?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" title="Eliminar" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg transition-colors">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="1.5"/><path d="M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No hay reuniones registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div id="hist-empty" class="hidden px-5 py-12 text-center text-sm text-gray-400">No se encontraron reuniones con esos filtros.</div>
    </div>
</div>

{{-- ════  MODALS  ════ --}}
@push('modals')

{{-- MODAL: Nueva Reunión --}}
<div id="modal-nueva-reunion" class="hidden fixed inset-0 z-[99999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-nueva-reunion').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nueva Reunión</h3>
                <p class="text-xs text-gray-400 mt-0.5">Agenda una reunión interna o con clientes</p>
            </div>
            <button onclick="document.getElementById('modal-nueva-reunion').classList.add('hidden')"
                class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">✕</button>
        </div>
        <form method="POST" action="{{ route('reuniones.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                    <input type="text" name="title" id="new-title" required placeholder="Ej: Revisión semanal"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha *</label>
                        <input type="date" name="meeting_date" id="new-date" required value="{{ date('Y-m-d') }}"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Hora *</label>
                        <input type="time" name="meeting_time" required
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proyecto</label>
                    <select name="project_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Sin proyecto</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Participantes</label>
                    <select name="participants[]" multiple style="height:88px"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Ctrl+Click para seleccionar varios</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación / Link</label>
                    <input type="text" name="location" placeholder="Sala, Zoom link, etc."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción / Notas</label>
                    <textarea name="description" rows="3" placeholder="Agenda, objetivos..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modal-nueva-reunion').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm">Crear Reunión</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Editar Reunión --}}
<div id="modal-edit-reunion" class="hidden fixed inset-0 z-[99999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-edit-reunion').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Reunión</h3>
            <button onclick="document.getElementById('modal-edit-reunion').classList.add('hidden')"
                class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">✕</button>
        </div>
        <form id="edit-reunion-form" method="POST" action="">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Título *</label>
                    <input type="text" name="title" id="edit-title" required
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                        <input type="date" name="meeting_date" id="edit-date"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Hora</label>
                        <input type="time" name="meeting_time" id="edit-time"
                            class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proyecto</label>
                    <select name="project_id" id="edit-project"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Sin proyecto</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Estado</label>
                    <select name="status" id="edit-status"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="programada">Programada</option>
                        <option value="completada">Completada</option>
                        <option value="cancelada">Cancelada</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modal-edit-reunion').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-800">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Detalle --}}
<div id="modal-detail" class="hidden fixed inset-0 z-[99999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-detail').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[92vh] overflow-y-auto">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
                <span id="detail-badge" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mb-2"></span>
                <h3 id="detail-title" class="text-lg font-bold text-gray-900 dark:text-white leading-tight"></h3>
            </div>
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                class="ml-3 p-1.5 text-gray-400 hover:text-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 flex-shrink-0">✕</button>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex items-center gap-3 text-gray-600 dark:text-gray-400">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 2v4M8 2v4M3 10h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                <span id="detail-date">—</span>
            </div>
            <div id="detail-location-wrap" class="hidden items-center gap-3 text-gray-600 dark:text-gray-400">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="flex-shrink-0"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
                <span id="detail-location"></span>
            </div>
            <div id="detail-project-wrap" class="hidden items-center gap-3 text-gray-600 dark:text-gray-400">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="flex-shrink-0"><rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="1.5"/></svg>
                <span id="detail-project"></span>
            </div>
            <div id="detail-participants-wrap" class="hidden">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Participantes</p>
                <div id="detail-participants" class="flex flex-wrap gap-2"></div>
            </div>
            <div id="detail-desc-wrap" class="hidden">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notas</p>
                <p id="detail-desc" class="text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 rounded-xl p-3 leading-relaxed"></p>
            </div>
        </div>
        <div class="flex gap-2 mt-5">
            <button id="detail-edit-btn" class="flex-1 px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Editar</button>
            <button onclick="document.getElementById('modal-detail').classList.add('hidden')"
                class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-800">Cerrar</button>
        </div>
    </div>
</div>

@endpush

{{-- ════  INLINE SCRIPT (no depende de CDN — usa window.ReunionesCalendar de Vite)  ════ --}}
@push('scripts')
<script>
// Datos del servidor
const ALL_MEETINGS = @json($allMeetings);
const ALL_TASKS    = @json($allTasks);
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Construye el array de eventos aplicando filtros
function buildEvents(typeFilter, projectFilter, statusFilter) {
    const events = [];
    const rColors = { programada:'#3b82f6', completada:'#10b981', cancelada:'#9ca3af' };
    const tColors = { completada:'#059669', en_progreso:'#6366f1', bloqueada:'#ef4444', pendiente:'#f59e0b' };

    if (!typeFilter || typeFilter === 'reunion') {
        ALL_MEETINGS.forEach(m => {
            if (projectFilter && String(m.project_id) !== projectFilter) return;
            if (statusFilter  && m.status !== statusFilter) return;
            events.push({
                id: 'm_' + m.id,
                title: '📅 ' + m.title,
                start: m.meeting_date + (m.meeting_time ? 'T' + m.meeting_time : ''),
                backgroundColor: rColors[m.status] || '#3b82f6',
                borderColor: 'transparent',
                extendedProps: { kind: 'reunion', data: m }
            });
        });
    }
    if (!typeFilter || typeFilter === 'tarea') {
        ALL_TASKS.forEach(t => {
            if (!t.due_date) return;
            if (projectFilter && String(t.project_id) !== projectFilter) return;
            if (statusFilter  && t.status !== statusFilter) return;
            events.push({
                id: 't_' + t.id,
                title: '✅ ' + t.title,
                start: t.due_date,
                backgroundColor: tColors[t.status] || '#f59e0b',
                borderColor: 'transparent',
                extendedProps: { kind: 'tarea', data: t }
            });
        });
    }
    return events;
}

// Espera a que window.ReunionesCalendar esté listo (lo inyecta Vite async)
function waitForCalendar(cb, retries) {
    retries = retries === undefined ? 30 : retries;
    if (window.ReunionesCalendar) { cb(); return; }
    if (retries <= 0) { console.error('ReunionesCalendar no se cargó'); return; }
    setTimeout(() => waitForCalendar(cb, retries - 1), 100);
}

document.addEventListener('DOMContentLoaded', function () {
    // Botón "Nueva Reunión"
    document.getElementById('btn-nueva-reunion').addEventListener('click', function () {
        const d = document.getElementById('new-date');
        if (d) d.value = new Date().toISOString().substring(0, 10);
        document.getElementById('modal-nueva-reunion').classList.remove('hidden');
    });

    // Inicializar FullCalendar cuando su módulo Vite esté listo
    waitForCalendar(function () {
        window.ReunionesCalendar.init(buildEvents('', '', ''), {
            onEventClick: function (info) {
                const p = info.event.extendedProps;
                if (p.kind === 'reunion') openDetailFromData(p.data);
                else openTaskDetail(p.data);
            },
            onDateClick: function (info) {
                const d = document.getElementById('new-date');
                if (d) d.value = info.dateStr;
                document.getElementById('modal-nueva-reunion').classList.remove('hidden');
            },
            onEventDrop: function (info) {
                const p       = info.event.extendedProps;
                const newDate = info.event.startStr.substring(0, 10);
                const name    = info.event.title.replace(/^[^\s]+\s/, '');
                if (!confirm('¿Mover "' + name + '" al ' + newDate + '?')) { info.revert(); return; }
                const url = p.kind === 'reunion'
                    ? '/reuniones/' + p.data.id + '/actualizar-fecha'
                    : '/tareas/'    + p.data.id + '/actualizar-fecha';
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ new_date: newDate })
                })
                .then(r => r.json())
                .then(d => { if (!d.success) { alert(d.message || 'Error.'); info.revert(); } })
                .catch(() => { alert('Error de red.'); info.revert(); });
            }
        });
    });
});

// ── Filtros calendario ───────────────────────────────────────────────────────
function applyCalendarFilters() {
    if (!window.ReunionesCalendar) return;
    window.ReunionesCalendar.setEvents(buildEvents(
        document.getElementById('filter-type').value,
        document.getElementById('filter-project').value,
        document.getElementById('filter-status').value
    ));
}
function resetFilters() {
    ['filter-type','filter-project','filter-status'].forEach(id => document.getElementById(id).value = '');
    applyCalendarFilters();
}

// ── Tab switch ───────────────────────────────────────────────────────────────
function switchTab(tab) {
    ['cal','hist'].forEach(t => {
        document.getElementById('pane-' + t).classList.toggle('hidden', t !== tab);
        const btn = document.getElementById('tab-' + t);
        if (t === tab) {
            btn.classList.add('bg-white','dark:bg-gray-900','text-gray-900','dark:text-white','shadow-sm');
            btn.classList.remove('text-gray-500','dark:text-gray-400','hover:text-gray-700');
        } else {
            btn.classList.remove('bg-white','dark:bg-gray-900','text-gray-900','dark:text-white','shadow-sm');
            btn.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700');
        }
    });
    if (tab === 'cal' && window.ReunionesCalendar) setTimeout(() => window.ReunionesCalendar.updateSize(), 50);
}

// ── Historial filter ─────────────────────────────────────────────────────────
function filterHistory() {
    const q      = document.getElementById('hist-search').value.toLowerCase();
    const proj   = document.getElementById('hist-project').value;
    const status = document.getElementById('hist-status').value;
    const rows   = document.querySelectorAll('#hist-tbody tr.hist-row');
    let visible  = 0;
    rows.forEach(row => {
        const ok = (!q      || row.dataset.title.includes(q) || row.dataset.desc.includes(q))
                && (!proj   || row.dataset.project === proj)
                && (!status || row.dataset.status  === status);
        row.classList.toggle('hidden', !ok);
        if (ok) visible++;
    });
    document.getElementById('hist-count').textContent = visible;
    document.getElementById('hist-empty').classList.toggle('hidden', visible > 0);
}

// ── Edit modal ───────────────────────────────────────────────────────────────
function openEditMeeting(id, title, date, time, projectId, status) {
    document.getElementById('edit-reunion-form').action = '/reuniones/' + id;
    document.getElementById('edit-title').value   = title;
    document.getElementById('edit-date').value    = date;
    document.getElementById('edit-time').value    = time ? time.substring(0, 5) : '';
    document.getElementById('edit-project').value = projectId || '';
    document.getElementById('edit-status').value  = status || 'programada';
    document.getElementById('modal-detail').classList.add('hidden');
    document.getElementById('modal-edit-reunion').classList.remove('hidden');
}

// ── Detail modal ─────────────────────────────────────────────────────────────
function openDetailModal(id) {
    const m = ALL_MEETINGS.find(x => x.id == id);
    if (m) openDetailFromData(m);
}
function openDetailFromData(m) {
    const scMap = { programada:'bg-blue-50 text-blue-700', completada:'bg-emerald-50 text-emerald-700', cancelada:'bg-gray-100 text-gray-600' };
    const lMap  = { programada:'Programada', completada:'Completada', cancelada:'Cancelada' };
    document.getElementById('detail-badge').textContent = '📅 Reunión · ' + (lMap[m.status] || m.status);
    document.getElementById('detail-badge').className   = 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mb-2 ' + (scMap[m.status] || scMap.programada);
    document.getElementById('detail-title').textContent = m.title;
    document.getElementById('detail-date').textContent  = (m.meeting_date||'').substring(0,10) + (m.meeting_time ? ' · ' + m.meeting_time.substring(0,5) : '');

    const locWrap = document.getElementById('detail-location-wrap');
    if (m.location) { document.getElementById('detail-location').textContent = m.location; locWrap.classList.replace('hidden','flex'); }
    else locWrap.classList.replace('flex','hidden');

    const projWrap = document.getElementById('detail-project-wrap');
    const pName = m.project ? m.project.project_name : null;
    if (pName) { document.getElementById('detail-project').textContent = pName; projWrap.classList.replace('hidden','flex'); }
    else projWrap.classList.replace('flex','hidden');

    const partWrap = document.getElementById('detail-participants-wrap');
    const partEl   = document.getElementById('detail-participants');
    if (m.participants && m.participants.length) {
        partEl.innerHTML = m.participants.map(p =>
            `<span class="flex items-center gap-1.5 px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded-full text-xs text-gray-700 dark:text-gray-300">
                <span class="w-5 h-5 rounded-full bg-brand-500 flex items-center justify-center text-white text-[9px] font-bold">${p.name.substring(0,2).toUpperCase()}</span>
                ${p.name}</span>`
        ).join('');
        partWrap.classList.remove('hidden');
    } else partWrap.classList.add('hidden');

    const descWrap = document.getElementById('detail-desc-wrap');
    if (m.description) { document.getElementById('detail-desc').textContent = m.description; descWrap.classList.remove('hidden'); }
    else descWrap.classList.add('hidden');

    document.getElementById('detail-edit-btn').onclick = () =>
        openEditMeeting(m.id, m.title, (m.meeting_date||'').substring(0,10), m.meeting_time||'', m.project_id, m.status);
    document.getElementById('detail-edit-btn').classList.remove('hidden');
    document.getElementById('modal-detail').classList.remove('hidden');
}
function openTaskDetail(t) {
    const lMap = { pendiente:'Pendiente', en_progreso:'En progreso', completada:'Completada', bloqueada:'Bloqueada' };
    document.getElementById('detail-badge').textContent = '✅ Tarea · ' + (lMap[t.status] || t.status);
    document.getElementById('detail-badge').className   = 'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium mb-2 bg-amber-50 text-amber-700';
    document.getElementById('detail-title').textContent = t.title;
    document.getElementById('detail-date').textContent  = 'Vence: ' + ((t.due_date||'').substring(0,10)||'—');
    document.getElementById('detail-location-wrap').classList.add('hidden');
    const projWrap = document.getElementById('detail-project-wrap');
    if (t.project) { document.getElementById('detail-project').textContent = t.project.project_name; projWrap.classList.replace('hidden','flex'); }
    else projWrap.classList.replace('flex','hidden');
    document.getElementById('detail-participants-wrap').classList.add('hidden');
    const descWrap = document.getElementById('detail-desc-wrap');
    if (t.description) { document.getElementById('detail-desc').textContent = t.description; descWrap.classList.remove('hidden'); }
    else descWrap.classList.add('hidden');
    document.getElementById('detail-edit-btn').classList.add('hidden');
    document.getElementById('modal-detail').classList.remove('hidden');
}
</script>
@endpush
@endsection
