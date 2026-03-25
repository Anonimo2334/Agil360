@extends('layouts.app')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Editar Cliente</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $company->name }}</p>
    </div>
    <a href="{{ route('clientes') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
        ← Volver
    </a>
</div>

<div class="max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
    @if($errors->any())
    <div class="mb-5 p-4 bg-error-50 border border-error-200 rounded-xl dark:bg-error-500/10 dark:border-error-500/20">
        @foreach($errors->all() as $error)
            <p class="text-sm text-error-700 dark:text-error-400">{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('clientes.update', $company) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre de Empresa <span class="text-error-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $company->name) }}" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contacto Principal</label>
                <input type="text" name="contact_name" value="{{ old('contact_name', $company->contact_name) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">WhatsApp</label>
                <input type="text" name="whatsapp" value="{{ old('whatsapp', $company->whatsapp) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Sitio Web</label>
                <input type="url" name="website" value="{{ old('website', $company->website) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">País</label>
                <input type="text" name="country" value="{{ old('country', $company->country) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado</label>
                <select name="is_active" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                    <option value="1" {{ $company->is_active ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ !$company->is_active ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Dirección</label>
                <input type="text" name="address" value="{{ old('address', $company->address) }}" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notas Internas</label>
                <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">{{ old('notes', $company->notes) }}</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-800">
            <button type="submit" class="px-6 py-2.5 bg-brand-500 text-white text-sm font-medium rounded-lg hover:bg-brand-600 transition-colors">Guardar Cambios</button>
            <a href="{{ route('clientes') }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">Cancelar</a>
        </div>
    </form>
</div>
@endsection
