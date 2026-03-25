<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acceso Denegado | Agil365</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%); }
    </style>
</head>
<body class="h-full flex items-center justify-center min-h-screen">
    <div class="text-center px-6 py-16 max-w-md mx-auto">
        {{-- Icon --}}
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-error-100 dark:bg-error-500/20 flex items-center justify-center shadow-lg">
            <svg width="44" height="44" fill="none" viewBox="0 0 24 24" class="text-error-500">
                <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        {{-- Error code --}}
        <p class="text-7xl font-black text-gray-200 dark:text-gray-700 mb-2 select-none">403</p>

        {{-- Title --}}
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Acceso Denegado</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-2 text-sm">
            No tienes los permisos necesarios para acceder a esta sección.
        </p>
        @if(auth()->check())
        <p class="text-xs text-gray-400 mb-8">
            Tu rol actual: <span class="font-medium text-brand-600 dark:text-brand-400">{{ auth()->user()->role?->name ?? 'Sin rol' }}</span>
        </p>
        @endif

        {{-- Role Summary --}}
        @if(auth()->check() && auth()->user()->role)
        <div class="mb-8 p-4 bg-white/70 dark:bg-gray-800/70 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 text-left">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Tus accesos disponibles</p>
            @php
                $perms = auth()->user()->role?->permissions ?? [];
                $labels = [
                    'clientes.ver'        => ['📋 Clientes', 'Ver listado'],
                    'proyectos.ver'       => ['📁 Proyectos', 'Ver proyectos'],
                    'proyectos.crear'     => ['📁 Proyectos', 'Crear nuevos'],
                    'proyectos.editar'    => ['📁 Proyectos', 'Editar'],
                    'tareas.ver'          => ['✅ Tareas', 'Ver y seguir'],
                    'tareas.editar'       => ['✅ Tareas', 'Actualizar estado'],
                    'tareas.crear'        => ['✅ Tareas', 'Crear tareas'],
                    'reportes.ver'        => ['📊 Reportes', 'Ver reportes'],
                    'usuarios.gestionar'  => ['👥 Usuarios', 'Gestionar usuarios'],
                    'roles.gestionar'     => ['🔑 Roles', 'Gestionar roles'],
                ];
            @endphp
            @if(auth()->user()->isAdmin())
                <p class="text-xs text-success-600 dark:text-success-400 font-medium">✓ Acceso total al sistema</p>
            @elseif(!empty($perms))
                <div class="space-y-1">
                @foreach($perms as $perm)
                    @if(isset($labels[$perm]))
                    <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                        <span class="text-success-500">✓</span>
                        <span>{{ $labels[$perm][0] }} — {{ $labels[$perm][1] }}</span>
                    </div>
                    @endif
                @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400">Sin permisos asignados. Contacta al administrador.</p>
            @endif
        </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Ir al Dashboard
            </a>
            <a href="javascript:history.back()"
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Volver
            </a>
        </div>
    </div>
</body>
</html>
