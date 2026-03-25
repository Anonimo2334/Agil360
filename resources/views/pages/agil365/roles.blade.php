@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles y Permisos</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configuración de acceso para todos los roles del sistema</p>
    </div>
</div>

@if(session('success'))
<div class="mb-5 p-4 bg-green-50 text-green-700 rounded-xl dark:bg-green-500/10 dark:text-green-400 border border-green-200 dark:border-green-800">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-5 p-4 bg-red-50 text-red-700 rounded-xl dark:bg-red-500/10 dark:text-red-400 border border-red-200 dark:border-red-800">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($roles as $role)
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm flex flex-col h-full">
        <div class="p-5 border-b border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/50">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $role->name }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ collect($role->permissions)->count() }} permisos asignados</p>
        </div>
        
        <div class="p-5 flex-1 relative">
            <form action="{{ route('roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-3 h-64 overflow-y-auto pr-2 custom-scrollbar">
                    @if($role->slug === 'super_admin')
                        <div class="p-4 bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400 rounded-xl border border-brand-200 dark:border-brand-800">
                            El <b>Super Administrador</b> tiene acceso total al sistema. Sus permisos no pueden ser modificados.
                        </div>
                    @else
                        @foreach($availablePermissions as $key => $label)
                        <label class="flex items-center gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg cursor-pointer transition-colors">
                            <input 
                                type="checkbox" 
                                name="permissions[]" 
                                value="{{ $key }}"
                                {{ in_array($key, $role->permissions ?? []) ? 'checked' : '' }}
                                class="w-4 h-4 text-brand-500 rounded border-gray-300 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-700 dark:focus:ring-brand-600"
                            >
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</span>
                        </label>
                        @endforeach
                    @endif
                </div>

                @if($role->slug !== 'super_admin')
                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <button type="submit" class="w-full py-2.5 px-4 bg-brand-500 hover:bg-brand-600 text-white font-medium text-sm rounded-xl transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 dark:focus:ring-offset-gray-900">
                        Guardar Permisos
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
    @endforeach
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #374151; }
</style>
@endsection
