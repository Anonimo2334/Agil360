<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use App\Models\Meeting;
use App\Models\PendingItem;
use App\Models\Alert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────────────────────────
        $roles = [
            ['name' => 'Super Administrador', 'slug' => 'super_admin',  'description' => 'Acceso total al sistema', 'permissions' => []],
            ['name' => 'Administrador',       'slug' => 'admin',        'description' => 'Gestión del sistema',    'permissions' => ['clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar', 'proyectos.ver', 'proyectos.crear', 'proyectos.editar', 'proyectos.eliminar', 'tareas.ver', 'tareas.crear', 'tareas.editar', 'tareas.eliminar', 'usuarios.gestionar', 'roles.gestionar', 'reportes.ver']],
            ['name' => 'Gerente',             'slug' => 'gerente',      'description' => 'Gerencia operativa',     'permissions' => ['clientes.ver', 'proyectos.ver', 'proyectos.crear', 'proyectos.editar', 'tareas.ver', 'tareas.crear', 'tareas.editar', 'reportes.ver']],
            ['name' => 'Ingeniero',           'slug' => 'ingeniero',    'description' => 'Desarrollador / Técnico','permissions' => ['proyectos.ver', 'tareas.ver', 'tareas.crear', 'tareas.editar']],
            ['name' => 'Soporte',             'slug' => 'soporte',      'description' => 'Soporte técnico',        'permissions' => ['clientes.ver', 'proyectos.ver', 'tareas.ver', 'tareas.crear', 'tareas.editar']],
            ['name' => 'Visualizador',        'slug' => 'visualizador', 'description' => 'Solo lectura',           'permissions' => ['clientes.ver', 'proyectos.ver', 'tareas.ver', 'reportes.ver']],
            ['name' => 'Contabilidad',        'slug' => 'contabilidad', 'description' => 'Gestión de bonos y finanzas', 'permissions' => ['reportes.ver']],
        ];
        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }

        $adminRole       = Role::where('slug', 'admin')->first();
        $ingRole         = Role::where('slug', 'ingeniero')->first();
        $soporteRole     = Role::where('slug', 'soporte')->first();
        $superRole       = Role::where('slug', 'super_admin')->first();
        $contabilidadRole = Role::where('slug', 'contabilidad')->first();

        // ── Users ─────────────────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@agil365.com'], [
            'name'       => 'Admin Agil365',
            'password'   => Hash::make('password'),
            'role_id'    => $superRole->id,
            'department' => 'Administración',
            'is_active'  => true,
        ]);

        // Default accounting user
        User::firstOrCreate(['email' => 'contabilidad@agil365.com'], [
            'name'       => 'Contabilidad',
            'password'   => Hash::make('password'),
            'role_id'    => $contabilidadRole->id,
            'department' => 'Contabilidad',
            'is_active'  => true,
        ]);

        $engineers = [
            ['name' => 'Ana López',    'email' => 'ana@agil365.com',     'department' => 'Desarrollo'],
            ['name' => 'Carlos Ruiz',  'email' => 'carlos@agil365.com',  'department' => 'Desarrollo'],
            ['name' => 'María Torres', 'email' => 'maria@agil365.com',   'department' => 'Desarrollo'],
            ['name' => 'Luis García',  'email' => 'luis@agil365.com',    'department' => 'Desarrollo'],
            ['name' => 'Sara Méndez',  'email' => 'sara@agil365.com',    'department' => 'Soporte'],
            ['name' => 'Pedro Vega',   'email' => 'pedro@agil365.com',   'department' => 'Desarrollo'],
        ];

        $engineerUsers = [];
        foreach ($engineers as $eng) {
            $roleId = $eng['department'] === 'Soporte' ? $soporteRole->id : $ingRole->id;
            $engineerUsers[] = User::firstOrCreate(['email' => $eng['email']], [
                'name'       => $eng['name'],
                'password'   => Hash::make('password'),
                'role_id'    => $roleId,
                'department' => $eng['department'],
                'is_active'  => true,
            ]);
        }

        // ── Companies ─────────────────────────────────────────────────────────
        $companiesData = [
            ['name' => 'Nexo Corp',      'contact_name' => 'Roberto Nexo',   'email' => 'info@nexocorp.com',    'country' => 'México'],
            ['name' => 'TechVision SA',  'contact_name' => 'Diana Visión',   'email' => 'info@techvision.com',  'country' => 'Colombia'],
            ['name' => 'Innova Digital', 'contact_name' => 'Jorge Innovate', 'email' => 'info@innovadig.com',   'country' => 'Argentina'],
            ['name' => 'StartUp Pro',    'contact_name' => 'Laura Startup',  'email' => 'info@startuppro.com',  'country' => 'Chile'],
            ['name' => 'MegaLogistic',   'contact_name' => 'Marcos Logíst',  'email' => 'info@megalog.com',     'country' => 'Perú'],
            ['name' => 'FinTech Group',  'contact_name' => 'Natalia Fintech','email' => 'info@fintech.com',     'country' => 'Brasil'],
        ];

        $companies = [];
        foreach ($companiesData as $comp) {
            $companies[] = Company::firstOrCreate(['name' => $comp['name']], $comp);
        }

        // ── Projects ──────────────────────────────────────────────────────────
        $projectsData = [
            [
                'company_id'          => $companies[0]->id,
                'project_name'        => 'Bot WhatsApp Business',
                'ceo'                 => 'Roberto Nexo',
                'primary_engineer_id' => $engineerUsers[0]->id,
                'backup_engineer_id'  => $engineerUsers[1]->id,
                'start_date'          => '2025-01-15',
                'end_date'            => '2025-04-15',
                'progress_percentage' => 78,
                'status'              => 'en_proceso',
                'platform'            => 'Agil365',
                'bot_name'            => 'NexoBot',
                'is_at_risk'          => false,
            ],
            [
                'company_id'          => $companies[1]->id,
                'project_name'        => 'Automatización CRM',
                'ceo'                 => 'Diana Visión',
                'primary_engineer_id' => $engineerUsers[1]->id,
                'backup_engineer_id'  => $engineerUsers[2]->id,
                'start_date'          => '2025-01-01',
                'end_date'            => '2025-03-28',
                'progress_percentage' => 42,
                'status'              => 'en_proceso',
                'platform'            => 'Agil365',
                'is_at_risk'          => true,
            ],
            [
                'company_id'          => $companies[2]->id,
                'project_name'        => 'Plataforma E-Commerce',
                'ceo'                 => 'Jorge Innovate',
                'primary_engineer_id' => $engineerUsers[2]->id,
                'backup_engineer_id'  => $engineerUsers[0]->id,
                'start_date'          => '2024-11-01',
                'end_date'            => '2025-05-10',
                'progress_percentage' => 91,
                'status'              => 'soporte',
                'platform'            => 'Agil365',
                'is_at_risk'          => false,
            ],
            [
                'company_id'          => $companies[3]->id,
                'project_name'        => 'TechBot Pro',
                'ceo'                 => 'Laura Startup',
                'primary_engineer_id' => $engineerUsers[3]->id,
                'backup_engineer_id'  => $engineerUsers[4]->id,
                'start_date'          => '2025-01-10',
                'end_date'            => '2025-03-20',
                'progress_percentage' => 31,
                'status'              => 'en_proceso',
                'platform'            => 'Agil365',
                'is_at_risk'          => true,
            ],
            [
                'company_id'          => $companies[4]->id,
                'project_name'        => 'Sistema de Tracking',
                'ceo'                 => 'Marcos Logíst',
                'primary_engineer_id' => $engineerUsers[4]->id,
                'backup_engineer_id'  => $engineerUsers[5]->id,
                'start_date'          => '2025-02-01',
                'end_date'            => '2025-06-05',
                'progress_percentage' => 65,
                'status'              => 'en_proceso',
                'platform'            => 'Agil365',
                'is_at_risk'          => false,
            ],
            [
                'company_id'          => $companies[5]->id,
                'project_name'        => 'Dashboard Analytics',
                'ceo'                 => 'Natalia Fintech',
                'primary_engineer_id' => $engineerUsers[5]->id,
                'backup_engineer_id'  => $engineerUsers[0]->id,
                'start_date'          => '2024-12-01',
                'end_date'            => '2025-03-01',
                'progress_percentage' => 100,
                'status'              => 'completado',
                'platform'            => 'Agil365',
                'is_at_risk'          => false,
            ],
        ];

        $projects = [];
        foreach ($projectsData as $proj) {
            $projects[] = Project::firstOrCreate(
                ['project_name' => $proj['project_name'], 'company_id' => $proj['company_id']],
                $proj
            );
        }

        // ── Tasks ─────────────────────────────────────────────────────────────
        $tasksData = [
            ['project_id' => $projects[0]->id, 'title' => 'Integración API WhatsApp',        'assigned_engineer_id' => $engineerUsers[0]->id, 'priority' => 'alta',   'status' => 'en_progreso', 'progress' => 80],
            ['project_id' => $projects[0]->id, 'title' => 'Panel de mensajes',               'assigned_engineer_id' => $engineerUsers[1]->id, 'priority' => 'media',  'status' => 'en_progreso', 'progress' => 70],
            ['project_id' => $projects[1]->id, 'title' => 'Sincronización de contactos',     'assigned_engineer_id' => $engineerUsers[1]->id, 'priority' => 'critica','status' => 'bloqueada',   'progress' => 30],
            ['project_id' => $projects[1]->id, 'title' => 'Dashboard de reportes CRM',       'assigned_engineer_id' => $engineerUsers[1]->id, 'priority' => 'alta',   'status' => 'pendiente',   'progress' => 0],
            ['project_id' => $projects[2]->id, 'title' => 'Módulo de pagos',                 'assigned_engineer_id' => $engineerUsers[2]->id, 'priority' => 'alta',   'status' => 'completada',  'progress' => 100],
            ['project_id' => $projects[3]->id, 'title' => 'Entrenamiento del modelo NLP',    'assigned_engineer_id' => $engineerUsers[3]->id, 'priority' => 'critica','status' => 'en_progreso', 'progress' => 25],
            ['project_id' => $projects[4]->id, 'title' => 'GPS en tiempo real',              'assigned_engineer_id' => $engineerUsers[4]->id, 'priority' => 'alta',   'status' => 'en_progreso', 'progress' => 60],
            ['project_id' => $projects[5]->id, 'title' => 'Gráficas de conversión',          'assigned_engineer_id' => $engineerUsers[5]->id, 'priority' => 'media',  'status' => 'completada',  'progress' => 100],
        ];

        foreach ($tasksData as $task) {
            Task::firstOrCreate(['project_id' => $task['project_id'], 'title' => $task['title']], $task);
        }

        // ── Alerts ────────────────────────────────────────────────────────────
        Alert::firstOrCreate(
            ['project_id' => $projects[1]->id, 'type' => 'riesgo', 'status' => 'activa'],
            ['message' => 'Automatización CRM: <50% avance con <30% tiempo restante.', 'severity' => 'error']
        );
        Alert::firstOrCreate(
            ['project_id' => $projects[3]->id, 'type' => 'riesgo', 'status' => 'activa'],
            ['message' => 'TechBot Pro: <50% avance con <30% tiempo restante.', 'severity' => 'error']
        );
        Alert::firstOrCreate(
            ['project_id' => $projects[4]->id, 'type' => 'sin_actualizacion', 'status' => 'activa'],
            ['message' => 'Sistema de Tracking: sin actualización en 3 días.', 'severity' => 'warning']
        );

        // ── Meetings ──────────────────────────────────────────────────────────
        Meeting::firstOrCreate(
            ['title' => 'Revisión Sprint Nexo Corp'],
            [
                'project_id'   => $projects[0]->id,
                'meeting_date' => today()->toDateString(),
                'meeting_time' => '10:00:00',
                'description'  => 'Revisión del sprint quincenal',
                'status'       => 'programada',
                'created_by'   => $admin->id,
            ]
        );
        Meeting::firstOrCreate(
            ['title' => 'Demo cliente TechVision'],
            [
                'project_id'   => $projects[1]->id,
                'meeting_date' => today()->toDateString(),
                'meeting_time' => '15:30:00',
                'description'  => 'Demo del CRM al cliente',
                'status'       => 'programada',
                'created_by'   => $admin->id,
            ]
        );

        // ── Pending Items ─────────────────────────────────────────────────────
        PendingItem::firstOrCreate(
            ['project_id' => $projects[1]->id, 'type' => 'cliente', 'description' => 'Faltan credenciales de acceso al CRM actual'],
            ['status' => 'pendiente']
        );
        PendingItem::firstOrCreate(
            ['project_id' => $projects[3]->id, 'type' => 'cliente', 'description' => 'Datos de entrenamiento pendientes de envío'],
            ['status' => 'pendiente']
        );
        PendingItem::firstOrCreate(
            ['project_id' => $projects[1]->id, 'type' => 'ingeniero', 'description' => 'Completar integración con Salesforce', 'assigned_to' => $engineerUsers[1]->id],
            ['status' => 'pendiente']
        );

        $this->command->info('✅ Agil365 seeded successfully!');
        $this->command->info('📧 Admin login: admin@agil365.com / password');
        $this->command->info('📧 Engineer login: ana@agil365.com / password');
    }
}
