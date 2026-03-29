@extends('layouts.app')

@section('content')
<style>
/* ── FullCalendar – modern ClickUp-style event cards ─────────────── */
.fc-event {
    cursor: pointer !important;
    border: none !important;
    background: transparent !important;
    padding: 1px 2px !important;
    margin-bottom: 1px !important;
}
.fc-event:hover { opacity: 1 !important; }
.fc-event .fc-event-main { padding: 0 !important; }

/* Custom event card */
.cal-event-card {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 3px 7px 3px 5px;
    border-radius: 6px;
    font-size: .72rem;
    font-weight: 600;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    border-left: 3px solid rgba(0,0,0,.25);
    transition: filter .15s, transform .15s;
    max-width: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}
.cal-event-card:hover {
    filter: brightness(.93);
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,.14);
}
.cal-event-card .ev-icon { flex-shrink: 0; font-size: .75rem; }
.cal-event-card .ev-body { overflow: hidden; }
.cal-event-card .ev-title {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 600;
}
.cal-event-card .ev-time {
    display: block;
    font-size: .66rem;
    opacity: .8;
    font-weight: 500;
    margin-top: 0px;
}

/* Calendar grid */
.fc-theme-standard td, .fc-theme-standard th { border-color: #e5e7eb; }
.fc-daygrid-day { min-height: 90px; }
.fc-col-header-cell-cushion {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #6b7280;
    text-decoration: none !important;
    padding: 8px 4px;
}
.fc-daygrid-day-number {
    font-size: .8rem;
    font-weight: 700;
    color: #374151;
    text-decoration: none !important;
    padding: 4px 8px;
}
.fc-day-today { background: rgba(59,130,246,.05) !important; }
.fc-day-today .fc-daygrid-day-number {
    background: #3b82f6;
    color: #fff !important;
    border-radius: 50%;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 3px;
    padding: 0;
}
.fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 800; letter-spacing: -.01em; }
.fc-button {
    border-radius: 8px !important;
    font-size: .78rem !important;
    font-weight: 600 !important;
    padding: 5px 12px !important;
}
.fc-more-link {
    font-size: .68rem;
    font-weight: 600;
    color: #3b82f6;
    padding: 1px 5px;
    background: #eff6ff;
    border-radius: 4px;
}

/* Dark mode */
.dark .fc-theme-standard td, .dark .fc-theme-standard th { border-color: #374151; }
.dark .fc-col-header-cell-cushion, .dark .fc-daygrid-day-number { color: #9ca3af; }
.dark .fc-day-today .fc-daygrid-day-number { background: #3b82f6; color:#fff !important; }
.dark .fc-toolbar-title { color: #f9fafb; }
.dark .fc-button { background: #1f2937 !important; border-color: #374151 !important; color: #d1d5db !important; }
.dark .fc-button:hover { background: #374151 !important; }
.dark .fc-button-active { background: #374151 !important; border-color: #6b7280 !important; }
.dark .fc-day-today { background: rgba(59,130,246,.07) !important; }
.dark .fc-daygrid-day { color: #d1d5db; }
.dark .fc-more-link { background: rgba(59,130,246,.15); color: #60a5fa; }

/* History table rows */
.hist-row { transition: background .15s; }
.hist-row:hover { background: rgba(59,130,246,.04); }

/* Detail modal animations */
@keyframes modalIn {
    from { opacity:0; transform: scale(.96) translateY(12px); }
    to   { opacity:1; transform: scale(1)  translateY(0); }
}
.modal-animate { animation: modalIn .22s cubic-bezier(.34,1.2,.64,1) both; }

/* Custom Event Tooltip */
#cal-event-tooltip {
    position: fixed;
    z-index: 100000;
    pointer-events: none;
    opacity: 0;
    transform: scale(0.95);
    transition: opacity 0.15s ease, transform 0.15s ease;
    filter: drop-shadow(0 4px 12px rgba(0,0,0,0.15));
}
#cal-event-tooltip.show {
    opacity: 1;
    transform: scale(1);
}

/* ── Participant Picker ──────────────────────────────────────── */
.participant-picker { position: relative; }
.picker-chip {
    display: inline-flex; align-items: center; gap: 3px;
    padding: 2px 6px 2px 4px; border-radius: 999px;
    font-size: .7rem; font-weight: 600; color: #fff; white-space: nowrap;
}
.picker-chip-remove {
    display: flex; align-items: center; justify-content: center;
    width: 13px; height: 13px; border-radius: 50%;
    background: rgba(0,0,0,.25); border: none; color: #fff;
    font-size: 9px; cursor: pointer; padding: 0; flex-shrink: 0;
}
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
    <button id="tab-audit" onclick="switchTab('audit')"
        class="px-4 py-2 text-sm font-medium rounded-lg transition-all text-gray-500 dark:text-gray-400 hover:text-gray-700">
        🔄 Trazabilidad (Audit)
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
        <div id="calendar" data-reuniones="true"></div>
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

{{-- ════  TAB: TRAZABILIDAD (AUDIT)  ════ --}}
<div id="pane-audit" class="hidden">
    <div class="flex flex-wrap gap-3 mb-4 p-4 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl items-center">
        <svg class="text-gray-400 ml-2" width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        <h2 class="font-semibold text-gray-700 dark:text-gray-200 mr-4">Registro de Cambios</h2>
        
        <select id="audit-meeting" onchange="filterAudit()"
            class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todas las reuniones</option>
            @foreach($allMeetings as $m)
                <option value="{{ $m->id }}">{{ $m->title }}</option>
            @endforeach
        </select>
        
        <select id="audit-user" onchange="filterAudit()"
            class="px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none">
            <option value="">Todos los usuarios</option>
            @foreach($engineers as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-6 overflow-hidden">
        <div class="relative pl-6 border-l-2 border-gray-100 dark:border-gray-800 space-y-8" id="audit-timeline">
            @forelse($meetingLogs as $log)
                @php
                    $colorMap = [
                        'creada' => 'text-emerald-500 bg-emerald-100 dark:bg-emerald-500/20',
                        'editada' => 'text-blue-500 bg-blue-100 dark:bg-blue-500/20',
                        'estado_cambiado' => 'text-amber-500 bg-amber-100 dark:bg-amber-500/20',
                        'fecha_cambiada' => 'text-purple-500 bg-purple-100 dark:bg-purple-500/20',
                        'eliminada' => 'text-red-500 bg-red-100 dark:bg-red-500/20',
                    ];
                    $dotColor = $colorMap[$log->action] ?? 'text-gray-500 bg-gray-100 dark:bg-gray-500/20';
                    // Parse markdown carefully due to strict escaping in blade
                    $desc = Str::inlineMarkdown($log->human_description);
                @endphp
                <div class="relative audit-row transition-all duration-300" data-meeting="{{ $log->meeting_id }}" data-user="{{ $log->user_id }}">
                    <span class="absolute -left-[33px] flex items-center justify-center w-6 h-6 rounded-full ring-4 ring-white dark:ring-gray-900 {{ explode(' ', $dotColor)[1] }}">
                        <span class="w-2 h-2 rounded-full {{ explode(' ', $dotColor)[0] }} bg-current"></span>
                    </span>
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between mb-1 gap-2">
                        <div>
                            <p class="text-[13px] text-gray-800 dark:text-gray-200">{!! $desc !!}</p>
                            @if($log->meeting)
                                <p class="text-xs font-medium text-brand-600 dark:text-brand-400 mt-0.5">{{ $log->meeting->title }}</p>
                            @endif
                        </div>
                        <time class="text-xs text-gray-400 font-medium whitespace-nowrap">{{ $log->created_at->format('d M Y, h:i A') }}</time>
                    </div>
                    @if($log->reason)
                        <div class="mt-2 text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700/50 inline-block">
                            <span class="font-semibold">Motivo:</span> {{ $log->reason }}
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 text-center py-4">No hay registros de cambios.</p>
            @endforelse
        </div>
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
                    {{-- Custom multi-select picker --}}
                    <div class="participant-picker" id="picker-new">
                        {{-- Hidden inputs container --}}
                        <div id="picker-new-inputs"></div>
                        {{-- Chips: selected users --}}
                        <div id="picker-new-chips"
                            class="min-h-[40px] flex flex-wrap gap-1.5 px-2.5 py-2 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 cursor-pointer transition-colors hover:border-brand-400"
                            onclick="togglePickerDropdown('new')">
                            <span id="picker-new-placeholder" class="text-sm text-gray-400 flex items-center gap-1.5 select-none">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="1.5"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="1.5"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                Seleccionar participantes…
                            </span>
                        </div>
                        {{-- Dropdown --}}
                        <div id="picker-new-dropdown"
                            class="hidden absolute z-[200] mt-1 w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
                            style="max-height:220px">
                            <div class="p-2 border-b border-gray-100 dark:border-gray-800">
                                <input type="text" placeholder="Buscar…" oninput="filterPicker('new', this.value)"
                                    class="w-full px-3 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 dark:text-gray-300 focus:outline-none focus:ring-1 focus:ring-brand-500"
                                    onclick="event.stopPropagation()">
                            </div>
                            <div id="picker-new-list" class="overflow-y-auto" style="max-height:160px">
                                @foreach($engineers as $eng)
                                <label id="picker-new-item-{{ $eng->id }}"
                                    class="flex items-center gap-2.5 px-3 py-2 cursor-pointer hover:bg-brand-50 dark:hover:bg-gray-800 transition-colors"
                                    onclick="event.stopPropagation(); togglePickerItem('new', {{ $eng->id }}, '{{ addslashes($eng->name) }}')">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-white text-[10px] font-bold" style="background: hsl({{ ($loop->index * 47) % 360 }}, 65%, 55%)">
                                        {{ strtoupper(substr($eng->name, 0, 2)) }}
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 flex-1">{{ $eng->name }}</span>
                                    <span id="picker-new-check-{{ $eng->id }}" class="hidden text-brand-500">
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
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
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ubicación / Link</label>
                    <input type="text" name="location" id="edit-location" placeholder="Sala, Zoom link, etc."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción / Notas</label>
                    <textarea name="description" id="edit-description" rows="3" placeholder="Agenda, objetivos..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo / Razón del cambio (opcional)</label>
                    <textarea name="reason" id="edit-reason" rows="2" placeholder="Ej: Se aplazó por solicitud del cliente..."
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div id="edit-error-msg" class="hidden mb-3 text-xs text-red-600 bg-red-50 border border-red-200 rounded-lg px-3 py-2"></div>
            <div class="flex items-center justify-end gap-2 mt-5">
                <button type="button" onclick="document.getElementById('modal-edit-reunion').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-800">Cancelar</button>
                <button type="button" id="btn-save-edit" onclick="saveEditMeeting()"
                    class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Detalle (Premium redesign) --}}
<div id="modal-detail" class="hidden fixed inset-0 z-[99999] flex items-end sm:items-center justify-center p-0 sm:p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>
    <div class="modal-animate relative bg-white dark:bg-gray-900 rounded-t-2xl sm:rounded-2xl shadow-2xl w-full sm:max-w-lg max-h-[92vh] overflow-y-auto">

        {{-- Colored header band --}}
        <div id="detail-header-band" class="relative rounded-t-2xl sm:rounded-t-2xl p-5 pb-12" style="background: linear-gradient(135deg,#3b82f6,#6366f1)">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <span id="detail-type-badge" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/20 text-white mb-3"></span>
                    <h3 id="detail-title" class="text-xl font-bold text-white leading-tight"></h3>
                </div>
                <button onclick="closeDetailModal()" class="flex-shrink-0 ml-3 w-8 h-8 flex items-center justify-center rounded-full bg-white/20 hover:bg-white/35 text-white transition-colors text-sm">✕</button>
            </div>
        </div>

        {{-- Status badge overlapping the header --}}
        <div class="relative -mt-6 mx-5 mb-4">
            <div id="detail-status-card" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-xl shadow-md px-4 py-3 border border-gray-100 dark:border-gray-700">
                <span id="detail-status-dot" class="w-3 h-3 rounded-full flex-shrink-0" style="background:#3b82f6"></span>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Estado</p>
                    <p id="detail-status-label" class="text-sm font-semibold text-gray-800 dark:text-gray-100">—</p>
                </div>
                <div class="border-l border-gray-100 dark:border-gray-700 pl-3 flex-shrink-0">
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Fecha</p>
                    <p id="detail-date" class="text-sm font-semibold text-gray-800 dark:text-gray-100">—</p>
                </div>
            </div>
        </div>

        {{-- Body details --}}
        <div class="px-5 space-y-4 pb-2">

            {{-- Location --}}
            <div id="detail-location-wrap" class="hidden items-center gap-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/60 rounded-xl px-4 py-3">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="flex-shrink-0 text-indigo-400"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="9" r="2.5" stroke="currentColor" stroke-width="1.5"/></svg>
                <span id="detail-location" class="font-medium"></span>
            </div>

            {{-- Project --}}
            <div id="detail-project-wrap" class="hidden items-center gap-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/60 rounded-xl px-4 py-3">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" class="flex-shrink-0 text-purple-400"><rect x="2" y="7" width="20" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2" stroke="currentColor" stroke-width="1.5"/></svg>
                <div>
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Proyecto</p>
                    <p id="detail-project" class="font-semibold text-gray-800 dark:text-gray-100"></p>
                </div>
            </div>

            {{-- Participants --}}
            <div id="detail-participants-wrap" class="hidden bg-gray-50 dark:bg-gray-800/60 rounded-xl px-4 py-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-3 flex items-center gap-2">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" stroke="currentColor" stroke-width="2"/><circle cx="9" cy="7" r="4" stroke="currentColor" stroke-width="2"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Participantes
                </p>
                <div id="detail-participants" class="flex flex-wrap gap-2"></div>
            </div>

            {{-- Description --}}
            <div id="detail-desc-wrap" class="hidden bg-gray-50 dark:bg-gray-800/60 rounded-xl px-4 py-3">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 mb-2 flex items-center gap-2">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" stroke="currentColor" stroke-width="2"/><polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/></svg>
                    Descripción / Notas
                </p>
                <p id="detail-desc" class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed"></p>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="flex flex-wrap gap-2 mx-5 mt-4 mb-5 pt-4 border-t border-gray-100 dark:border-gray-800" id="detail-actions"></div>
    </div>
</div>

<form id="action-form" method="POST" class="hidden">
    @csrf @method('PATCH')
    <input type="hidden" name="status" id="action-status">
</form>

<form id="delete-form" method="POST" class="hidden">
    @csrf @method('DELETE')
</form>

{{-- TOOLTIP (Ventana Inteligente Flotante) --}}
<div id="cal-event-tooltip" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden w-64 border border-gray-100 dark:border-gray-700 hidden">
    <div id="tooltip-header" class="px-3 py-2 border-b border-white/20">
        <div class="flex items-center gap-1.5">
            <span id="tooltip-icon" class="text-white text-[10px] leading-none"></span>
            <span id="tooltip-badge" class="text-[9px] font-bold text-white uppercase tracking-wider"></span>
        </div>
        <p id="tooltip-title" class="text-[13px] font-bold text-white mt-1 leading-tight truncate"></p>
    </div>
    <div class="p-3 bg-white dark:bg-gray-800 space-y-2">
        <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
            <span class="w-1.5 h-1.5 rounded-full" id="tooltip-status-dot"></span>
            <span id="tooltip-status" class="font-medium"></span>
            <span class="text-gray-300 dark:text-gray-600">|</span>
            <span id="tooltip-time" class="font-medium text-gray-500 truncate"></span>
        </div>
        <div id="tooltip-desc-wrap" class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed"></div>
        <div id="tooltip-participants" class="hidden flex-wrap gap-1 mt-1"></div>
    </div>
</div>

@endpush
{{-- ════  INLINE SCRIPT (no depende de CDN — usa window.ReunionesCalendar de Vite)  ════ --}}
@push('scripts')
<script>
// Datos del servidor
// Se reconstruye la URL actual basándose completamente en lo que ve el navegador para evitar recortes de subdirectorios en XAMPP.
const BASE_CURRENT_URL = window.location.origin + window.location.pathname.replace(/\/$/, '');
const URL_REUNIONES = BASE_CURRENT_URL.includes('/reuniones') ? BASE_CURRENT_URL : BASE_CURRENT_URL + '/reuniones';
const URL_TAREAS    = URL_REUNIONES.replace('/reuniones', '/tareas');

const ALL_MEETINGS = @json($allMeetings);
const ALL_TASKS    = @json($allTasks);
const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Color palettes
const R_COLORS = {
    programada: { bg:'#eff6ff', text:'#1d4ed8', border:'#3b82f6', dot:'#3b82f6' },
    completada:  { bg:'#f0fdf4', text:'#166534', border:'#22c55e', dot:'#22c55e' },
    cancelada:   { bg:'#f3f4f6', text:'#4b5563', border:'#9ca3af', dot:'#9ca3af' },
};
const T_COLORS = {
    completada:  { bg:'#f0fdf4', text:'#166534', border:'#22c55e', dot:'#22c55e' },
    en_progreso: { bg:'#eef2ff', text:'#3730a3', border:'#6366f1', dot:'#6366f1' },
    bloqueada:   { bg:'#fef2f2', text:'#991b1b', border:'#ef4444', dot:'#ef4444' },
    pendiente:   { bg:'#fffbeb', text:'#92400e', border:'#f59e0b', dot:'#f59e0b' },
};

// Custom event renderer – produces the ClickUp-style pill card
function renderEventContent(info) {
    const p    = info.event.extendedProps;
    const col  = p.kind === 'reunion'
        ? (R_COLORS[p.data.status] || R_COLORS.programada)
        : (T_COLORS[p.data.status] || T_COLORS.pendiente);

    const icon = p.kind === 'reunion' ? '📅' : '✅';
    const rawTitle = info.event.title;
    const timeStr  = info.event.start
        ? info.event.start.toLocaleTimeString('es', { hour:'2-digit', minute:'2-digit', hour12:true })
        : null;

    const wrapper = document.createElement('div');
    wrapper.className = 'cal-event-card';
    wrapper.style.cssText = `background:${col.bg};color:${col.text};border-left-color:${col.border};`;
    wrapper.innerHTML = `
        <span class="ev-icon">${icon}</span>
        <span class="ev-body">
            <span class="ev-title" title="${rawTitle}">${rawTitle}</span>
            ${timeStr ? `<span class="ev-time">${timeStr}</span>` : ''}
        </span>`;
    return { domNodes: [wrapper] };
}

// Construye el array de eventos aplicando filtros
function buildEvents(typeFilter, projectFilter, statusFilter) {
    const events = [];
    if (!typeFilter || typeFilter === 'reunion') {
        ALL_MEETINGS.forEach(m => {
            if (projectFilter && String(m.project_id) !== projectFilter) return;
            if (statusFilter  && m.status !== statusFilter) return;
            const mDate = m.meeting_date ? m.meeting_date.substring(0, 10) : '';
            events.push({
                id: 'm_' + m.id,
                title: m.title,
                start: mDate + (m.meeting_time ? 'T' + m.meeting_time : ''),
                backgroundColor: 'transparent',
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
            const tDate = t.due_date ? t.due_date.substring(0, 10) : '';
            events.push({
                id: 't_' + t.id,
                title: t.title,
                start: tDate,
                backgroundColor: 'transparent',
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
        let tooltipHideTimeout;
        const tooltip = document.getElementById('cal-event-tooltip');

        window.ReunionesCalendar.init(buildEvents('', '', ''), {
            eventContent: renderEventContent,
            onEventClick: function (info) {
                // hide tooltip immediately
                tooltip.classList.remove('show');
                setTimeout(() => tooltip.classList.add('hidden'), 150);
                
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
                const name    = info.event.title;
                if (!confirm('¿Mover "' + name + '" al ' + newDate + '?')) { info.revert(); return; }
                const url = p.kind === 'reunion'
                    ? URL_REUNIONES + '/' + p.data.id + '/actualizar-fecha'
                    : URL_TAREAS + '/' + p.data.id + '/actualizar-fecha';
                fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                    body: JSON.stringify({ new_date: newDate })
                })
                .then(r => r.json())
                .then(d => { if (!d.success) { alert(d.message || 'Error.'); info.revert(); } })
                .catch(() => { alert('Error de red.'); info.revert(); });
            },
            onEventMouseEnter: function (info) {
                clearTimeout(tooltipHideTimeout);
                const p = info.event.extendedProps;
                const bounding = info.el.getBoundingClientRect();
                
                // Color data
                const col = p.kind === 'reunion'
                    ? (R_COLORS[p.data.status] || R_COLORS.programada)
                    : (T_COLORS[p.data.status] || T_COLORS.pendiente);
                const icon = p.kind === 'reunion' ? '📅' : '✅';
                const label = p.kind === 'reunion' ? 'Reunión' : 'Tarea';
                    
                // Header styles
                const headerStyles = {
                    programada: 'linear-gradient(135deg,#3b82f6,#6366f1)',
                    completada: 'linear-gradient(135deg,#10b981,#059669)',
                    cancelada:  'linear-gradient(135deg,#9ca3af,#6b7280)',
                    en_progreso:'linear-gradient(135deg,#6366f1,#4f46e5)',
                    bloqueada:  'linear-gradient(135deg,#ef4444,#dc2626)',
                    pendiente:  'linear-gradient(135deg,#f59e0b,#d97706)',
                };
                const bgHeader = headerStyles[p.data.status] || headerStyles.programada;

                // Update tooltip DOM
                tooltip.querySelector('#tooltip-header').style.background = bgHeader;
                document.getElementById('tooltip-icon').textContent = icon;
                document.getElementById('tooltip-badge').textContent = label;
                document.getElementById('tooltip-title').textContent = info.event.title;
                document.getElementById('tooltip-status-dot').style.background = col.dot;
                document.getElementById('tooltip-status').textContent = p.data.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                
                let timeTxt = 'Todo el día';
                if (info.event.start && (info.event.start.getHours() > 0 || info.event.start.getMinutes() > 0)) {
                    timeTxt = info.event.start.toLocaleTimeString('es', {hour:'2-digit', minute:'2-digit', hour12:true});
                } else if (p.kind === 'reunion' && p.data.meeting_time) {
                    timeTxt = p.data.meeting_time.substring(0,5);
                }
                document.getElementById('tooltip-time').textContent = timeTxt;

                // Note
                const descEl = document.getElementById('tooltip-desc-wrap');
                if (p.data.description) {
                    descEl.textContent = p.data.description;
                    descEl.classList.remove('hidden');
                } else {
                    descEl.classList.add('hidden');
                }

                // Participants preview (for meetings)
                const partEl = document.getElementById('tooltip-participants');
                if (p.kind === 'reunion' && p.data.participants && p.data.participants.length > 0) {
                    partEl.innerHTML = p.data.participants.slice(0, 4).map(u => 
                        `<div class="w-5 h-5 rounded-full bg-black/10 flex items-center justify-center text-[8px] font-bold text-gray-700 dark:text-gray-300 ring-1 ring-gray-200 dark:ring-gray-700" title="${u.name}">${u.name.substring(0,2).toUpperCase()}</div>`
                    ).join('');
                    if (p.data.participants.length > 4) {
                        partEl.innerHTML += `<div class="text-[9px] text-gray-400 font-medium ml-1">+${p.data.participants.length - 4}</div>`;
                    }
                    partEl.classList.replace('hidden', 'flex');
                } else {
                    partEl.classList.replace('flex', 'hidden');
                }

                // Position the tooltip intelligently near the mouse/event, ensuring it doesn't go off-screen
                tooltip.classList.remove('hidden');
                
                // Read tooltip dimensions after un-hiding
                const tooltipRect = tooltip.getBoundingClientRect();
                
                // Calculate target positions
                let tipX = bounding.left + (bounding.width / 2) - (tooltipRect.width / 2);
                let tipY = bounding.top - tooltipRect.height - 10; // 10px spacing above the event card
                
                // If it goes above the viewport, place it below the event card instead
                if (tipY < 10) {
                    tipY = bounding.bottom + 10;
                }
                
                // Constrain horizontally
                if (tipX < 10) tipX = 10;
                if (tipX + tooltipRect.width > window.innerWidth - 10) tipX = window.innerWidth - tooltipRect.width - 10;
                
                tooltip.style.left = `${tipX}px`;
                tooltip.style.top = `${tipY}px`;
                
                // Animate in
                setTimeout(() => tooltip.classList.add('show'), 10);
            },
            onEventMouseLeave: function (info) {
                tooltip.classList.remove('show');
                tooltipHideTimeout = setTimeout(() => {
                    tooltip.classList.add('hidden');
                }, 150); // matches CSS transition out time
            }
        });
    });
});

// ── Filtros calendario ───────────────────────────────────────────────────────
function applyCalendarFilters() {
    if (!window.ReunionesCalendar) return;
    window.ReunionesCalendar.setEventsWithContent(buildEvents(
        document.getElementById('filter-type').value,
        document.getElementById('filter-project').value,
        document.getElementById('filter-status').value
    ), renderEventContent);
}
function resetFilters() {
    ['filter-type','filter-project','filter-status'].forEach(id => document.getElementById(id).value = '');
    applyCalendarFilters();
}

function filterAudit() {
    const mId = document.getElementById('audit-meeting').value;
    const uId = document.getElementById('audit-user').value;
    const rows = document.querySelectorAll('.audit-row');
    rows.forEach(r => {
        const matchM = !mId || r.dataset.meeting === mId;
        const matchU = !uId || r.dataset.user === uId;
        r.style.display = (matchM && matchU) ? 'block' : 'none';
    });
}

// ── Tab switch ───────────────────────────────────────────────────────────────
function switchTab(tab) {
    ['cal','hist','audit'].forEach(t => {
        const pane = document.getElementById('pane-' + t);
        if (pane) pane.classList.toggle('hidden', t !== tab);
        
        const btn = document.getElementById('tab-' + t);
        if (btn) {
            if (t === tab) {
                btn.classList.add('bg-white','dark:bg-gray-900','text-gray-900','dark:text-white','shadow-sm');
                btn.classList.remove('text-gray-500','dark:text-gray-400','hover:text-gray-700');
            } else {
                btn.classList.remove('bg-white','dark:bg-gray-900','text-gray-900','dark:text-white','shadow-sm');
                btn.classList.add('text-gray-500','dark:text-gray-400','hover:text-gray-700');
            }
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
    // Look up full meeting data to populate description & location
    const m = ALL_MEETINGS.find(x => x.id == id) || {};

    document.getElementById('edit-reunion-form').dataset.meetingId = id;
    document.getElementById('edit-title').value       = title;
    document.getElementById('edit-date').value        = (date || '').substring(0, 10);
    document.getElementById('edit-time').value        = time ? time.substring(0, 5) : '';
    document.getElementById('edit-project').value     = projectId || '';
    document.getElementById('edit-status').value      = status || 'programada';
    document.getElementById('edit-location').value    = m.location || '';
    document.getElementById('edit-description').value = m.description || '';
    if (document.getElementById('edit-reason')) document.getElementById('edit-reason').value = '';
    const errEl = document.getElementById('edit-error-msg');
    if (errEl) errEl.classList.add('hidden');
    document.getElementById('modal-detail').classList.add('hidden');
    document.getElementById('modal-edit-reunion').classList.remove('hidden');
}

function saveEditMeeting() {
    const form = document.getElementById('edit-reunion-form');
    const id   = form.dataset.meetingId;
    const btn  = document.getElementById('btn-save-edit');
    const errEl = document.getElementById('edit-error-msg');

    if (!id) return;

    // Disable button to prevent double submit
    btn.disabled = true;
    btn.textContent = 'Guardando...';

    const payload = {
        title:        document.getElementById('edit-title').value,
        meeting_date: document.getElementById('edit-date').value,
        meeting_time: document.getElementById('edit-time').value,
        project_id:   document.getElementById('edit-project').value || null,
        status:       document.getElementById('edit-status').value,
        location:     document.getElementById('edit-location').value || null,
        description:  document.getElementById('edit-description').value || null,
        reason:       document.getElementById('edit-reason') ? document.getElementById('edit-reason').value : '',
    };

    fetch(URL_REUNIONES + '/' + id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(r => {
        if (!r.ok) return r.json().then(e => Promise.reject(e));
        return r.json();
    })
    .then(() => {
        // Force hard reload to bypass BFCache and get fresh PHP data
        window.location.href = window.location.pathname + '?_t=' + Date.now();
    })
    .catch(err => {
        btn.disabled = false;
        btn.textContent = 'Guardar Cambios';
        const msg = err?.message || (err?.errors ? Object.values(err.errors).flat().join(' | ') : 'Error al guardar.');
        if (errEl) { errEl.textContent = '⚠ ' + msg; errEl.classList.remove('hidden'); }
        else alert(msg);
    });
}

// ── Detail modal helpers ──────────────────────────────────────────────────────
function closeDetailModal() {
    document.getElementById('modal-detail').classList.add('hidden');
}

function openDetailModal(id) {
    const m = ALL_MEETINGS.find(x => x.id == id);
    if (m) openDetailFromData(m);
}

function openDetailFromData(m) {
    // Header gradient per status
    const headerStyles = {
        programada: 'linear-gradient(135deg,#3b82f6,#6366f1)',
        completada:  'linear-gradient(135deg,#10b981,#059669)',
        cancelada:   'linear-gradient(135deg,#9ca3af,#6b7280)',
    };
    const statusColors = {
        programada: { dot:'#3b82f6', label:'Programada' },
        completada:  { dot:'#22c55e', label:'Completada' },
        cancelada:   { dot:'#9ca3af', label:'Cancelada'  },
    };
    const sc = statusColors[m.status] || statusColors.programada;

    // Header band
    document.getElementById('detail-header-band').style.background = headerStyles[m.status] || headerStyles.programada;
    document.getElementById('detail-type-badge').innerHTML = '📅 Reunión';
    document.getElementById('detail-title').textContent = m.title;

    // Status card
    document.getElementById('detail-status-dot').style.background = sc.dot;
    document.getElementById('detail-status-label').textContent = sc.label;

    // Date
    let dateStr = '';
    if (m.meeting_date) {
        const rawDate = (m.meeting_date || '').substring(0, 10); // strip MySQL timestamp if present
        const d = new Date(rawDate + 'T00:00:00');
        dateStr = d.toLocaleDateString('es', { weekday:'short', day:'numeric', month:'short', year:'numeric' });
    }
    if (m.meeting_time) dateStr += ' · ' + m.meeting_time.substring(0,5);
    document.getElementById('detail-date').textContent = dateStr || '—';

    // Location
    const locWrap = document.getElementById('detail-location-wrap');
    if (m.location) { document.getElementById('detail-location').textContent = m.location; locWrap.classList.replace('hidden','flex'); }
    else locWrap.classList.replace('flex','hidden');

    // Project
    const projWrap = document.getElementById('detail-project-wrap');
    const pName = m.project ? m.project.project_name : null;
    if (pName) { document.getElementById('detail-project').textContent = pName; projWrap.classList.replace('hidden','flex'); }
    else projWrap.classList.replace('flex','hidden');

    // Participants
    const partWrap = document.getElementById('detail-participants-wrap');
    const partEl   = document.getElementById('detail-participants');
    if (m.participants && m.participants.length) {
        const avatarColors = ['#3b82f6','#6366f1','#ec4899','#f59e0b','#10b981','#ef4444'];
        partEl.innerHTML = m.participants.map((p, i) => {
            const color = avatarColors[i % avatarColors.length];
            return `<span class="flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 rounded-full text-xs text-gray-700 dark:text-gray-200 shadow-sm border border-gray-100 dark:border-gray-600 font-medium">
                <span class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0" style="background:${color}">${p.name.substring(0,2).toUpperCase()}</span>
                ${p.name}</span>`;
        }).join('');
        partWrap.classList.remove('hidden');
    } else partWrap.classList.add('hidden');

    // Description
    const descWrap = document.getElementById('detail-desc-wrap');
    if (m.description) { document.getElementById('detail-desc').textContent = m.description; descWrap.classList.remove('hidden'); }
    else descWrap.classList.add('hidden');

    // Actions
    const actEl = document.getElementById('detail-actions');
    actEl.innerHTML = '';
    if (m.status === 'programada') {
        actEl.innerHTML += `<button onclick="submitStatusForm(${m.id},'completada')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold bg-emerald-500 text-white rounded-xl hover:bg-emerald-600 transition-all shadow-sm">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="20,6 9,17 4,12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Marcar Completada</button>`;
    } else {
        actEl.innerHTML += `<button onclick="submitStatusForm(${m.id},'programada')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-all shadow-sm">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="1,4 1,10 7,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Reabrir</button>`;
    }
    actEl.innerHTML += `<button onclick="openEditMeeting(${m.id},'${m.title.replace(/'/g,"\\'").replace(/"/g,'&quot;')}','${(m.meeting_date||'').substring(0,10)}','${m.meeting_time||''}','${m.project_id||''}','${m.status}')" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all shadow-sm">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/></svg>
        Editar</button>`;
    actEl.innerHTML += `<button onclick="submitDeleteForm(${m.id})" class="inline-flex items-center justify-center gap-2 px-3 py-2.5 text-sm font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-all border border-red-100">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2"/><path d="M19 6l-1 14H6L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Eliminar</button>`;

    // Re-trigger animation
    const modalInner = document.querySelector('#modal-detail .modal-animate');
    if (modalInner) { modalInner.classList.remove('modal-animate'); void modalInner.offsetWidth; modalInner.classList.add('modal-animate'); }

    document.getElementById('modal-detail').classList.remove('hidden');
}

function submitStatusForm(id, status) {
    let reason = '';
    if (status === 'cancelada' || status === 'completada') {
        reason = prompt('Opcional: Indique un motivo o resumen del cambio (para trazabilidad):');
        if (reason === null) return; // User cancelled the prompt
    }
    
    fetch(URL_REUNIONES + '/' + id + '/estado', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
        body: JSON.stringify({ status: status, reason: reason })
    }).then(() => window.location.reload());
}

function submitDeleteForm(id) {
    if (!confirm('¿Está seguro de que desea eliminar esta reunión permanentemente?')) return;
    const f = document.getElementById('delete-form');
    f.action = URL_REUNIONES + '/' + id;
    f.submit();
}

function openTaskDetail(t) {
    const taskColors = {
        completada:  { grad:'linear-gradient(135deg,#10b981,#059669)', dot:'#22c55e', label:'Completada' },
        en_progreso: { grad:'linear-gradient(135deg,#6366f1,#4f46e5)', dot:'#6366f1', label:'En progreso' },
        bloqueada:   { grad:'linear-gradient(135deg,#ef4444,#dc2626)', dot:'#ef4444', label:'Bloqueada'   },
        pendiente:   { grad:'linear-gradient(135deg,#f59e0b,#d97706)', dot:'#f59e0b', label:'Pendiente'   },
    };
    const tc = taskColors[t.status] || taskColors.pendiente;

    document.getElementById('detail-header-band').style.background = tc.grad;
    document.getElementById('detail-type-badge').innerHTML = '✅ Tarea';
    document.getElementById('detail-title').textContent = t.title;
    document.getElementById('detail-status-dot').style.background = tc.dot;
    document.getElementById('detail-status-label').textContent = tc.label;
    document.getElementById('detail-date').textContent = 'Vence: ' + ((t.due_date||'').substring(0,10)||'—');
    document.getElementById('detail-location-wrap').classList.replace('flex','hidden');

    const projWrap = document.getElementById('detail-project-wrap');
    if (t.project) { document.getElementById('detail-project').textContent = t.project.project_name; projWrap.classList.replace('hidden','flex'); }
    else projWrap.classList.replace('flex','hidden');

    document.getElementById('detail-participants-wrap').classList.add('hidden');

    const descWrap = document.getElementById('detail-desc-wrap');
    if (t.description) { document.getElementById('detail-desc').textContent = t.description; descWrap.classList.remove('hidden'); }
    else descWrap.classList.add('hidden');

    // No actions for tasks (read-only view)
    document.getElementById('detail-actions').innerHTML =
        `<button onclick="closeDetailModal()" class="w-full px-4 py-2.5 text-sm font-semibold text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-800">Cerrar</button>`;

    const modalInner = document.querySelector('#modal-detail .modal-animate');
    if (modalInner) { modalInner.classList.remove('modal-animate'); void modalInner.offsetWidth; modalInner.classList.add('modal-animate'); }

    document.getElementById('modal-detail').classList.remove('hidden');
}
</script>

<script>
// ══ Participant Picker ════════════════════════════════════════════════════════
// Shared state: pickerSelected[pickerId] = { id: number, name: string, color: string }[]
const pickerSelected = {};
const pickerColors   = [
    '#3b82f6','#6366f1','#ec4899','#f59e0b','#10b981',
    '#ef4444','#8b5cf6','#14b8a6','#f97316','#06b6d4'
];

function togglePickerDropdown(pid) {
    const dd = document.getElementById('picker-' + pid + '-dropdown');
    if (!dd) return;
    const isOpen = !dd.classList.contains('hidden');
    // close all other dropdowns first
    document.querySelectorAll('.participant-picker [id$="-dropdown"]').forEach(d => d.classList.add('hidden'));
    if (!isOpen) {
        dd.classList.remove('hidden');
        const inp = dd.querySelector('input[type="text"]');
        if (inp) { inp.value = ''; filterPicker(pid, ''); setTimeout(() => inp.focus(), 50); }
    }
}

function togglePickerItem(pid, userId, userName) {
    if (!pickerSelected[pid]) pickerSelected[pid] = [];
    const idx   = pickerSelected[pid].findIndex(x => x.id === userId);
    const color = pickerColors[userId % pickerColors.length];

    if (idx === -1) {
        pickerSelected[pid].push({ id: userId, name: userName, color });
    } else {
        pickerSelected[pid].splice(idx, 1);
    }
    renderPickerChips(pid);
    updatePickerCheck(pid, userId, idx === -1);
}

function removePickerItem(pid, userId, ev) {
    ev.stopPropagation();
    if (!pickerSelected[pid]) return;
    pickerSelected[pid] = pickerSelected[pid].filter(x => x.id !== userId);
    renderPickerChips(pid);
    updatePickerCheck(pid, userId, false);
}

function updatePickerCheck(pid, userId, selected) {
    const checkEl = document.getElementById('picker-' + pid + '-check-' + userId);
    const itemEl  = document.getElementById('picker-' + pid + '-item-' + userId);
    if (checkEl) checkEl.classList.toggle('hidden', !selected);
    if (itemEl)  itemEl.classList.toggle('bg-brand-50', selected);
    if (itemEl)  itemEl.classList.toggle('dark:bg-gray-800/80', selected);
}

function renderPickerChips(pid) {
    const chipsEl      = document.getElementById('picker-' + pid + '-chips');
    const inputsEl     = document.getElementById('picker-' + pid + '-inputs');
    const placeholder  = document.getElementById('picker-' + pid + '-placeholder');
    if (!chipsEl || !inputsEl) return;

    const selected = pickerSelected[pid] || [];

    // Update hidden inputs
    inputsEl.innerHTML = selected.map(u =>
        `<input type="hidden" name="participants[]" value="${u.id}">`
    ).join('');

    // Update chips area
    const chips = selected.map(u => `
        <span class="picker-chip" style="background:${u.color}">
            <span class="w-4 h-4 rounded-full bg-black/20 flex-shrink-0 flex items-center justify-center text-[9px] font-bold">
                ${u.name.substring(0,2).toUpperCase()}
            </span>
            <span class="truncate max-w-[90px]">${u.name}</span>
            <button type="button" class="picker-chip-remove" onclick="removePickerItem('${pid}', ${u.id}, event)">✕</button>
        </span>`
    ).join('');

    // Remove old chips (keep placeholder)
    chipsEl.querySelectorAll('.picker-chip').forEach(c => c.remove());

    if (chips) {
        chipsEl.insertAdjacentHTML('afterbegin', chips);
        if (placeholder) placeholder.classList.add('hidden');
    } else {
        if (placeholder) placeholder.classList.remove('hidden');
    }
}

function filterPicker(pid, query) {
    const q    = query.toLowerCase().trim();
    const list = document.getElementById('picker-' + pid + '-list');
    if (!list) return;
    list.querySelectorAll('[id^="picker-' + pid + '-item-"]').forEach(item => {
        const name = item.querySelector('span:nth-child(2)')?.textContent?.toLowerCase() || '';
        item.style.display = (!q || name.includes(q)) ? '' : 'none';
    });
}

function resetPicker(pid) {
    pickerSelected[pid] = [];
    // Uncheck all
    document.querySelectorAll(`[id^="picker-${pid}-check-"]`).forEach(el => el.classList.add('hidden'));
    document.querySelectorAll(`[id^="picker-${pid}-item-"]`).forEach(el => {
        el.classList.remove('bg-brand-50', 'dark:bg-gray-800/80');
    });
    renderPickerChips(pid);
    const dd = document.getElementById('picker-' + pid + '-dropdown');
    if (dd) dd.classList.add('hidden');
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('.participant-picker')) {
        document.querySelectorAll('[id$="-dropdown"].participant-picker [id$="-dropdown"]').forEach(d => d.classList.add('hidden'));
        // More robust selector:
        document.querySelectorAll('.participant-picker [id$="-dropdown"]').forEach(d => d.classList.add('hidden'));
    }
});

// Reset picker when new meeting modal closes
document.addEventListener('DOMContentLoaded', function() {
    const closeBtn = document.querySelector('#modal-nueva-reunion button[onclick*="hidden"]');
    if (closeBtn) {
        const orig = closeBtn.getAttribute('onclick');
        closeBtn.setAttribute('onclick', orig + '; resetPicker("new");');
    }
});
</script>
@endpush
@endsection
