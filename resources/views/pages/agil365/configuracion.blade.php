@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Configuración del Sistema</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Administración y ajustes generales de Agil365</p>
    </div>
    @if($settings['maintenance_mode'] ?? false)
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-warning-50 text-warning-600 border border-warning-200 dark:bg-warning-500/10 dark:text-warning-400 dark:border-warning-500/20">
        <span class="w-1.5 h-1.5 rounded-full bg-warning-500 animate-pulse"></span>
        Modo mantenimiento activo
    </span>
    @endif
</div>

@if(session('success'))
<div id="flash-msg" class="mb-5 p-4 bg-success-50 text-success-700 rounded-xl dark:bg-success-500/10 dark:text-success-400 border border-success-200 dark:border-success-500/20 flex items-center justify-between">
    <span class="flex items-center gap-2">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
        {{ session('success') }}
    </span>
    <button onclick="document.getElementById('flash-msg').remove()" class="opacity-50 hover:opacity-100">✕</button>
</div>
@endif

@if($errors->any())
<div class="mb-5 p-4 bg-error-50 text-error-700 rounded-xl dark:bg-error-500/10 dark:text-error-400 border border-error-200 dark:border-error-800">
    <ul class="list-disc pl-5 space-y-1 text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid grid-cols-12 gap-5">

    {{-- ── Sidebar Navigation ── --}}
    <div class="col-span-12 xl:col-span-3">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-3 sticky top-6">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-3 mb-2">Secciones</p>
            <nav class="space-y-1">
                @php
                $navItems = [
                    [
                        'tab'   => 'general',
                        'label' => 'General',
                        'desc'  => 'Sistema, idioma, zona horaria',
                        'icon'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    ],
                    [
                        'tab'   => 'alertas',
                        'label' => 'Alertas',
                        'desc'  => 'Umbrales, notificaciones, reglas',
                        'icon'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    ],
                    [
                        'tab'   => 'bonos',
                        'label' => 'Bonos',
                        'desc'  => 'Montos, aprobación, condiciones',
                        'icon'  => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'tab'   => null,
                        'label' => 'Roles y permisos',
                        'desc'  => 'Accesos y restricciones',
                        'link'  => route('roles'),
                        'icon'  => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
                    ],
                ];
                @endphp
                @foreach($navItems as $nav)
                @php $isActive = ($nav['tab'] !== null && $tab === $nav['tab']); @endphp
                <a href="{{ $nav['link'] ?? route('configuracion', ['tab' => $nav['tab']]) }}"
                   class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-colors
                       {{ $isActive ? 'bg-brand-50 dark:bg-brand-500/10' : 'hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $isActive ? 'bg-brand-500 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                            <path d="{{ $nav['icon'] }}" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium {{ $isActive ? 'text-brand-600 dark:text-brand-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $nav['label'] }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $nav['desc'] }}</p>
                    </div>
                    @if($isActive)
                    <div class="ml-auto w-1.5 h-4 bg-brand-500 rounded-full flex-shrink-0"></div>
                    @endif
                </a>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- ── Content Area ── --}}
    <div class="col-span-12 xl:col-span-9">

        {{-- ════════════════ TAB: GENERAL ════════════════ --}}
        @if($tab === 'general')
        <form action="{{ route('configuracion.update') }}" method="POST">
            @csrf
            <input type="hidden" name="tab" value="general">

            {{-- Application Info --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-brand-500" fill="none" viewBox="0 0 24 24"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Información del Sistema</h3>
                        <p class="text-xs text-gray-400">Nombre, contacto y credenciales básicas</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del sistema</label>
                        <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <p class="text-xs text-gray-400 mt-1">Aparece en el navegador y correos</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email de notificaciones</label>
                        <input type="email" name="notification_email" value="{{ old('notification_email', $settings['notification_email']) }}"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <p class="text-xs text-gray-400 mt-1">Destino de las alertas automáticas</p>
                    </div>
                </div>
            </div>

            {{-- Locale --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-blue-light-500" fill="none" viewBox="0 0 24 24"><path d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Localización</h3>
                        <p class="text-xs text-gray-400">Idioma, zona horaria y formato de fechas</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Zona horaria</label>
                        <select name="timezone" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            @foreach(['America/Mexico_City'=>'México (UTC-6)','America/Bogota'=>'Colombia (UTC-5)','America/Lima'=>'Perú (UTC-5)','America/Santiago'=>'Chile (UTC-4)','America/Buenos_Aires'=>'Argentina (UTC-3)','America/Sao_Paulo'=>'Brasil (UTC-3)','America/Caracas'=>'Venezuela (UTC-4)','America/New_York'=>'New York (UTC-5)','Europe/Madrid'=>'Madrid (UTC+1)'] as $tz => $label)
                                <option value="{{ $tz }}" {{ old('timezone', $settings['timezone']) === $tz ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Idioma</label>
                        <select name="language" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="es" {{ old('language', $settings['language']) === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="en" {{ old('language', $settings['language']) === 'en' ? 'selected' : '' }}>English</option>
                            <option value="pt" {{ old('language', $settings['language']) === 'pt' ? 'selected' : '' }}>Português</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Formato de fecha</label>
                        <select name="date_format" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <option value="d/m/Y" {{ old('date_format', $settings['date_format']) === 'd/m/Y' ? 'selected' : '' }}>DD/MM/AAAA</option>
                            <option value="m/d/Y" {{ old('date_format', $settings['date_format']) === 'm/d/Y' ? 'selected' : '' }}>MM/DD/AAAA</option>
                            <option value="Y-m-d" {{ old('date_format', $settings['date_format']) === 'Y-m-d' ? 'selected' : '' }}>AAAA-MM-DD</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- System --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                        <svg width="18" height="18" class="text-gray-500" fill="none" viewBox="0 0 24 24"><path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Preferencias del Sistema</h3>
                        <p class="text-xs text-gray-400">Paginación y estado del sistema</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-gray-800">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Elementos por página</p>
                            <p class="text-xs text-gray-400">Cantidad de registros en tablas y listados</p>
                        </div>
                        <select name="items_per_page" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            @foreach([10, 20, 50, 100] as $n)
                                <option value="{{ $n }}" {{ old('items_per_page', $settings['items_per_page']) == $n ? 'selected' : '' }}>{{ $n }} registros</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Modo mantenimiento</p>
                            <p class="text-xs text-gray-400">Deshabilita el acceso para usuarios no-admin mientras trabajas</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="maintenance_mode" value="1" {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-warning-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm">Guardar cambios</button>
            </div>
        </form>


        {{-- ════════════════ TAB: ALERTAS ════════════════ --}}
        @elseif($tab === 'alertas')
        <form action="{{ route('configuracion.update') }}" method="POST">
            @csrf
            <input type="hidden" name="tab" value="alertas">

            {{-- Triggering Conditions --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-error-50 dark:bg-error-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-error-500" fill="none" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Condiciones de Disparo</h3>
                        <p class="text-xs text-gray-400">Cuándo se generan alertas automáticas</p>
                    </div>
                </div>
                <div class="space-y-0">
                    {{-- Alert risk toggle --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Alertas de riesgo por avance bajo</p>
                            <p class="text-xs text-gray-400 mt-0.5">Genera una alerta cuando el avance es menor al umbral definido y el tiempo restante es crítico</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="alert_risk_enabled" value="1" {{ $settings['alert_risk_enabled'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    {{-- Thresholds --}}
                    <div class="py-4 border-b border-gray-100 dark:border-gray-800">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 mb-3">Umbrales de riesgo</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Progreso crítico</p>
                                    <p class="text-xs text-gray-400">Si el avance es menor a este % y el tiempo es escaso</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <input type="number" name="alert_risk_threshold_progress" value="{{ old('alert_risk_threshold_progress', $settings['alert_risk_threshold_progress']) }}" min="1" max="99"
                                        class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-lg bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                    <span class="text-xs text-gray-400">%</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-xl">
                                <div class="flex-1">
                                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Tiempo restante crítico</p>
                                    <p class="text-xs text-gray-400">Si el tiempo restante es menor a este %</p>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <input type="number" name="alert_risk_threshold_time" value="{{ old('alert_risk_threshold_time', $settings['alert_risk_threshold_time']) }}" min="1" max="99"
                                        class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-lg bg-white dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                    <span class="text-xs text-gray-400">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Inactivity --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Alerta por inactividad</p>
                            <p class="text-xs text-gray-400 mt-0.5">Alerta cuando un proyecto no tiene actualización de progreso por X días</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" name="alert_no_update_days" value="{{ old('alert_no_update_days', $settings['alert_no_update_days']) }}" min="1" max="30"
                                class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <span class="text-xs text-gray-400">días</span>
                        </div>
                    </div>

                    {{-- Overdue projects --}}
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Proyectos vencidos</p>
                            <p class="text-xs text-gray-400 mt-0.5">Alerta cuando un proyecto supera su fecha límite sin completarse</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="alert_overdue_projects" value="1" {{ $settings['alert_overdue_projects'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    {{-- Overdue tasks --}}
                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Tareas vencidas</p>
                            <p class="text-xs text-gray-400 mt-0.5">Alerta cuando una tarea supera su fecha de entrega sin completarse</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="alert_overdue_tasks" value="1" {{ $settings['alert_overdue_tasks'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-brand-500" fill="none" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notificaciones por Email</h3>
                        <p class="text-xs text-gray-400">Configura cuándo y qué notificaciones se envían</p>
                    </div>
                </div>
                <div class="space-y-0">
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Notificaciones por email activas</p>
                            <p class="text-xs text-gray-400 mt-0.5">Habilita el envío de alertas al correo de notificaciones del sistema</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="alert_email_notifications" value="1" {{ $settings['alert_email_notifications'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Solo alertas críticas por email</p>
                            <p class="text-xs text-gray-400 mt-0.5">Filtra y envía únicamente alertas de severidad crítica o error</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="alert_critical_only_email" value="1" {{ $settings['alert_critical_only_email'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Frecuencia de resumen</p>
                            <p class="text-xs text-gray-400 mt-0.5">Con qué frecuencia se envía el digest de alertas por email</p>
                        </div>
                        <select name="alert_digest_frequency" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none flex-shrink-0">
                            <option value="realtime" {{ old('alert_digest_frequency', $settings['alert_digest_frequency']) === 'realtime' ? 'selected' : '' }}>En tiempo real</option>
                            <option value="hourly"   {{ old('alert_digest_frequency', $settings['alert_digest_frequency']) === 'hourly'   ? 'selected' : '' }}>Cada hora</option>
                            <option value="daily"    {{ old('alert_digest_frequency', $settings['alert_digest_frequency']) === 'daily'    ? 'selected' : '' }}>Resumen diario</option>
                            <option value="weekly"   {{ old('alert_digest_frequency', $settings['alert_digest_frequency']) === 'weekly'   ? 'selected' : '' }}>Resumen semanal</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Auto-resolver alertas inactivas</p>
                            <p class="text-xs text-gray-400 mt-0.5">Resolver automáticamente alertas que no tienen una acción después de X días (0 = nunca)</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" name="alert_auto_resolve_days" value="{{ old('alert_auto_resolve_days', $settings['alert_auto_resolve_days']) }}" min="0" max="90"
                                class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <span class="text-xs text-gray-400">días</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('configuracion', ['tab' => 'alertas']) }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm">Guardar cambios</button>
            </div>
        </form>


        {{-- ════════════════ TAB: BONOS ════════════════ --}}
        @elseif($tab === 'bonos')
        <form action="{{ route('configuracion.update') }}" method="POST">
            @csrf
            <input type="hidden" name="tab" value="bonos">

            {{-- Bonus Enable --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-success-50 dark:bg-success-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-success-500" fill="none" viewBox="0 0 24 24"><path d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Estado del Sistema de Bonos</h3>
                        <p class="text-xs text-gray-400">Activa o desactiva los bonos para todos los ingenieros</p>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Sistema de bonos activo</p>
                        <p class="text-xs text-gray-400 mt-0.5">Cuando está desactivado, no se generarán nuevos bonos aunque los proyectos se completen</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="bonus_enabled" value="1" {{ $settings['bonus_enabled'] ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-success-500"></div>
                    </label>
                </div>
            </div>

            {{-- Bonus Amount --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-brand-50 dark:bg-brand-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-brand-500" fill="none" viewBox="0 0 24 24"><path d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Monto y Moneda</h3>
                        <p class="text-xs text-gray-400">Configura el valor del bono por proyecto completado a tiempo</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Monto del bono por proyecto</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">$</span>
                            <input type="number" name="bonus_amount" value="{{ old('bonus_amount', $settings['bonus_amount']) }}" min="0"
                                class="w-full pl-7 pr-16 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs" id="currency-display">{{ $settings['bonus_currency'] }}</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Monto base por proyecto completado antes del plazo</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Moneda</label>
                        <select name="bonus_currency" id="bonus_currency_select" onchange="document.getElementById('currency-display').textContent=this.value"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            @foreach(['USD'=>'USD — Dólar','EUR'=>'EUR — Euro','MXN'=>'MXN — Peso Mexicano','COP'=>'COP — Peso Colombiano','ARS'=>'ARS — Peso Argentino','CLP'=>'CLP — Peso Chileno','PEN'=>'PEN — Sol Peruano','BRL'=>'BRL — Real Brasileño'] as $code => $label)
                                <option value="{{ $code }}" {{ old('bonus_currency', $settings['bonus_currency']) === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Approval & Conditions --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-warning-50 dark:bg-warning-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-warning-500" fill="none" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Aprobación y Condiciones</h3>
                        <p class="text-xs text-gray-400">Quién aprueba y qué condiciones debe cumplir un proyecto para generar bono</p>
                    </div>
                </div>
                <div class="space-y-0">
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Flujo de aprobación</p>
                            <p class="text-xs text-gray-400 mt-0.5">Quién debe aprobar el bono antes de que pueda pagarse al ingeniero</p>
                        </div>
                        <select name="bonus_approval_type" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none flex-shrink-0">
                            <option value="auto"    {{ old('bonus_approval_type', $settings['bonus_approval_type']) === 'auto'    ? 'selected':'' }}>Automática (sin aprobación)</option>
                            <option value="gerente" {{ old('bonus_approval_type', $settings['bonus_approval_type']) === 'gerente' ? 'selected':'' }}>Requiere aprobación del gerente</option>
                            <option value="admin"   {{ old('bonus_approval_type', $settings['bonus_approval_type']) === 'admin'   ? 'selected':'' }}>Requiere aprobación del admin</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Requerir entrega a tiempo</p>
                            <p class="text-xs text-gray-400 mt-0.5">El proyecto debe completarse antes de la fecha límite para calificar al bono</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="bonus_require_on_time" value="1" {{ $settings['bonus_require_on_time'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Progreso mínimo requerido</p>
                            <p class="text-xs text-gray-400 mt-0.5">Porcentaje mínimo de avance del proyecto para poder generar el bono</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" name="bonus_min_project_progress" value="{{ old('bonus_min_project_progress', $settings['bonus_min_project_progress']) }}" min="1" max="100"
                                class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <span class="text-xs text-gray-400">%</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Bonos máximos por mes</p>
                            <p class="text-xs text-gray-400 mt-0.5">Límite de bonos que puede acumular un ingeniero en el mes</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" name="bonus_max_per_month" value="{{ old('bonus_max_per_month', $settings['bonus_max_per_month']) }}" min="1" max="100"
                                class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <span class="text-xs text-gray-400">bonos</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Expiración de bonos pendientes</p>
                            <p class="text-xs text-gray-400 mt-0.5">Días hasta que un bono pendiente de aprobación expira automáticamente (0 = no expira)</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <input type="number" name="bonus_expiry_days" value="{{ old('bonus_expiry_days', $settings['bonus_expiry_days']) }}" min="0" max="365"
                                class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none">
                            <span class="text-xs text-gray-400">días</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notifications --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-5">
                <div class="flex items-center gap-3 mb-5 pb-4 border-b border-gray-100 dark:border-gray-800">
                    <div class="w-9 h-9 rounded-xl bg-blue-light-50 dark:bg-blue-light-500/10 flex items-center justify-center">
                        <svg width="18" height="18" class="text-blue-light-500" fill="none" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notificaciones de Bonos</h3>
                        <p class="text-xs text-gray-400">A quién se notifica cuando se genera o aprueba un bono</p>
                    </div>
                </div>
                <div class="space-y-0">
                    <div class="flex items-center justify-between py-4 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Notificar al ingeniero</p>
                            <p class="text-xs text-gray-400 mt-0.5">Enviar email al ingeniero cuando se genera, aprueba o rechaza un bono</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="bonus_notify_engineer" value="1" {{ $settings['bonus_notify_engineer'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1 pr-6">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Notificar al administrador</p>
                            <p class="text-xs text-gray-400 mt-0.5">Enviar email al administrador cuando se genera un nuevo bono pendiente de aprobación</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                            <input type="checkbox" name="bonus_notify_admin" value="1" {{ $settings['bonus_notify_admin'] ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-500"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('configuracion', ['tab' => 'bonos']) }}" class="px-6 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">Cancelar</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm">Guardar cambios</button>
            </div>
        </form>
        @endif

    </div>{{-- end content --}}
</div>
@endsection
