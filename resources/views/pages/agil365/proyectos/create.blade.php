@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Nuevo Proyecto</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Completa la información del nuevo proyecto</p>
    </div>
    <a href="{{ route('proyectos') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
        <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Volver a proyectos
    </a>
</div>

{{-- Validation errors --}}
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

<form action="{{ route('proyectos.store') }}" method="POST" class="grid grid-cols-12 gap-5">
    @csrf

    {{-- ── Main column ── --}}
    <div class="col-span-12 xl:col-span-8 space-y-5">

        {{-- Basic Info --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5 pb-3 border-b border-gray-100 dark:border-gray-800">Información Básica</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nombre del Proyecto *</label>
                        <input type="text" name="project_name" value="{{ old('project_name') }}"
                            placeholder="Ej: Bot WhatsApp Business v2"
                            class="w-full px-4 py-2.5 text-sm border {{ $errors->has('project_name') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-300">
                        @error('project_name')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Cliente / Empresa *</label>
                        <div class="relative" id="company-autocomplete">
                            <input type="text" id="company-search-input" placeholder="Escribe para buscar o crear cliente..."
                                autocomplete="off"
                                class="w-full px-4 py-2.5 text-sm border {{ $errors->has('company_id') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <input type="hidden" name="company_id" id="company-id-hidden" value="{{ old('company_id') }}" required>
                            <div id="company-dropdown" class="absolute top-full left-0 right-0 z-50 mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg hidden max-h-52 overflow-y-auto">
                            </div>
                        </div>
                        @error('company_id')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">CEO / Contacto Principal</label>
                        <input type="text" name="ceo" value="{{ old('ceo') }}"
                            placeholder="Nombre del CEO o contacto"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plataforma</label>
                        <input type="text" name="platform" value="{{ old('platform', 'Agil365') }}"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bot Asociado</label>
                        <input type="text" name="bot_name" value="{{ old('bot_name') }}"
                            placeholder="Nombre del bot"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">URL del Sitio Web</label>
                        <input type="url" name="website_url" value="{{ old('website_url') }}"
                            placeholder="https://ejemplo.com"
                            class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        @error('website_url')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Servidor / Hosting</label>
                    <input type="text" name="server_hosting" value="{{ old('server_hosting') }}"
                        placeholder="Ej: AWS EC2 - t3.medium"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                </div>
            </div>
        </div>

        {{-- Dates & Status --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5 pb-3 border-b border-gray-100 dark:border-gray-800">Fechas y Estado</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha de Inicio</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    @error('start_date')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Fecha Límite</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                        class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                    @error('end_date')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Estado Inicial *</label>
                    <select name="status" class="w-full px-4 py-2.5 text-sm border {{ $errors->has('status') ? 'border-error-400' : 'border-gray-200' }} rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="iniciado"   {{ old('status', 'iniciado') === 'iniciado'   ? 'selected' : '' }}>Iniciado</option>
                        <option value="en_proceso" {{ old('status') === 'en_proceso' ? 'selected' : '' }}>En proceso</option>
                        <option value="soporte"    {{ old('status') === 'soporte'    ? 'selected' : '' }}>Soporte</option>
                        <option value="completado" {{ old('status') === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado"  {{ old('status') === 'cancelado'  ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('status')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Progreso Inicial: <span id="progressLabel" class="text-brand-500 font-bold">0%</span>
                </label>
                <div class="flex items-center gap-4">
                    <input type="range" name="progress_percentage" min="0" max="100" value="{{ old('progress_percentage', 0) }}"
                        id="progressRange"
                        class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-brand-500"
                        oninput="document.getElementById('progressLabel').textContent = this.value + '%'">
                    <input type="number" id="progressNumber" min="0" max="100" value="{{ old('progress_percentage', 0) }}"
                        class="w-16 px-2 py-1.5 text-sm text-center border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none"
                        oninput="document.getElementById('progressRange').value=this.value; document.getElementById('progressLabel').textContent=this.value+'%'">
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-5 pb-3 border-b border-gray-100 dark:border-gray-800">Notas Internas</h3>
            <textarea name="notes" rows="4"
                placeholder="Agrega notas internas del proyecto (información del cliente, requerimientos, observaciones)..."
                class="w-full px-4 py-3 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20 resize-none">{{ old('notes') }}</textarea>
        </div>
    </div>

    {{-- ── Sidebar ── --}}
    <div class="col-span-12 xl:col-span-4 space-y-5">

        {{-- Team Assignment --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Asignación de Equipo</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero Principal</label>
                    <select name="primary_engineer_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Seleccionar...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ old('primary_engineer_id') == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }}
                                @if($eng->role) · {{ $eng->role->name }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('primary_engineer_id')<p class="text-xs text-error-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ingeniero Backup</label>
                    <select name="backup_engineer_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                        <option value="">Seleccionar...</option>
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}" {{ old('backup_engineer_id') == $eng->id ? 'selected' : '' }}>
                                {{ $eng->name }}
                                @if($eng->role) · {{ $eng->role->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Live Preview --}}
        <div class="rounded-2xl border border-brand-200 bg-brand-50 p-5 dark:border-brand-500/20 dark:bg-brand-500/10">
            <h3 class="text-sm font-semibold text-brand-700 dark:text-brand-400 mb-3">Vista previa del proyecto</h3>
            <div class="space-y-2 text-xs text-brand-600 dark:text-brand-500">
                <div class="flex items-center justify-between">
                    <span>Estado:</span>
                    <span class="font-medium" id="previewStatus">Iniciado</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Progreso:</span>
                    <span class="font-medium" id="previewProgress">0%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Alerta de riesgo:</span>
                    <span class="font-medium text-success-600">Ninguna</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="space-y-3">
            <button type="submit" id="submitBtn" class="w-full py-3 text-sm font-semibold bg-brand-500 text-white rounded-xl hover:bg-brand-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Crear Proyecto
            </button>
            <a href="{{ route('proyectos') }}" class="block w-full py-3 text-sm font-medium text-center text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
                Cancelar
            </a>
        </div>

        {{-- Required hint --}}
        <p class="text-xs text-gray-400 text-center">Los campos marcados con <span class="text-error-500">*</span> son obligatorios</p>
    </div>
</form>

@push('scripts')
<script>
// Sync range ↔ number inputs for progress
document.getElementById('progressRange').addEventListener('input', function() {
    document.getElementById('progressNumber').value   = this.value;
    document.getElementById('progressLabel').textContent = this.value + '%';
    document.getElementById('previewProgress').textContent  = this.value + '%';
});
document.getElementById('progressNumber').addEventListener('input', function() {
    const v = Math.min(100, Math.max(0, this.value || 0));
    document.getElementById('progressRange').value    = v;
    document.getElementById('progressLabel').textContent = v + '%';
    document.getElementById('previewProgress').textContent  = v + '%';
});

// Live status preview
document.querySelector('select[name="status"]').addEventListener('change', function() {
    const labels = {
        iniciado: 'Iniciado', en_proceso: 'En proceso',
        soporte: 'Soporte', completado: 'Completado', cancelado: 'Cancelado'
    };
    document.getElementById('previewStatus').textContent = labels[this.value] || this.value;
});

// Prevent double-submit
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Guardando...';
});

// ─── Company Autocomplete ─────────────────────────────────────────────────────
const companyInput   = document.getElementById('company-search-input');
const companyHidden  = document.getElementById('company-id-hidden');
const companyDropdown = document.getElementById('company-dropdown');
let companyTimeout;

// Pre-fill if old value exists
@if(old('company_id'))
(function() {
    fetch('/api/clientes/buscar?q=', {headers: {'Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'}})
    .then(r=>r.json())
    .then(items => {
        const found = items.find(i => i.id == {{ old('company_id', 0) }});
        if (found) companyInput.value = found.name;
    });
})();
@endif

companyInput.addEventListener('input', function() {
    clearTimeout(companyTimeout);
    const q = this.value.trim();
    companyHidden.value = ''; // Clear hidden until user selects
    if (q.length < 1) { companyDropdown.classList.add('hidden'); return; }
    companyTimeout = setTimeout(() => {
        fetch(`/api/clientes/buscar?q=${encodeURIComponent(q)}`, {
            headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        })
        .then(r => r.json())
        .then(items => {
            let html = items.map(item => `
                <div class="px-3 py-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-sm text-gray-800 dark:text-gray-200 border-b border-gray-100 dark:border-gray-800 last:border-0"
                     onclick="selectCompany(${item.id}, '${item.name.replace(/'/g, "\\'")}')">🏢 ${item.name}</div>
            `).join('');
            // Add quick-create option
            html += `
                <div class="px-3 py-2.5 hover:bg-brand-50 dark:hover:bg-brand-500/10 cursor-pointer text-sm text-brand-600 border-t border-dashed border-gray-200 dark:border-gray-700 dark:text-brand-400 font-medium flex items-center gap-2"
                     onclick="quickCreateCompany('${q.replace(/'/g, "\\'")}')">➕ Agregar a "${q}"</div>
            `;
            companyDropdown.innerHTML = html;
            companyDropdown.classList.remove('hidden');
        });
    }, 200);
});

function selectCompany(id, name) {
    companyHidden.value = id;
    companyInput.value  = name;
    companyDropdown.classList.add('hidden');
}

function quickCreateCompany(name) {
    fetch('/api/clientes/rapido', {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({name: name})
    })
    .then(r => r.json())
    .then(data => {
        if (data.id) {
            selectCompany(data.id, data.name);
            // Show a small feedback
            const feedback = document.createElement('p');
            feedback.textContent = '✓ Cliente "' + data.name + '" guardado.';
            feedback.className = 'text-xs text-success-500 mt-1';
            document.getElementById('company-autocomplete').appendChild(feedback);
            setTimeout(() => feedback.remove(), 3000);
        }
    })
    .catch(() => alert('Error al crear cliente.'));
    companyDropdown.classList.add('hidden');
}

document.addEventListener('click', function(e) {
    if (!companyInput.contains(e.target) && !companyDropdown.contains(e.target)) {
        companyDropdown.classList.add('hidden');
    }
});
</script>
@endpush

@endsection
