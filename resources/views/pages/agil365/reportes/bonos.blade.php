@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reporte de Bonos</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Análisis financiero de bonificaciones otorgadas</p>
    </div>
    <div class="flex items-center gap-2">
        <button class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition-colors dark:bg-brand-500/10 dark:text-brand-400 dark:border-brand-500/20">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Exportar CSV
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Monto Total</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($stats['total_amount'], 2) }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pagado</p>
        <p class="text-2xl font-bold text-success-600 dark:text-success-400">${{ number_format($stats['paid_amount'], 2) }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Pendiente / Aprobado</p>
        <p class="text-2xl font-bold text-warning-600 dark:text-warning-400">${{ number_format($stats['pending_amount'], 2) }}</p>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Operaciones de bono</p>
        <p class="text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $stats['count'] }}</p>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ingeniero</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Proyecto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Monto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($bonuses as $b)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                    <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-200">
                        {{ $b->engineer->name ?? '—' }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $b->project->project_name ?? '—' }}</td>
                    <td class="px-4 py-3 font-bold {{ $b->status === 'rechazado' ? 'text-gray-400 line-through' : 'text-success-600 dark:text-success-400' }}">${{ number_format($b->amount, 2) }}</td>
                    <td class="px-4 py-3">
                        @php
                            $stateClass = match($b->status) {
                                'pagado' => 'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400',
                                'aprobado' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
                                'pendiente' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/10 dark:text-warning-400',
                                'rechazado' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                                default => 'bg-gray-100 text-gray-500'
                            };
                        @endphp
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $stateClass }}">{{ ucfirst($b->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $b->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-400 text-sm">No hay bonos registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
