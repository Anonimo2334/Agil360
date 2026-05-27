@extends('layouts.app')

@php
    $checkboxGroups = [
        'funciones_agentes' => [
            'title' => 'Funciones de los agentes',
            'options' => [
                'atencion_inicial' => 'Atencion inicial',
                'ventas' => 'Ventas',
                'agendamiento_reservaciones' => 'Agendamiento / Reservaciones',
                'seguimiento' => 'Seguimiento',
                'informativo' => 'Informativo',
                'soporte_cliente' => 'Soporte al cliente',
            ],
        ],
        'canales_activos' => [
            'title' => 'Canales activos',
            'options' => [
                'instagram' => 'Instagram',
                'facebook' => 'Facebook',
                'messenger' => 'Messenger',
                'whatsapp_directo' => 'WhatsApp directo',
                'tiktok' => 'TikTok',
                'pagina_web' => 'Pagina web',
                'google_ads' => 'Google Ads',
            ],
        ],
        'implementaciones' => [
            'title' => 'Que desea implementar',
            'options' => [
                'pagina_web' => 'Pagina web',
                'landing_page' => 'Landing page',
                'formularios_ventas' => 'Formularios de ventas',
                'email_marketing' => 'Email marketing',
                'resenas_google' => 'Resenas de Google',
                'tienda_inventario' => 'Tienda / Inventario',
            ],
        ],
        'etiquetas_sistema' => [
            'title' => 'Etiquetas del sistema',
            'options' => [
                'lead_caliente' => 'Lead caliente',
                'lead_frio' => 'Lead frio',
                'interesado_producto_x' => 'Interesado en producto X',
                'cliente_recurrente' => 'Cliente recurrente',
                'cliente_vip' => 'Cliente VIP',
                'personalizadas' => 'Personalizadas',
            ],
        ],
        'seguimiento_clientes' => [
            'title' => 'Seguimiento de clientes',
            'options' => [
                'confirmacion_cita' => 'Confirmacion de cita',
                'recordatorio_cita' => 'Recordatorio de cita',
                'seguimiento_despues_cita' => 'Seguimiento despues de cita',
                'encuesta_post_servicio' => 'Encuesta post-servicio',
                'seguimiento_post_venta' => 'Seguimiento post-venta',
            ],
        ],
        'automatizaciones_internas' => [
            'title' => 'Automatizaciones internas',
            'options' => [
                'asignacion_automatica_usuarios' => 'Asignacion automatica de usuarios',
                'etiquetado_automatico_interes' => 'Etiquetado automatico por interes',
                'mensajes_automaticos_etapa' => 'Mensajes automaticos por etapa',
            ],
        ],
        'bots_adicionales' => [
            'title' => 'Bots adicionales',
            'options' => [
                'bot_cumpleanos' => 'Bot de cumpleanos',
                'bot_boletines' => 'Bot de boletines',
                'bot_recordatorio_pago' => 'Bot de recordatorio de pago',
            ],
        ],
    ];

    $personalidades = ['Formal', 'Profesional cercano', 'Jovial / Dominicanizado', 'Tecnico'];
    $autonomias = [
        'Solo responder preguntas',
        'Calificar / etiquetar leads',
        'Agendar citas directamente',
        'Proporcionar detalles de productos (precios)',
        'Transferir a humano',
    ];
    $basesDatos = ['Celular', 'CRM', 'Excel', 'Documento', 'No tiene'];
@endphp

@section('content')
<div x-data="requirementsForm()" class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-brand-500">Levantamiento de implementacion</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Requerimientos para Agentes IA, CRM y Automatizaciones</h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-500 dark:text-gray-400">
                Formulario unico para documentar estrategia de agentes, canales, marketing, CRM, automatizaciones e infraestructura tecnica.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="expandAll()"
                class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                Expandir todo
            </button>
            <button type="button" @click="collapseAll()"
                class="rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800">
                Contraer todo
            </button>
        </div>
    </div>

    <form x-ref="form" @submit.prevent="buildSummary()" class="grid grid-cols-12 gap-5">
        <div class="col-span-12 xl:col-span-8 space-y-5">
            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">1. Agentes IA</summary>
                <div class="mt-5 space-y-6 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'funciones_agentes', 'group' => $checkboxGroups['funciones_agentes']])

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Personalidad del agente <span class="text-error-500">*</span></label>
                            <div class="space-y-2">
                                @foreach ($personalidades as $option)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        <input type="radio" name="personalidad_agente" value="{{ $option }}" required data-summary-label="Personalidad del agente" class="size-4 text-brand-500">
                                        {{ $option }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Nivel de autonomia <span class="text-error-500">*</span></label>
                            <div class="space-y-2">
                                @foreach ($autonomias as $option)
                                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                        <input type="radio" name="nivel_autonomia" value="{{ $option }}" required data-summary-label="Nivel de autonomia" class="size-4 text-brand-500">
                                        {{ $option }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="md:col-span-2">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Nombre de los agentes</label>
                            <input type="text" name="nombre_agentes" data-summary-label="Nombre de los agentes" placeholder="Ej: Ana, Max, Asistente de ventas"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Cantidad de agentes <span class="text-error-500">*</span></label>
                            <input type="number" min="1" name="cantidad_agentes" required data-summary-label="Cantidad de agentes" placeholder="1"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Horario de funcionamiento</label>
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                            @foreach (['24/7', 'Horario laboral'] as $option)
                                <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                    <input type="radio" name="horario_funcionamiento" value="{{ $option }}" data-summary-label="Horario de funcionamiento" class="size-4 text-brand-500">
                                    {{ $option }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">2. Canales y captacion</summary>
                <div class="mt-5 space-y-6 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'canales_activos', 'group' => $checkboxGroups['canales_activos']])
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Cantidad de WhatsApp de atencion</label>
                            <input type="number" min="0" name="cantidad_whatsapp_atencion" data-summary-label="Cantidad de WhatsApp de atencion"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Meta/portafolio configurado</label>
                            <select name="meta_portafolio_configurado" data-summary-label="Meta/portafolio configurado" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="">Seleccionar</option>
                                <option>Si</option>
                                <option>No</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Dominio vigente</label>
                            <select name="dominio_vigente" data-summary-label="Dominio vigente" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="">Seleccionar</option>
                                <option>Si</option>
                                <option>No</option>
                            </select>
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Dominio</label>
                            <input type="text" name="dominio" data-summary-label="Dominio" placeholder="empresa.com"
                                class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        </div>
                    </div>
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">3. Servicios de marketing y desarrollo</summary>
                <div class="mt-5 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'implementaciones', 'group' => $checkboxGroups['implementaciones']])
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">4. CRM y pipeline</summary>
                <div class="mt-5 space-y-6 border-t border-gray-100 pt-5 dark:border-gray-800">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tiene CRM actual</label>
                            <select name="tiene_crm_actual" data-summary-label="Tiene CRM actual" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="">Seleccionar</option>
                                <option>Si</option>
                                <option>No</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Tipo de base de datos</label>
                            <select name="tipo_base_datos" data-summary-label="Tipo de base de datos" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="">Seleccionar</option>
                                @foreach ($basesDatos as $option)
                                    <option>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Pipeline actual</label>
                        <textarea name="pipeline_actual" rows="3" data-summary-label="Pipeline actual" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Nuevo pipeline (6 etapas)</label>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @for ($stage = 1; $stage <= 6; $stage++)
                                <input type="text" name="nuevo_pipeline_etapa_{{ $stage }}" data-summary-label="Pipeline etapa {{ $stage }}" placeholder="Etapa {{ $stage }}"
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            @endfor
                        </div>
                    </div>
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'etiquetas_sistema', 'group' => $checkboxGroups['etiquetas_sistema']])
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">5. Usuarios</summary>
                <div class="mt-5 space-y-4 border-t border-gray-100 pt-5 dark:border-gray-800">
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Cantidad de usuarios</label>
                        <input type="number" min="0" name="cantidad_usuarios" data-summary-label="Cantidad de usuarios" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    </div>
                    <template x-for="(user, index) in users" :key="index">
                        <div class="grid grid-cols-1 gap-3 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800 md:grid-cols-[1fr_1fr_auto]">
                            <input type="text" :name="`usuarios[${index}][nombre]`" x-model="user.name" data-summary-label="Usuario" placeholder="Nombre"
                                class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <input type="text" :name="`usuarios[${index}][rol]`" x-model="user.role" data-summary-label="Rol de usuario" placeholder="Rol"
                                class="w-full rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                            <button type="button" @click="removeUser(index)" class="rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-600 hover:bg-white dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-900">Quitar</button>
                        </div>
                    </template>
                    <button type="button" @click="addUser()" class="rounded-lg bg-brand-50 px-4 py-2.5 text-sm font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400">Agregar usuario</button>
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">6. Automatizaciones</summary>
                <div class="mt-5 space-y-6 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'seguimiento_clientes', 'group' => $checkboxGroups['seguimiento_clientes']])
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'automatizaciones_internas', 'group' => $checkboxGroups['automatizaciones_internas']])
                    @include('pages.agil365.partials.requirements-checkbox-group', ['groupKey' => 'bots_adicionales', 'group' => $checkboxGroups['bots_adicionales']])
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">7. Contenido y soporte</summary>
                <div class="mt-5 grid grid-cols-1 gap-4 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @foreach ([
                        'preguntas_frecuentes' => 'Preguntas frecuentes',
                        'politicas_negocio' => 'Politicas del negocio',
                        'informacion_servicios' => 'Informacion de servicios',
                        'catalogo_productos_servicios' => 'Catalogo de productos o servicios',
                        'ejemplo_conversacion_cliente' => 'Ejemplo de conversacion con cliente',
                    ] as $name => $label)
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                            <textarea name="{{ $name }}" rows="4" data-summary-label="{{ $label }}" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                        </div>
                    @endforeach
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">8. Accesos y configuracion</summary>
                <div class="mt-5 grid grid-cols-1 gap-4 border-t border-gray-100 pt-5 dark:border-gray-800 md:grid-cols-2">
                    @foreach ([
                        'acceso_redes_sociales' => 'Acceso a redes sociales',
                        'acceso_dominio' => 'Acceso a dominio',
                    ] as $name => $label)
                        <div class="space-y-2">
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                            <select name="{{ $name }}" data-summary-label="{{ $label }}" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                <option value="">Seleccionar</option>
                                <option>Si</option>
                                <option>No</option>
                            </select>
                            <textarea name="{{ $name }}_detalle" rows="2" data-summary-label="Detalle {{ $label }}" placeholder="Detalle" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                        </div>
                    @endforeach
                    <div class="md:col-span-2">
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Cuentas de WhatsApp</label>
                        <textarea name="cuentas_whatsapp" rows="3" data-summary-label="Cuentas de WhatsApp" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Correos de usuarios para plataforma</label>
                        <textarea name="correos_usuarios_plataforma" rows="3" data-summary-label="Correos de usuarios para plataforma" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">Base de datos de contactos</label>
                        <input type="file" name="base_datos_contactos_archivo" data-summary-label="Archivo de base de datos" class="mb-2 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                        <textarea name="base_datos_contactos_descripcion" rows="2" data-summary-label="Descripcion de base de datos" placeholder="Descripcion si no hay archivo" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                    </div>
                </div>
            </details>

            <details open data-requirements-section class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <summary class="cursor-pointer list-none text-sm font-semibold text-gray-900 dark:text-white">9. Recursos necesarios</summary>
                <div class="mt-5 grid grid-cols-1 gap-4 border-t border-gray-100 pt-5 dark:border-gray-800">
                    @foreach ([
                        'plantillas_mensajes_actuales' => 'Plantillas de mensajes actuales',
                        'automatizaciones_personalizadas' => 'Automatizaciones personalizadas requeridas',
                        'disponibilidad_horarios_calendarios' => 'Disponibilidad de horarios para calendarios',
                    ] as $name => $label)
                        <div>
                            <label class="mb-1.5 block text-xs font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
                            <textarea name="{{ $name }}" rows="3" data-summary-label="{{ $label }}" class="w-full resize-none rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300"></textarea>
                        </div>
                    @endforeach
                </div>
            </details>
        </div>

        <div class="col-span-12 xl:col-span-4">
            <div class="sticky top-24 space-y-4">
                <div class="rounded-2xl border border-brand-200 bg-brand-50 p-5 dark:border-brand-500/20 dark:bg-brand-500/10">
                    <h2 class="text-sm font-semibold text-brand-700 dark:text-brand-400">Campos criticos</h2>
                    <p class="mt-2 text-xs leading-5 text-brand-600 dark:text-brand-300">
                        Completa personalidad, autonomia y cantidad de agentes para poder generar un resumen base confiable.
                    </p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Acciones</h2>
                    <div class="mt-4 space-y-3">
                        <button type="submit" class="w-full rounded-xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Generar resumen</button>
                        <button type="button" @click="copySummary()" :disabled="!summary" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-gray-700 disabled:opacity-50 dark:border-gray-700 dark:text-gray-300">Copiar resumen</button>
                    </div>
                    <p class="mt-3 text-center text-xs text-gray-400">El formulario no guarda datos en servidor; genera un resumen en esta pagina.</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Resumen final</h2>
                    <pre x-text="summary || 'Completa el formulario y presiona Generar resumen.'" class="mt-4 max-h-[520px] overflow-auto whitespace-pre-wrap rounded-xl bg-gray-50 p-4 text-xs leading-5 text-gray-600 dark:bg-gray-800 dark:text-gray-300"></pre>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function requirementsForm() {
    return {
        users: [{ name: '', role: '' }],
        summary: '',
        addUser() {
            this.users.push({ name: '', role: '' });
        },
        removeUser(index) {
            if (this.users.length === 1) {
                this.users = [{ name: '', role: '' }];
                return;
            }
            this.users.splice(index, 1);
        },
        expandAll() {
            this.$root.querySelectorAll('[data-requirements-section]').forEach((section) => section.open = true);
        },
        collapseAll() {
            this.$root.querySelectorAll('[data-requirements-section]').forEach((section) => section.open = false);
        },
        buildSummary() {
            if (!this.$refs.form.reportValidity()) {
                return;
            }

            const lines = ['Resumen de requerimientos'];
            const sections = this.$refs.form.querySelectorAll('[data-requirements-section]');

            sections.forEach((section) => {
                const title = section.querySelector('summary')?.textContent?.trim();
                const sectionLines = [];
                const controls = section.querySelectorAll('[data-summary-label]');

                controls.forEach((control) => {
                    const label = control.dataset.summaryLabel;
                    let value = '';

                    if (control.type === 'checkbox') {
                        if (!control.checked) return;
                        value = control.value || 'Seleccionado';
                        const comment = control.closest('[data-checkbox-option]')?.querySelector('[data-comment-field]')?.value?.trim();
                        if (comment) value += ' - ' + comment;
                    } else if (control.type === 'radio') {
                        if (!control.checked) return;
                        value = control.value;
                    } else if (control.type === 'file') {
                        value = Array.from(control.files || []).map((file) => file.name).join(', ');
                    } else {
                        value = control.value?.trim();
                    }

                    if (value) {
                        sectionLines.push(label + ': ' + value);
                    }
                });

                if (sectionLines.length) {
                    lines.push('', title, ...sectionLines.map((item) => '- ' + item));
                }
            });

            this.summary = lines.join('\n');
        },
        copySummary() {
            if (!this.summary || !navigator.clipboard) return;
            navigator.clipboard.writeText(this.summary);
        },
    };
}
</script>
@endpush
@endsection
