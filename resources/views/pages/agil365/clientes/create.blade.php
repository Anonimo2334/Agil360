@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Cliente</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registra una nueva empresa o cliente en el sistema</p>
    </div>
    <a href="{{ route('clientes') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Volver a clientes
    </a>
</div>

@if ($errors->any())
<div class="mb-5 p-4 bg-error-50 text-error-700 rounded-xl dark:bg-error-500/10 dark:text-error-400 border border-error-200 dark:border-error-800">
    <p class="font-semibold text-sm mb-1">Por favor corrige los siguientes errores:</p>
    <ul class="list-disc pl-5 space-y-0.5 text-sm">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('clientes.store') }}" method="POST" class="grid grid-cols-12 gap-5">
    @csrf

    <div class="col-span-12 xl:col-span-8 space-y-5">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5 pb-3 border-b border-gray-100 dark:border-gray-800">Información de la Empresa</h3>
            <div class="space-y-4">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre de la empresa <span class="text-error-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            placeholder="Ej: Nexo Corp SA de CV"
                            class="w-full px-4 py-2.5 text-sm border {{ $errors->has('name') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        @error('name')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contacto principal</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}"
                            placeholder="Nombre del contacto"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="contacto@empresa.com"
                            class="w-full px-4 py-2.5 text-sm border {{ $errors->has('email') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        @error('email')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Teléfono</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            placeholder="+52 55 1234 5678"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp</label>
                        <input type="tel" name="whatsapp" value="{{ old('whatsapp') }}"
                            placeholder="+52 55 1234 5678"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sitio web</label>
                        <input type="url" name="website" value="{{ old('website') }}"
                            placeholder="https://empresa.com"
                            class="w-full px-4 py-2.5 text-sm border {{ $errors->has('website') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        @error('website')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">País</label>
                        <select name="country" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="">Seleccionar...</option>
                            @foreach(['México','Colombia','Argentina','España','EEUU','Chile','Perú','Venezuela','Brasil','Ecuador','Bolivia','Paraguay','Uruguay','Guatemala','Honduras','El Salvador','Nicaragua','Costa Rica','Panamá','República Dominicana'] as $pais)
                                <option value="{{ $pais }}" {{ old('country') === $pais ? 'selected' : '' }}>{{ $pais }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado del cliente</label>
                        <select name="is_active" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección</label>
                    <input type="text" name="address" value="{{ old('address') }}"
                        placeholder="Dirección de la empresa"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas Internas</label>
                    <textarea name="notes" rows="3"
                        placeholder="Observaciones internas del cliente..."
                        class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none">{{ old('notes') }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="col-span-12 xl:col-span-4 space-y-4">
        {{-- Info Card --}}
        <div class="rounded-2xl border border-brand-200 bg-brand-50 p-5 dark:border-brand-500/20 dark:bg-brand-500/10">
            <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-400 mb-3">📋 Información del registro</h3>
            <ul class="space-y-1.5 text-xs text-brand-600 dark:text-brand-500">
                <li class="flex items-center gap-2">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
                    Solo el nombre es obligatorio
                </li>
                <li class="flex items-center gap-2">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
                    Podrás editar los datos en cualquier momento
                </li>
                <li class="flex items-center gap-2">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="2"/></svg>
                    Los clientes activos aparecen en el selector de proyectos
                </li>
            </ul>
        </div>

        {{-- Actions --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Acciones</h3>
            <div class="space-y-3">
                <button type="submit" id="submitBtn"
                    class="w-full py-3 text-sm font-semibold bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                    Crear Cliente
                </button>
                <a href="{{ route('clientes') }}"
                    class="block w-full py-3 text-sm font-medium text-center text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                    Cancelar
                </a>
            </div>
            <p class="text-xs text-gray-400 text-center mt-3">Los campos con <span class="text-error-500">*</span> son obligatorios</p>
        </div>
    </div>
</form>

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Guardando...';
});
</script>
@endpush

@endsection
