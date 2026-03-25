@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Proyecto</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $project->project_name }}</p>
    </div>
    <a href="{{ route('proyectos.show', $project) }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
        ← Volver
    </a>
</div>

<div class="max-w-3xl rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
    @if($errors->any())
    <div class="mb-5 p-4 bg-error-50 border border-error-200 rounded-xl dark:bg-error-500/10 dark:border-error-500/20">
        @foreach($errors->all() as $error)
            <p class="text-sm text-error-700 dark:text-error-400">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('proyectos.update', $project) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del Proyecto <span class="text-error-500">*</span></label>
                <input type="text" name="project_name" value="{{ old('project_name', $project->project_name) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cliente <span class="text-error-500">*</span></label>
                <select name="company_id" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                    @foreach($companies as $company)
                    <option value="{{ $company->id }}" {{ old('company_id', $project->company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CEO / Contacto Principal</label>
                <input type="text" name="ceo" value="{{ old('ceo', $project->ceo) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero Primario</label>
                <select name="primary_engineer_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                    <option value="">— Sin asignar —</option>
                    @foreach($engineers as $engineer)
                    <option value="{{ $engineer->id }}" {{ old('primary_engineer_id', $project->primary_engineer_id) == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero Backup</label>
                <select name="backup_engineer_id" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                    <option value="">— Sin asignar —</option>
                    @foreach($engineers as $engineer)
                    <option value="{{ $engineer->id }}" {{ old('backup_engineer_id', $project->backup_engineer_id) == $engineer->id ? 'selected' : '' }}>{{ $engineer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha Inicio</label>
                <input type="date" name="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha Límite</label>
                <input type="date" name="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado del Proyecto</label>
                <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                    <option value="iniciado" {{ old('status', $project->status) == 'iniciado' ? 'selected' : '' }}>Iniciado</option>
                    <option value="en_proceso" {{ old('status', $project->status) == 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                    <option value="soporte" {{ old('status', $project->status) == 'soporte' ? 'selected' : '' }}>Soporte</option>
                    <option value="completado" {{ old('status', $project->status) == 'completado' ? 'selected' : '' }}>Completado</option>
                    <option value="cancelado" {{ old('status', $project->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Avance (%)</label>
                <input type="number" name="progress_percentage" value="{{ old('progress_percentage', $project->progress_percentage) }}" min="0" max="100" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plataforma</label>
                <input type="text" name="platform" value="{{ old('platform', $project->platform) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del Bot</label>
                <input type="text" name="bot_name" value="{{ old('bot_name', $project->bot_name) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">URL del Sitio Web</label>
                <input type="url" name="website_url" value="{{ old('website_url', $project->website_url) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Servidor / Hosting</label>
                <input type="text" name="server_hosting" value="{{ old('server_hosting', $project->server_hosting) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas Internas</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">{{ old('notes', $project->notes) }}</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
            <button type="submit" class="px-6 py-2.5 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600 transition-colors">Guardar Cambios</button>
            <a href="{{ route('proyectos.show', $project) }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
