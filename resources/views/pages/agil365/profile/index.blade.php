@extends('layouts.app')

@section('content')
{{-- Flash messages --}}
@if(session('success'))
<div id="flash-msg" class="mb-4 p-4 bg-success-50 border border-success-200 text-success-700 rounded-xl text-sm flex items-center justify-between">
    <span>✓ {{ session('success') }}</span>
    <button onclick="document.getElementById('flash-msg').remove()" class="ml-4 opacity-60 hover:opacity-100">✕</button>
</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-center justify-between">
    <span>✗ {{ session('error') }}</span>
</div>
@endif

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mi Perfil</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gestiona tu información personal y configuraciones de cuenta.</p>
</div>

<div class="flex flex-col md:flex-row gap-6">
    {{-- Sidebar tabs --}}
    <div class="w-full md:w-64 flex-shrink-0">
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl overflow-hidden shadow-sm">
            <nav class="flex flex-col">
                <a href="{{ route('profile.index', ['tab' => 'profile']) }}" 
                   class="px-5 py-4 text-sm font-medium border-b border-gray-100 dark:border-gray-800 transition-colors
                   {{ $tab === 'profile' ? 'bg-brand-50/50 text-brand-600 border-l-4 border-l-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 border-l-4 border-l-transparent' }}">
                    ✏️ Editar Perfil
                </a>
                <a href="{{ route('profile.index', ['tab' => 'settings']) }}" 
                   class="px-5 py-4 text-sm font-medium border-b border-gray-100 dark:border-gray-800 transition-colors
                   {{ $tab === 'settings' ? 'bg-brand-50/50 text-brand-600 border-l-4 border-l-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 border-l-4 border-l-transparent' }}">
                    ⚙️ Ajustes de Cuenta
                </a>
                <a href="{{ route('profile.index', ['tab' => 'support']) }}" 
                   class="px-5 py-4 text-sm font-medium transition-colors
                   {{ $tab === 'support' ? 'bg-brand-50/50 text-brand-600 border-l-4 border-l-brand-600 dark:bg-brand-500/10 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 border-l-4 border-l-transparent' }}">
                    🆘 Soporte
                </a>
            </nav>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="flex-1">
        {{-- TAB 1: Editar Perfil --}}
        @if($tab === 'profile')
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5 border-b border-gray-100 dark:border-gray-800 pb-3">Información Personal</h2>
            
            <form action="{{ route('profile.update_info') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4 max-w-lg">
                    {{-- Avatar mock (optional real implementation later) --}}
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-full bg-brand-500 flex items-center justify-center text-white text-xl font-bold shadow-md">
                            {{ $user->initials ?? substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <button type="button" class="px-3 py-1.5 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition">Cambiar foto</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nombre Completo</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Correo Electrónico</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm transition">Guardar Cambios</button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- TAB 2: Ajustes de Cuenta --}}
        @if($tab === 'settings')
        <div class="space-y-6">
            {{-- Integración Google Calendar --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="p-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg">
                                <svg width="20" height="20" viewBox="0 0 48 48" fill="none"><path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.63v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.16z"/><path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/><path fill="#FBBC05" d="M11.69 28.18c-.44-1.32-.69-2.72-.69-4.18s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24c0 3.55.85 6.91 2.34 9.88l7.35-5.7z"/><path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/></svg>
                            </span>
                            Integración con Google Calendar
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Sincroniza tus reuniones externas y de clientes automáticamente.</p>
                    </div>
                </div>

                <div class="mt-6 p-4 rounded-xl border {{ $googleIntegration ? 'bg-success-50 border-success-200 dark:bg-success-500/5 dark:border-success-500/20' : 'bg-gray-50 border-gray-200 dark:bg-gray-800/50 dark:border-gray-800' }}">
                    @if($googleIntegration)
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-success-800 dark:text-success-400 flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full bg-success-500 shadow-sm"></span> Conectado
                                </span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sincronizando a: <strong>{{ $googleIntegration->email }}</strong></span>
                            </div>
                            <form action="{{ route('google.calendar.disconnect') }}" method="POST">
                                @csrf
                                <button type="submit" onclick="return confirm('¿Seguro que deseas desconectar tu calendario?')" class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg shadow-sm hover:bg-red-50 hover:text-red-700 transition dark:bg-gray-800 dark:border-red-500/30 dark:hover:bg-red-500/10 dark:hover:text-red-400">
                                    Desconectar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Estado: No conectado</span>
                            </div>
                            <a href="{{ route('google.calendar.connect') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium bg-white text-gray-700 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 hover:text-gray-900 transition dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-700">
                                <svg width="18" height="18" viewBox="0 0 48 48" fill="none"><path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.63v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.16z"/><path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/><path fill="#FBBC05" d="M11.69 28.18c-.44-1.32-.69-2.72-.69-4.18s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24c0 3.55.85 6.91 2.34 9.88l7.35-5.7z"/><path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/></svg>
                                Conectar con Google
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Cambio de Contraseña --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5 border-b border-gray-100 dark:border-gray-800 pb-3">Seguridad</h2>
                
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4 max-w-lg">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña Actual</label>
                            <input type="password" name="current_password" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Nueva Contraseña</label>
                            <input type="password" name="new_password" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            @error('new_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Confirmar Nueva Contraseña</label>
                            <input type="password" name="new_password_confirmation" required
                                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                        </div>
                        
                        <div class="pt-4">
                            <button type="submit" class="px-5 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm transition">Actualizar Contraseña</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- TAB 3: Soporte --}}
        @if($tab === 'support')
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5 border-b border-gray-100 dark:border-gray-800 pb-3">Centro de Soporte</h2>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Si estás experimentando problemas técnicos o tienes alguna pregunta, envíanos un mensaje y te contactaremos a la brevedad.</p>

            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Solicitud de soporte enviada con éxito (Simulación)');">
                <div class="space-y-4 max-w-lg">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Asunto</label>
                        <select required class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none">
                            <option value="">Selecciona el motivo...</option>
                            <option value="bug">Reportar un error del sistema</option>
                            <option value="feature">Sugerir nueva funcionalidad</option>
                            <option value="help">Problemas con mi cuenta o acceso</option>
                            <option value="other">Otro / Consulta general</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Detalle del problema</label>
                        <textarea required rows="5" placeholder="Describe como podemos ayudarte..."
                                  class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-brand-500/20 focus:outline-none resize-none"></textarea>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="px-5 py-2.5 text-sm font-medium bg-brand-500 text-white rounded-lg hover:bg-brand-600 shadow-sm transition flex items-center gap-2">
                            Enviar Solicitud
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/></svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
