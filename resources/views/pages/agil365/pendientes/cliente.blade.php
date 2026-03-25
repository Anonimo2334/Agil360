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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pendientes por Cliente</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestión de información faltante y acciones requeridas de clientes</p>
    </div>
    <button onclick="document.getElementById('modal-nuevo-pendiente').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Agregar pendiente
    </button>
</div>

{{-- Summary Cards (calculadas sobre totales de la DB) --}}
@php
    $totalEspera = \App\Models\PendingItem::where('type','cliente')->where('status','pendiente')->count();
    $totalesHoy =  \App\Models\PendingItem::where('type','cliente')->where('status','completado')->whereDate('updated_at', today())->count();
    $totalAntiguos = \App\Models\PendingItem::where('type','cliente')->where('status','pendiente')->where('created_at', '<', now()->subDays(3))->count();
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="rounded-2xl border border-error-200 bg-error-50 p-4 dark:border-error-500/20 dark:bg-error-500/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-error-100 dark:bg-error-500/20 flex items-center justify-center -mt-2">
                <svg class="text-error-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-error-700 dark:text-error-400">{{ $totalAntiguos }}</p>
                <p class="text-xs text-error-600 dark:text-error-500">Demorados (+3 días)</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-warning-200 bg-warning-50 p-4 dark:border-warning-500/20 dark:bg-warning-500/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-warning-100 dark:bg-warning-500/20 flex items-center justify-center -mt-2">
                <svg class="text-warning-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-warning-700 dark:text-warning-400">{{ $totalEspera }}</p>
                <p class="text-xs text-warning-600 dark:text-warning-500">En espera de cliente</p>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-success-200 bg-success-50 p-4 dark:border-success-500/20 dark:bg-success-500/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-success-100 dark:bg-success-500/20 flex items-center justify-center -mt-2">
                <svg class="text-success-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div>
                <p class="text-2xl font-bold text-success-700 dark:text-success-400">{{ $totalesHoy }}</p>
                <p class="text-xs text-success-600 dark:text-success-500">Resueltos hoy</p>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('pendientes.cliente') }}" class="mb-4 flex flex-wrap items-center gap-3" id="filter-form">
    <div class="relative">
        <input type="text" id="realtime-client-search" placeholder="Buscar cliente en tiempo real..." class="pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <select name="status" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todo estado</option>
        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
        <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
    </select>
    @if(request()->anyFilled(['status']))
        <a href="{{ route('pendientes.cliente') }}" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Limpiar</a>
    @endif
</form>

{{-- Pending Items Table --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Cliente</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Proyecto</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Descripción</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800" id="pendientes-tbody">
                @forelse($pending as $p)
                @php
                    $colors = ['bg-brand-500','bg-purple-500','bg-warning-500','bg-error-500','bg-blue-light-500','bg-success-500'];
                    $cName = $p->project->company->name ?? 'X';
                    $cColor = $colors[crc32($cName) % count($colors)];
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors pending-row" data-client="{{ strtolower($cName) }}">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg {{ $cColor }} flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ substr($cName, 0, 1) }}</span>
                            </div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $cName }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $p->project->project_name ?? '—' }}</td>
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
                                'pendiente' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                'completado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $estClass }}">{{ ucfirst($p->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-400">{{ $p->created_at->diffForHumans() }}</td>
                    <td class="px-5 py-4 text-right">
                        @if($p->status === 'pendiente')
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="openResolveModal('{{ $p->id }}')" class="px-2.5 py-1 text-xs font-medium bg-success-50 text-success-600 rounded hover:bg-success-100 transition-colors dark:bg-success-500/10 dark:text-success-400 dark:hover:bg-success-500/20">
                                    Resolver
                                </button>
                                <form method="POST" action="{{ route('pendientes.destroy', $p) }}" onsubmit="return confirm('¿Eliminar este pendiente?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-gray-400 hover:text-error-500 rounded transition-colors" title="Eliminar pendiente">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="flex justify-end items-center gap-2">
                                <span class="text-xs text-gray-400">✓ Listo</span>
                                <form method="POST" action="{{ route('pendientes.destroy', $p) }}" onsubmit="return confirm('¿Eliminar este pendiente de la historia?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1 text-gray-400 hover:text-error-500 rounded transition-colors" title="Eliminar de historial">
                                        ✕
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                        No hay pendientes de cliente.
                        <button onclick="document.getElementById('modal-nuevo-pendiente').classList.remove('hidden')" class="ml-1 text-brand-500 hover:underline">Crear el primero</button>
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

{{-- MODAL: Nuevo Pendiente Cliente --}}
<div id="modal-nuevo-pendiente" class="hidden fixed inset-0 z-[999999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo Pendiente de Cliente</h3>
            <button onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('pendientes.store') }}">
            @csrf
            <input type="hidden" name="type" value="cliente">
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Proyecto *</label>
                    <select name="project_id" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Seleccionar proyecto...</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }} ({{ $project->company->name ?? 'Sin cliente' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Descripción del requerimiento *</label>
                    <textarea name="description" required rows="3" placeholder="Ej: Faltan credenciales del servidor..." class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-nuevo-pendiente').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Crear Pendiente</button>
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
    
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('realtime-client-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const val = this.value.toLowerCase();
                document.querySelectorAll('.pending-row').forEach(row => {
                    const clientName = row.getAttribute('data-client');
                    if (clientName && clientName.includes(val)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
