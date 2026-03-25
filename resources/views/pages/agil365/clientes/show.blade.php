@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Detalle del cliente</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('clientes.edit', $company) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
            Editar Cliente
        </a>
        <a href="{{ route('clientes') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
            ← Volver
        </a>
    </div>
</div>

<div class="grid grid-cols-12 gap-6">
    {{-- Company Info --}}
    <div class="col-span-12 lg:col-span-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-brand-500 flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($company->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="font-bold text-gray-900 dark:text-white">{{ $company->name }}</h2>
                    <span class="inline-flex items-center gap-1 text-xs {{ $company->is_active ? 'text-success-600 bg-success-50' : 'text-gray-500 bg-gray-100' }} px-2 py-0.5 rounded-full mt-1">
                        {{ $company->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
            </div>
            <div class="space-y-3">
                @if($company->contact_name)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-400 w-20 flex-shrink-0">Contacto</span>
                    <span class="text-gray-700 dark:text-gray-300 font-medium">{{ $company->contact_name }}</span>
                </div>
                @endif
                @if($company->email)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-400 w-20 flex-shrink-0">Email</span>
                    <a href="mailto:{{ $company->email }}" class="text-brand-500 hover:underline">{{ $company->email }}</a>
                </div>
                @endif
                @if($company->phone)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-400 w-20 flex-shrink-0">Teléfono</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $company->phone }}</span>
                </div>
                @endif
                @if($company->website)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-400 w-20 flex-shrink-0">Web</span>
                    <a href="{{ $company->website }}" target="_blank" class="text-brand-500 hover:underline truncate">{{ $company->website }}</a>
                </div>
                @endif
                @if($company->country)
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-gray-400 w-20 flex-shrink-0">País</span>
                    <span class="text-gray-700 dark:text-gray-300">{{ $company->country }}</span>
                </div>
                @endif
            </div>
            @if($company->notes)
            <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notas Internas</p>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $company->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Projects --}}
    <div class="col-span-12 lg:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Proyectos ({{ $company->projects->count() }})</h3>
                <a href="{{ route('proyectos.create') }}" class="text-xs font-medium text-brand-500 hover:text-brand-600">+ Nuevo proyecto</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Proyecto</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ingeniero</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Avance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($company->projects as $project)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $project->project_name }}</p>
                                @if($project->bot_name)
                                <p class="text-xs text-gray-400">Bot: {{ $project->bot_name }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">{{ $project->primaryEngineer?->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 w-16">
                                        <div class="{{ $project->progress_percentage >= 80 ? 'bg-success-500' : ($project->progress_percentage >= 50 ? 'bg-blue-light-500' : 'bg-error-500') }} h-1.5 rounded-full" style="width: {{ $project->progress_percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $project->progress_percentage }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $project->status === 'completado' ? 'bg-success-50 text-success-700' :
                                       ($project->status === 'en_proceso' ? 'bg-blue-light-50 text-blue-light-700' :
                                       ($project->status === 'soporte' ? 'bg-warning-50 text-warning-700' : 'bg-gray-100 text-gray-600')) }}">
                                    {{ $project->status_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('proyectos.show', $project) }}" class="text-xs text-brand-500 hover:text-brand-600">Ver →</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">Sin proyectos registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
