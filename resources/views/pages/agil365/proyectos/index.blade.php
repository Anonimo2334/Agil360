@extends('layouts.app')

@section('content')
{{-- Flash Messages --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400 flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif
@if(session('error'))
<div id="flash-msg-err" class="mb-4 p-4 bg-error-50 border border-error-200 text-error-700 rounded-xl text-sm dark:bg-error-500/10 dark:border-error-500/20 dark:text-error-400 flex items-center justify-between">
    <span>✕ {{ session('error') }}</span>
    <button onclick="document.getElementById('flash-msg-err').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif

{{-- Header --}}
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Proyectos</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Administra todos los proyectos y cuentas activas</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('proyectos.export') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Exportar CSV
        </a>
        <button type="button" onclick="document.getElementById('modal-importar-csv').classList.remove('hidden')" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Importar CSV
        </button>
        <a href="{{ route('proyectos.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors shadow-sm">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            Nuevo Proyecto
        </a>
    </div>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('proyectos') }}" id="filter-form">
<div class="mb-5 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[200px] max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar proyecto o cliente..." class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20" onchange="this.form.submit()">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <select name="status" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todos los estados</option>
        <option value="iniciado" {{ request('status') === 'iniciado' ? 'selected' : '' }}>Iniciado</option>
        <option value="en_proceso" {{ request('status') === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
        <option value="soporte" {{ request('status') === 'soporte' ? 'selected' : '' }}>Soporte</option>
        <option value="completado" {{ request('status') === 'completado' ? 'selected' : '' }}>Completado</option>
        <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
    </select>
    <select name="engineer_id" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todos los ingenieros</option>
        @foreach($engineers as $eng)
            <option value="{{ $eng->id }}" {{ request('engineer_id') == $eng->id ? 'selected' : '' }}>{{ $eng->name }}</option>
        @endforeach
    </select>
    <select name="company_id" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todos los clientes</option>
        @foreach($companies as $company)
            <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
        @endforeach
    </select>
    @if(request()->anyFilled(['search','status','engineer_id','company_id']))
        <a href="{{ route('proyectos') }}" class="px-3 py-2 text-xs text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800">Limpiar filtros</a>
    @endif
    <div class="flex items-center gap-1 ml-auto">
        <button type="button" class="p-2.5 rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 text-gray-500 hover:bg-gray-50 transition-colors" title="Vista tabla" id="btn-table-view">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3 10h18M3 6h18M3 14h18M3 18h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </button>
        <button type="button" class="p-2.5 rounded-lg border border-gray-200 bg-white dark:bg-gray-900 dark:border-gray-700 text-gray-500 hover:bg-gray-50 transition-colors" title="Vista tarjetas" id="btn-card-view">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="3" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="14" width="7" height="7" rx="1" stroke="currentColor" stroke-width="1.5"/></svg>
        </button>
    </div>
</div>
</form>

{{-- TABLE VIEW --}}
<div id="table-view" class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/60 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cuenta / Proyecto</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Avance</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">CEO</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ing. Principal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inicio</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Límite</th>
                    <th class="px-5 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($projects as $project)
                @php
                    $avance = $project->progress_percentage ?? 0;
                    $progressColor = $avance >= 80 ? 'bg-success-500' : ($avance >= 50 ? 'bg-brand-500' : 'bg-error-500');
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
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-mono text-gray-400">#{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}</span>
                            @if($project->is_at_risk)
                                <svg class="text-error-500 animate-pulse flex-shrink-0" width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $project->project_name }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $project->company->name ?? '—' }}</p>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                                <div class="{{ $progressColor }} h-2 rounded-full transition-all duration-500" style="width: {{ $avance }}%"></div>
                            </div>
                            <span class="text-xs font-bold {{ $avance >= 80 ? 'text-success-600' : ($avance >= 50 ? 'text-brand-500' : 'text-error-500') }}">{{ $avance }}%</span>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full {{ $badgeClass }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-600 dark:text-gray-400">{{ $project->ceo ?? '—' }}</td>
                    <td class="px-5 py-4">
                        @if($project->primaryEngineer)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center">
                                <span class="text-white text-xs">{{ substr($project->primaryEngineer->name, 0, 1) }}</span>
                            </div>
                            <span class="text-xs text-gray-600 dark:text-gray-400 hidden lg:block">{{ explode(' ', $project->primaryEngineer->name)[0] }}</span>
                        </div>
                        @else <span class="text-xs text-gray-400">—</span> @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '—' }}</td>
                    <td class="px-5 py-4 text-xs {{ $project->is_overdue ? 'text-error-500 font-semibold' : 'text-gray-500 dark:text-gray-400' }}">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : '—' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('proyectos.show', $project) }}" class="p-1.5 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors" title="Ver detalle">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/></svg>
                            </a>
                            <a href="{{ route('proyectos.edit', $project) }}" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" title="Editar">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="2"/></svg>
                            </a>
                            <form method="POST" action="{{ route('proyectos.destroy', $project) }}" onsubmit="return confirm('¿Eliminar proyecto {{ addslashes($project->project_name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition-colors" title="Eliminar">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-5 py-12 text-center text-sm text-gray-400">
                        No se encontraron proyectos. <a href="{{ route('proyectos.create') }}" class="text-brand-500 hover:underline">Crear nuevo proyecto</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{-- Pagination --}}
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">Mostrando {{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }} de {{ $projects->total() }} proyectos</p>
        <div class="flex items-center gap-1">{{ $projects->links() }}</div>
    </div>
</div>

{{-- CARD VIEW (hidden by default) --}}
<div id="card-view" class="hidden grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
    @foreach($projects as $project)
    @php
        $avance = $project->progress_percentage ?? 0;
        $progressColor = $avance >= 80 ? 'bg-success-500' : ($avance >= 50 ? 'bg-brand-500' : 'bg-error-500');
        $statusLabels = ['iniciado'=>'Iniciado','en_proceso'=>'En proceso','soporte'=>'Soporte','completado'=>'Completado','cancelado'=>'Cancelado'];
        $statusLabel = $statusLabels[$project->status] ?? $project->status;
        $badgeClass = match($project->status) {
            'completado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
            'en_proceso' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/10 dark:text-blue-light-400',
            'soporte'    => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
            default      => 'bg-gray-100 text-gray-600',
        };
    @endphp
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 hover:shadow-theme-md transition-all group">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-mono text-gray-400 mb-1">#{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}</p>
                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $project->project_name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $project->company->name ?? '—' }}</p>
            </div>
            <span class="ml-2 flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full {{ $badgeClass }}">{{ $statusLabel }}</span>
        </div>
        <div class="mb-4">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs text-gray-500">Avance</span>
                <span class="text-xs font-bold {{ $avance >= 80 ? 'text-success-600' : ($avance >= 50 ? 'text-brand-500' : 'text-error-500') }}">{{ $avance }}%</span>
            </div>
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                <div class="{{ $progressColor }} h-2 rounded-full" style="width: {{ $avance }}%"></div>
            </div>
        </div>
        <div class="flex items-center justify-between mb-4">
            @if($project->primaryEngineer)
            <div class="flex items-center gap-1.5">
                <div class="w-6 h-6 rounded-full bg-brand-500 flex items-center justify-center">
                    <span class="text-white text-xs">{{ substr($project->primaryEngineer->name, 0, 1) }}</span>
                </div>
                <span class="text-xs text-gray-500">{{ explode(' ', $project->primaryEngineer->name)[0] }}</span>
            </div>
            @else <span class="text-xs text-gray-400">Sin ingeniero</span> @endif
            <span class="text-xs text-gray-400">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : '—' }}</span>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2">
            <a href="{{ route('proyectos.show', $project) }}" class="flex-1 text-center py-1.5 text-xs font-medium text-brand-500 border border-brand-200 rounded-lg hover:bg-brand-50 transition-colors dark:border-brand-500/20 dark:hover:bg-brand-500/10">Ver detalle</a>
            <a href="{{ route('proyectos.edit', $project) }}" class="flex-1 text-center py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Editar</a>
        </div>
    </div>
    @endforeach
</div>

@push('scripts')
<script>
    const tableView = document.getElementById('table-view');
    const cardView = document.getElementById('card-view');
    const btnTable = document.getElementById('btn-table-view');
    const btnCard = document.getElementById('btn-card-view');

    btnTable.addEventListener('click', () => {
        tableView.classList.remove('hidden');
        cardView.classList.add('hidden');
        btnTable.classList.add('bg-brand-50', 'text-brand-500', 'border-brand-200');
        btnCard.classList.remove('bg-brand-50', 'text-brand-500', 'border-brand-200');
        localStorage.setItem('proyectos_view', 'table');
    });
    btnCard.addEventListener('click', () => {
        cardView.classList.remove('hidden');
        tableView.classList.add('hidden');
        btnCard.classList.add('bg-brand-50', 'text-brand-500', 'border-brand-200');
        btnTable.classList.remove('bg-brand-50', 'text-brand-500', 'border-brand-200');
        localStorage.setItem('proyectos_view', 'card');
    });
    // Restore last view
    if (localStorage.getItem('proyectos_view') === 'card') btnCard.click();
    else btnTable.click();
</script>
@endpush
@push('modals')
{{-- MODAL: Importar CSV --}}
<div id="modal-importar-csv" class="hidden fixed inset-0 z-[999999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-importar-csv').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Importar Proyectos (CSV)</h3>
            <button onclick="document.getElementById('modal-importar-csv').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('proyectos.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <p class="text-xs text-gray-500 mb-3">Sube un archivo .csv separado por comas válido. El formato esperado (7 columnas) es:</p>
                <code class="block bg-gray-50 p-2 rounded text-[10px] text-gray-600 mb-4 border border-gray-100 overflow-x-auto whitespace-nowrap dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700">ID_Cliente, Nombre_Proyecto, CEO, Fecha_Inicio, Fecha_Fin, Avance, Estado</code>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Archivo CSV</label>
                <input type="file" name="csv_file" accept=".csv" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 dark:file:bg-brand-500/10 dark:file:text-brand-400">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-importar-csv').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">Importar</button>
            </div>
        </form>
    </div>
</div>
@endpush

@endsection
