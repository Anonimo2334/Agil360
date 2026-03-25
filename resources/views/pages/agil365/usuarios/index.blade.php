@extends('layouts.app')

@section('content')
{{-- Flash --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm dark:bg-success-500/10 dark:border-success-500/20 dark:text-success-400 flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif
@if(session('error'))
<div id="flash-err" class="mb-4 p-4 bg-error-50 border border-error-200 text-error-700 rounded-xl text-sm dark:bg-error-500/10 dark:border-error-500/20 dark:text-error-400 flex items-center justify-between">
    <span>✕ {{ session('error') }}</span>
    <button onclick="document.getElementById('flash-err').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestión de Usuarios</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Administración de cuentas y roles del sistema</p>
    </div>
    <button onclick="document.getElementById('modal-nuevo-usuario').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        Nuevo Usuario
    </button>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('usuarios') }}" id="filter-form">
<div class="mb-5 flex flex-wrap items-center gap-3">
    {{-- Role filter tabs --}}
    <div class="flex items-center gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl w-fit flex-wrap">
        <button type="button" onclick="filterByRole('')" data-role="" class="role-tab active px-4 py-2 text-xs font-medium rounded-lg transition-colors bg-white dark:bg-gray-900 text-gray-800 dark:text-white shadow-sm">Todos</button>
        @foreach($roles as $role)
        <button type="button" onclick="filterByRole('{{ $role->id }}')" data-role="{{ $role->id }}" class="role-tab px-4 py-2 text-xs font-medium rounded-lg transition-colors text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">{{ ucfirst($role->slug) }}</button>
        @endforeach
    </div>
    <input type="hidden" name="role_id" id="role-id-input" value="{{ request('role_id') }}">

    <div class="relative">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar usuario..." class="pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none w-64" onchange="this.form.submit()">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
    </div>
    <select name="status" class="px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none" onchange="this.form.submit()">
        <option value="">Todo estado</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>
</div>
</form>

{{-- Users Table --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Usuario</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Rol</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Departamento</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Teléfono</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Estado</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Creado</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($users as $user)
                @php
                    $roleSlug = $user->role->slug ?? '';
                    $rolLabel = ucfirst($roleSlug);
                    $rolClass = match($roleSlug) {
                        'super_admin','superadmin' => 'bg-error-50 text-error-700 dark:bg-error-500/10 dark:text-error-400',
                        'admin'     => 'bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400',
                        'gerente'   => 'bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400',
                        'ingeniero' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400',
                        'soporte'   => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                        default     => 'bg-gray-100 text-gray-500',
                    };
                    $colors = ['bg-brand-500','bg-purple-500','bg-success-500','bg-warning-500','bg-error-500','bg-blue-light-500','bg-orange-500','bg-gray-600'];
                    $colorIdx = crc32($user->name) % count($colors);
                    $userColor = $colors[abs($colorIdx)];
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full {{ $userColor }} flex items-center justify-center flex-shrink-0">
                                <span class="text-white text-xs font-bold">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</td>
                    <td class="px-5 py-4">
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full {{ $rolClass }}">{{ $rolLabel ?: '—' }}</span>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $user->department ?? '—' }}</td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">{{ $user->phone ?? '—' }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-success-500' : 'bg-gray-400' }}"></div>
                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $user->is_active ? 'Activo' : 'Inactivo' }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openEditUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role_id }}', '{{ addslashes($user->phone ?? '') }}', '{{ addslashes($user->department ?? '') }}')"
                                class="p-1.5 rounded-lg text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 transition-colors" title="Editar">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" stroke="currentColor" stroke-width="1.5"/></svg>
                            </button>
                            <form method="POST" action="{{ route('usuarios.status', $user) }}" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="p-1.5 rounded-lg {{ $user->is_active ? 'text-warning-500 hover:bg-warning-50 dark:hover:bg-warning-500/10' : 'text-success-500 hover:bg-success-50 dark:hover:bg-success-500/10' }} transition-colors" title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                    @if($user->is_active)
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><rect x="6" y="4" width="4" height="16" rx="1" fill="currentColor"/><rect x="14" y="4" width="4" height="16" rx="1" fill="currentColor"/></svg>
                                    @else
                                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polygon points="5,3 19,12 5,21" fill="currentColor"/></svg>
                                    @endif
                                </button>
                            </form>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar usuario {{ addslashes($user->name) }}?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition-colors" title="Eliminar">
                                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24"><polyline points="3,6 5,6 21,6" stroke="currentColor" stroke-width="1.5"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">
                        No se encontraron usuarios. <button onclick="document.getElementById('modal-nuevo-usuario').classList.remove('hidden')" class="text-brand-500 hover:underline">Crear usuario</button>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
        <p class="text-xs text-gray-500 dark:text-gray-400">Mostrando {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }} de {{ $users->total() }} usuarios</p>
        <div>{{ $users->links() }}</div>
    </div>
</div>

{{-- MODAL: Nuevo Usuario --}}
<div id="modal-nuevo-usuario" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Nuevo Usuario</h3>
            <button onclick="document.getElementById('modal-nuevo-usuario').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre completo *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña *</label>
                    <input type="password" name="password" required minlength="8" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar contraseña *</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rol</label>
                    <select name="role_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Sin rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->slug) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                        <input type="text" name="phone" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                        <input type="text" name="department" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" checked id="is-active-new" class="rounded border-gray-300">
                    <label for="is-active-new" class="text-xs text-gray-700 dark:text-gray-300">Usuario activo</label>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-nuevo-usuario').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600">Crear Usuario</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Editar Usuario --}}
<div id="modal-edit-usuario" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Editar Usuario</h3>
            <button onclick="document.getElementById('modal-edit-usuario').classList.add('hidden')" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕</button>
        </div>
        <form id="edit-user-form" method="POST" action="">
            @csrf @method('PUT')
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre *</label>
                    <input type="text" name="name" id="edit-user-name" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Email *</label>
                    <input type="email" name="email" id="edit-user-email" required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva contraseña (opcional)</label>
                    <input type="password" name="password" minlength="8" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Rol</label>
                    <select name="role_id" id="edit-user-role" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                        <option value="">Sin rol</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ ucfirst($role->slug) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Teléfono</label>
                        <input type="text" name="phone" id="edit-user-phone" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Departamento</label>
                        <input type="text" name="department" id="edit-user-dept" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-4">
                <button type="button" onclick="document.getElementById('modal-edit-usuario').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600">Guardar</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditUser(id, name, email, roleId, phone, dept) {
    document.getElementById('edit-user-form').action = '/usuarios/' + id;
    document.getElementById('edit-user-name').value = name;
    document.getElementById('edit-user-email').value = email;
    document.getElementById('edit-user-role').value = roleId || '';
    document.getElementById('edit-user-phone').value = phone;
    document.getElementById('edit-user-dept').value = dept;
    document.getElementById('modal-edit-usuario').classList.remove('hidden');
}

function filterByRole(roleId) {
    document.getElementById('role-id-input').value = roleId;
    document.getElementById('filter-form').submit();
}

// Highlight active role tab
const tabs = document.querySelectorAll('.role-tab');
const currentRole = '{{ request("role_id") }}';
tabs.forEach(tab => {
    tab.classList.remove('bg-white', 'dark:bg-gray-900', 'text-gray-800', 'dark:text-white', 'shadow-sm');
    tab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400');
    if (tab.dataset.role === currentRole) {
        tab.classList.add('bg-white', 'dark:bg-gray-900', 'text-gray-800', 'dark:text-white', 'shadow-sm');
        tab.classList.remove('text-gray-500', 'dark:text-gray-400');
    }
});
</script>
@endpush
@endsection
