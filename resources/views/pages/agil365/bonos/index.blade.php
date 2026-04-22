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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bonos por Cumplimiento</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sistema automático de bonos para ingenieros por proyectos completados a tiempo</p>
    </div>
    @if(auth()->user()->isAdmin() || auth()->user()->hasAnyRole(['contabilidad']))
    <button onclick="document.getElementById('modal-nuevo-bono').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Registrar Bono Manual
    </button>
    @endif
</div>

{{-- Summary --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="w-10 h-10 rounded-xl bg-success-50 flex items-center justify-center mb-3 dark:bg-success-500/10">
            <svg class="text-success-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalMes, 2) }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total bonos este mes</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="w-10 h-10 rounded-xl bg-brand-50 flex items-center justify-center mb-3 dark:bg-brand-500/10">
            <svg class="text-brand-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $bonosGenerados }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bonos generados</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="w-10 h-10 rounded-xl bg-warning-50 flex items-center justify-center mb-3 dark:bg-warning-500/10">
            <svg class="text-warning-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M12 8v4M12 16h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($bonosPendientes, 2) }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Bonos pendientes</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="w-10 h-10 rounded-xl bg-blue-light-50 flex items-center justify-center mb-3 dark:bg-blue-light-500/10">
            <svg class="text-blue-light-500" width="20" height="20" fill="none" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($bonosPagados, 2) }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pagados (total)</p>
    </div>
</div>

{{-- Engineer Bonus Leaderboard --}}
<div class="grid grid-cols-12 gap-5">
    {{-- Leaderboard --}}
    <div class="col-span-12 xl:col-span-5">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">🏆 Ranking de Ingenieros</h3>
                <p class="text-xs text-gray-400 mt-0.5">Por bonos aprobados/pagados</p>
            </div>
            <div class="p-5 space-y-4">
                @php
                $emojis = ['🥇','🥈','🥉','4️⃣','5️⃣'];
                $colors = ['bg-success-500','bg-orange-500','bg-brand-500','bg-blue-light-500','bg-purple-500'];
                @endphp
                @forelse($ranking as $index => $r)
                <div class="flex items-center gap-4">
                    <span class="text-lg w-6">{{ $emojis[$index] ?? '·' }}</span>
                    <div class="w-9 h-9 rounded-full {{ $colors[$index % count($colors)] }} flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm font-bold">{{ substr($r->name, 0, 2) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $r->name }}</p>
                        <p class="text-xs text-gray-400">{{ $r->bonos_count }} bono{{ $r->bonos_count != 1 ? 's' : '' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-success-600 dark:text-success-400">${{ number_format($r->bonos_total, 2) }}</p>
                        <p class="text-xs text-gray-400">USD</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 text-center py-4">Sin datos de ranking aún.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Bonus Table --}}
    <div class="col-span-12 xl:col-span-7">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 relative">
            <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Historial de Bonos</h3>
                <form method="GET" action="{{ route('bonos') }}" class="flex items-center gap-2">
                    <select name="status" class="px-3 py-1.5 text-xs border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
                        <option value="">Todo estado</option>
                        <option value="pendiente" {{ request('status') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="aprobado" {{ request('status') === 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                        <option value="pagado" {{ request('status') === 'pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="rechazado" {{ request('status') === 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                    </select>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ingeniero</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Proyecto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($bonuses as $b)
                        @php
                            $colorsUser = ['bg-brand-500','bg-purple-500','bg-success-500','bg-warning-500','bg-error-500','bg-blue-light-500'];
                            $colorUser = $colorsUser[crc32($b->engineer->name ?? '') % count($colorsUser)];
                            $stateClass = match($b->status) {
                                'pagado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                'aprobado' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
                                'pendiente' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                'rechazado' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                                default => 'bg-gray-100 text-gray-500'
                            };
                            $stateIcon = match($b->status) {
                                'pagado' => '✓ ',
                                'aprobado' => '✓ ',
                                'pendiente' => '⏳ ',
                                'rechazado' => '✕ ',
                                default => ''
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full {{ $colorUser }} flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-bold">{{ substr($b->engineer->name ?? '?', 0, 1) }}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $b->engineer->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                {{ $b->project->project_name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-sm font-bold {{ $b->status === 'rechazado' ? 'text-gray-400 line-through' : 'text-success-600 dark:text-success-400' }}">${{ number_format($b->amount, 2) }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $b->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="whitespace-nowrap px-2.5 py-1 text-xs font-medium rounded-full {{ $stateClass }}">
                                    {{ $stateIcon }}{{ ucfirst($b->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex flex-wrap gap-1 justify-end">
                                    @if($b->status === 'pendiente')
                                    <form method="POST" action="{{ route('bonos.approve', $b) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-brand-600 bg-brand-50 rounded hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:hover:bg-brand-500/20">Aprobar</button>
                                    </form>
                                    <form method="POST" action="{{ route('bonos.reject', $b) }}" onsubmit="return confirm('\u00bfRechazar este bono?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-error-600 bg-error-50 rounded hover:bg-error-100 transition-colors dark:bg-error-500/10 dark:hover:bg-error-500/20">Rechazar</button>
                                    </form>
                                    @elseif($b->status === 'aprobado')
                                    <form method="POST" action="{{ route('bonos.paid', $b) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-2 py-1 text-xs font-medium text-success-600 bg-success-50 rounded hover:bg-success-100 transition-colors dark:bg-success-500/10 dark:hover:bg-success-500/20">Marcar Pagado</button>
                                    </form>
                                    @elseif($b->status === 'rechazado' && $b->rejection_reason)
                                    <span class="text-xs text-error-500 italic max-w-[120px] truncate" title="{{ $b->rejection_reason }}">{{ $b->rejection_reason }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-xs text-gray-400">No hay bonos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($bonuses->hasPages())
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex justify-center">
                {{ $bonuses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Bonus Rules --}}
<div class="mt-5 rounded-2xl border border-brand-200 bg-brand-50 p-5 dark:border-brand-500/20 dark:bg-brand-500/10">
    <h3 class="text-sm font-semibold text-brand-800 dark:text-brand-300 mb-3">📋 Reglas del Sistema de Bonos</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-brand-700 dark:text-brand-400">
        <div class="flex items-start gap-2">
            <svg class="flex-shrink-0 mt-0.5 text-brand-500" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
            <div>
                <p class="font-semibold mb-0.5">Condición de activación</p>
                <p>Proyecto completado en fecha límite o antes, con ingeniero responsable habiendo completado sus tareas y evaluado en el controller.</p>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <svg class="flex-shrink-0 mt-0.5 text-brand-500" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="currentColor"/></svg>
            <div>
                <p class="font-semibold mb-0.5">Monto del bono</p>
                <p>Monto definible al completar, asignado a ingeniero(s). Un admin debe aprobar antes del pago correspondiente.</p>
            </div>
        </div>
        <div class="flex items-start gap-2">
            <svg class="flex-shrink-0 mt-0.5 text-brand-500" width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="1" y="4" width="22" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><line x1="1" y1="10" x2="23" y2="10" stroke="currentColor" stroke-width="1.5"/></svg>
            <div>
                <p class="font-semibold mb-0.5">Proceso de pago</p>
                <p>Bonos generados entran en Pendiente. Al ser Aprobados, pueden ser liquidados (Pagados) mensualmente por gerencia.</p>
            </div>
        </div>
    </div>
</div>

@push('modals')
{{-- MODAL: Nuevo Bono Manual --}}
<div id="modal-nuevo-bono" class="hidden fixed inset-0 z-[999999] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modal-nuevo-bono').classList.add('hidden')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">🏆 Registrar Bono Manual</h3>
            <button onclick="document.getElementById('modal-nuevo-bono').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form method="POST" action="{{ route('bonos.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Ingeniero *</label>
                    <select name="engineer_id" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Seleccionar...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Monto (USD) *</label>
                    <input type="number" name="amount" min="1" step="0.01" required placeholder="50.00" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Razón / Descripción *</label>
                    <textarea name="reason" required rows="3" placeholder="Ej: Bono por desempeño excepcional..." class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none resize-none"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-nuevo-bono').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">Registrar Bono</button>
            </div>
        </form>
    </div>
</div>
@endpush
@endsection
