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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pendientes por Ingeniero</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitoreo de carga de trabajo, tareas, reuniones y bloqueos activos</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('tareas') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            Ver Kanban de Tareas
        </a>
        <button onclick="document.getElementById('modal-nuevo-pendiente').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Bloqueo rápido
        </button>
    </div>
</div>

{{-- Engineer Cards Dashboard --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
    @php
        $colorsUser = ['bg-brand-500','bg-purple-500','bg-success-500','bg-warning-500','bg-blue-light-500','bg-orange-500'];
    @endphp

    @forelse($engineers as $eng)
    @php
        $userColor = $colorsUser[crc32($eng->name) % count($colorsUser)];
    @endphp
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 group hover:shadow-theme-md transition-shadow">
        {{-- Header --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full {{ $userColor }} flex items-center justify-center flex-shrink-0 shadow-sm">
                        <span class="text-white text-sm font-bold">{{ substr($eng->name, 0, 2) }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $eng->name }}</h3>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <div class="w-2 h-2 rounded-full {{ $eng->is_active ? 'bg-success-500' : 'bg-gray-400' }}"></div>
                            <span class="text-xs text-gray-500">{{ $eng->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mini Stats --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="text-center p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                    <p class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ $eng->proyectos_count ?? 0 }}</p>
                    <p class="text-xs text-gray-500">Proyectos</p>
                </div>
                <div class="text-center p-2.5 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10">
                    <p class="text-lg font-bold text-blue-light-600 dark:text-blue-light-400">{{ $eng->tareas_count ?? 0 }}</p>
                    <p class="text-xs text-blue-light-600 dark:text-blue-light-400">Tareas activas</p>
                </div>
                <div class="text-center p-2.5 rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ $eng->reuniones_count ?? 0 }}</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400">Reuniones</p>
                </div>
            </div>
        </div>

        {{-- Top Pending Actions (Tasks & Meetings) --}}
        <div class="p-5 space-y-2.5">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Acciones inmediatas</h4>
            
            @forelse($eng->meetings as $meeting)
            <div class="flex items-start gap-3 p-3 rounded-xl bg-brand-50 border border-brand-100 dark:bg-brand-500/10 dark:border-brand-500/20">
                <div class="flex-shrink-0 mt-0.5"><svg class="text-brand-500" width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="1.5"/><line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="1.5"/><line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="1.5"/></svg></div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-brand-800 dark:text-brand-300">{{ $meeting->title }}</p>
                    <p class="text-[11px] text-brand-600 dark:text-brand-400 mt-0.5">{{ \Carbon\Carbon::parse($meeting->meeting_date)->format('d/m') }} - {{ \Carbon\Carbon::parse($meeting->meeting_time)->format('H:i') }}</p>
                </div>
                <span class="flex-shrink-0 px-1.5 py-0.5 text-[10px] uppercase font-bold text-brand-700 bg-brand-100 rounded">REUNIÓN</span>
            </div>
            @empty
            @endforelse

            @forelse($eng->assignedTasks as $task)
            @php
                $isBlocked = $task->status === 'bloqueada';
                $isHighPrio = in_array($task->priority, ['alta', 'critica']);
                $bgClass = $isBlocked ? 'bg-error-50 border-error-100 dark:bg-error-500/10 dark:border-error-500/20' : ($isHighPrio ? 'bg-orange-50 border-orange-100 dark:bg-orange-500/10 dark:border-orange-500/20' : 'bg-gray-50 border-gray-100 dark:bg-gray-800/50 dark:border-gray-700');
                $textClass = $isBlocked ? 'text-error-800 dark:text-error-300' : ($isHighPrio ? 'text-orange-800 dark:text-orange-300' : 'text-gray-700 dark:text-gray-300');
            @endphp
            <div class="flex flex-col gap-1 p-3 rounded-xl border {{ $bgClass }}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 mt-0.5"><svg class="{{ $isBlocked ? 'text-error-500' : ($isHighPrio ? 'text-orange-500' : 'text-gray-400') }}" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold {{ $textClass }} line-clamp-1">{{ $task->title }}</p>
                        <p class="text-[11px] opacity-75 mt-0.5 {{ $textClass }}">{{ $task->project->project_name ?? 'Tarea suelta' }} — {{ $task->progress }}%</p>
                    </div>
                    <span class="flex-shrink-0 px-1.5 py-0.5 text-[10px] uppercase font-bold rounded {{ $isBlocked ? 'text-error-700 bg-error-200 dark:bg-error-500/30' : ($isHighPrio ? 'text-orange-700 bg-orange-200 dark:bg-orange-500/30' : 'text-gray-500 bg-gray-200 dark:bg-gray-700') }}">
                        TAREA
                    </span>
                </div>
            </div>
            @empty
            @endforelse

            @if($eng->assignedTasks->isEmpty() && $eng->meetings->isEmpty())
                <p class="text-xs text-center text-gray-400 py-4 border border-dashed border-gray-200 rounded-xl dark:border-gray-700">No hay acciones urgentes abiertas.</p>
            @endif
        </div>
        <div class="px-5 pb-4">
            <a href="{{ route('tareas', ['engineer_id' => $eng->id]) }}" class="block w-full py-2.5 text-xs font-medium text-center text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                Ver todas sus tareas
            </a>
        </div>
    </div>
    @empty
        <div class="col-span-full py-12 text-center text-gray-500">No se encontraron ingenieros en el sistema.</div>
    @endforelse
</div>


{{-- System Blockers for Engineers List (Pendientes global type 'ingeniero') --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">Bloqueos Registrados (Sistema de Pendientes)</h2>
            <p class="text-xs text-gray-500 mt-1">Situaciones generales o requerimientos internos que bloquean el trabajo técnico.</p>
        </div>
        {{-- Filters global --}}
        <form method="GET" action="{{ route('pendientes.ingeniero') }}" class="flex items-center gap-2">
            <select name="status" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
                <option value="">Todo estado</option>
                <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
            </select>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Ingeniero</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Proyecto / Cliente</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Descripción Bloqueo</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Reportado</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($pending as $p)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            @php
                                $cName = $p->assignedUser->name ?? 'SC';
                                $cColor = $colorsUser[crc32($cName) % count($colorsUser)];
                            @endphp
                            <div class="w-7 h-7 rounded-full {{ $cColor }} flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ substr($cName, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $p->assignedUser->name ?? 'Sin Asignar' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $p->project->project_name ?? '—' }}</p>
                        <p class="text-[11px] text-gray-400">{{ $p->project->company->name ?? '—' }}</p>
                    </td>
                    <td class="px-5 py-4 max-w-[250px]">
                        <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">{{ $p->description }}</p>
                        @if($p->resolution_note)
                            <div class="mt-2 p-2 bg-brand-50 dark:bg-brand-500/10 rounded border border-brand-100 dark:border-brand-500/20">
                                <span class="text-[10px] font-bold text-brand-700 dark:text-brand-400 uppercase tracking-wider block mb-0.5">Nota de solución:</span>
                                <p class="text-xs text-brand-800 dark:text-brand-300">{{ $p->resolution_note }}</p>
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $estClass = match($p->status) {
                                'pendiente' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                                'completado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $estClass }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-400">{{ $p->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-4 text-right flex justify-end gap-2">
                        @if($p->status === 'pendiente')
                            <button type="button" onclick="openResolveModal('{{ $p->id }}')" class="px-2.5 py-1.5 text-xs font-medium bg-success-50 text-success-600 rounded hover:bg-success-100 transition-colors dark:bg-success-500/10 dark:text-success-400 dark:hover:bg-success-500/20">
                                Resolver
                            </button>
                            <form method="POST" action="{{ route('pendientes.destroy', $p) }}" onsubmit="return confirm('¿Eliminar bloqueante?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-error-500 rounded transition-colors" title="Eliminar">✕</button>
                            </form>
                        @else
                            <span class="text-xs text-gray-400 flex items-center px-2 py-1">✓ Listo</span>
                            <form method="POST" action="{{ route('pendientes.destroy', $p) }}" onsubmit="return confirm('¿Eliminar registro completado?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-gray-400 hover:text-error-500 rounded transition-colors" title="Limpiar">✕</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400 text-sm">
                        No hay bloqueos/pendientes técnicos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pending->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $pending->links() }}
    </div>
    @endif
</div>

@push('modals')
{{-- MODAL: Resolver Pendiente --}}
<div id="modal-resolver-pendiente" class="hidden fixed inset-0 z-[999999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeResolveModal()"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Opción de Resolución</h3>
            <button type="button" onclick="closeResolveModal()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form id="form-resolver" method="POST" action="">
            @csrf @method('PATCH')
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nota de solución (Obligatorio) *</label>
                <textarea name="resolution_note" required rows="3" placeholder="Detalla cómo se resolvió este pendiente..." class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-success-500/20 resize-none"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="closeResolveModal()" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-success-500 text-white rounded-lg hover:bg-success-600 transition-colors">Marcar Resuelto</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Nuevo Pendiente/Bloqueo (Ingeniero) --}}
<div id="modal-nuevo-pendiente" class="hidden fixed inset-0 z-[999999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-error-600 dark:text-error-400 flex items-center gap-2">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Registrar Bloqueo Técnico
            </h3>
            <button onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('pendientes.store') }}">
            @csrf
            <input type="hidden" name="type" value="ingeniero">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proyecto Afectado *</label>
                    <select name="project_id" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Seleccionar proyecto...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ingeniero Bloqueado *</label>
                    <select name="assigned_to" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Seleccionar responsable...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Motivo del bloqueo / Faltante *</label>
                    <textarea name="description" required rows="3" placeholder="Ej: Falta acceso a servidor, entorno de desarrollo no disponible..." class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-error-500/20 border-error-200 dark:border-error-500/30 resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <button type="button" onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-error-600 text-white rounded-lg hover:bg-error-700 transition-colors">Registrar Bloqueo</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endsection

@push('scripts')
<script>
    function openResolveModal(id) {
        document.getElementById('form-resolver').action = "/pendientes/" + id + "/resolver";
        document.getElementById('modal-resolver-pendiente').classList.remove('hidden');
    }
    function closeResolveModal() {
        document.getElementById('modal-resolver-pendiente').classList.add('hidden');
    }
</script>
@endpush
