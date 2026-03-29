<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\PendingItemController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;

// ─── Auth Routes (Guest Only) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showLogin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'login'])->name('signin.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Authenticated Routes ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Dashboard — todos los autenticados pueden verlo
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ─── Mi Perfil & Ajustes ──────────────────────────────────────────────────
    Route::get('/perfil', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/perfil/actualizar', [App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update_info');
    Route::put('/perfil/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // ─── Integración Google Calendar ───────────────────────────────────────────
    Route::get('/google-calendar/connect', [App\Http\Controllers\GoogleCalendarController::class, 'connect'])->name('google.calendar.connect');
    Route::get('/google-calendar/callback', [App\Http\Controllers\GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');
    Route::post('/google-calendar/disconnect', [App\Http\Controllers\GoogleCalendarController::class, 'disconnect'])->name('google.calendar.disconnect');

    // ─── Clientes ──────────────────────────────────────────────────────────────
    // Ver: admin, gerente, soporte, visualizador, ingeniero (solo vista)
    Route::get('/clientes', [CompanyController::class, 'index'])
        ->name('clientes')
        ->middleware('permission:clientes.ver');

    // Crear / Editar / Eliminar: solo admin y gerente
    Route::get('/clientes/nuevo', [CompanyController::class, 'create'])
        ->name('clientes.create')
        ->middleware('permission:clientes.crear');
    Route::post('/clientes', [CompanyController::class, 'store'])
        ->name('clientes.store')
        ->middleware('permission:clientes.crear');
    Route::get('/clientes/{company}', [CompanyController::class, 'show'])
        ->name('clientes.show')
        ->middleware('permission:clientes.ver');
    Route::get('/clientes/{company}/editar', [CompanyController::class, 'edit'])
        ->name('clientes.edit')
        ->middleware('permission:clientes.editar');
    Route::put('/clientes/{company}', [CompanyController::class, 'update'])
        ->name('clientes.update')
        ->middleware('permission:clientes.editar');
    Route::delete('/clientes/{company}', [CompanyController::class, 'destroy'])
        ->name('clientes.destroy')
        ->middleware('permission:clientes.eliminar');

    // ─── Proyectos ─────────────────────────────────────────────────────────────
    Route::get('/proyectos', [ProjectController::class, 'index'])
        ->name('proyectos')
        ->middleware('permission:proyectos.ver');
    Route::get('/proyectos/exportar-csv', [ProjectController::class, 'exportCsv'])
        ->name('proyectos.export')
        ->middleware('permission:proyectos.ver');
    Route::post('/proyectos/importar-csv', [ProjectController::class, 'importCsv'])
        ->name('proyectos.import')
        ->middleware('permission:proyectos.crear');
    Route::get('/proyectos/nuevo', [ProjectController::class, 'create'])
        ->name('proyectos.create')
        ->middleware('permission:proyectos.crear');
    Route::post('/proyectos', [ProjectController::class, 'store'])
        ->name('proyectos.store')
        ->middleware('permission:proyectos.crear');
    Route::get('/proyectos/{project}', [ProjectController::class, 'show'])
        ->name('proyectos.show')
        ->middleware('permission:proyectos.ver');
    Route::get('/proyectos/{project}/editar', [ProjectController::class, 'edit'])
        ->name('proyectos.edit')
        ->middleware('permission:proyectos.editar');
    Route::put('/proyectos/{project}', [ProjectController::class, 'update'])
        ->name('proyectos.update')
        ->middleware('permission:proyectos.editar');
    Route::delete('/proyectos/{project}', [ProjectController::class, 'destroy'])
        ->name('proyectos.destroy')
        ->middleware('permission:proyectos.eliminar');
    Route::patch('/proyectos/{project}/progreso', [ProjectController::class, 'updateProgress'])
        ->name('proyectos.progress')
        ->middleware('permission:proyectos.editar');
    Route::post('/proyectos/{project}/notas', [ProjectController::class, 'storeNote'])
        ->name('proyectos.notes.store')
        ->middleware('permission:proyectos.ver');

    // ─── Tareas ────────────────────────────────────────────────────────────────
    Route::get('/tareas', [TaskController::class, 'index'])
        ->name('tareas')
        ->middleware('permission:tareas.ver');
    Route::post('/tareas', [TaskController::class, 'store'])
        ->name('tareas.store')
        ->middleware('permission:tareas.crear');
    Route::put('/tareas/{task}', [TaskController::class, 'update'])
        ->name('tareas.update')
        ->middleware('permission:tareas.editar');
    Route::delete('/tareas/{task}', [TaskController::class, 'destroy'])
        ->name('tareas.destroy')
        ->middleware('permission:tareas.eliminar');
    Route::patch('/tareas/{task}/estado', [TaskController::class, 'updateStatus'])
        ->name('tareas.status')
        ->middleware('permission:tareas.editar');
    Route::post('/tareas/{task}/actualizar-fecha', [TaskController::class, 'updateDate'])
        ->name('tareas.update_date')
        ->middleware('permission:tareas.editar');

    // ─── Pendientes ────────────────────────────────────────────────────────────
    // Ruta genérica que redirige según el rol
    Route::get('/pendientes', function () {
        if (auth()->user()->hasAnyRole(['super_admin', 'admin', 'gerente'])) {
            return redirect()->route('pendientes.cliente');
        }
        return redirect()->route('pendientes.ingeniero');
    })->name('pendientes');

    // Ingenieros y soporte pueden ver sus pendientes
    Route::get('/pendientes/cliente', [PendingItemController::class, 'byClient'])
        ->name('pendientes.cliente')
        ->middleware('permission:clientes.ver');
    Route::get('/pendientes/ingeniero', [PendingItemController::class, 'byEngineer'])
        ->name('pendientes.ingeniero')
        ->middleware('permission:tareas.ver');
    Route::post('/pendientes', [PendingItemController::class, 'store'])
        ->name('pendientes.store')
        ->middleware('permission:tareas.editar');
    Route::patch('/pendientes/{pendingItem}/resolver', [PendingItemController::class, 'resolve'])
        ->name('pendientes.resolve')
        ->middleware('permission:tareas.editar');
    Route::delete('/pendientes/{pendingItem}', [PendingItemController::class, 'destroy'])
        ->name('pendientes.destroy')
        ->middleware('permission:tareas.eliminar');

    // ─── Reuniones ─────────────────────────────────────────────────────────────
    Route::get('/reuniones', [MeetingController::class, 'index'])
        ->name('reuniones')
        ->middleware('permission:tareas.ver');
    Route::post('/reuniones', [MeetingController::class, 'store'])
        ->name('reuniones.store')
        ->middleware('permission:tareas.editar');
    Route::get('/reuniones/logs', [MeetingController::class, 'logs'])
        ->name('reuniones.logs')
        ->middleware('permission:tareas.ver');
    Route::get('/reuniones/{meeting}', [MeetingController::class, 'show'])
        ->name('reuniones.show')
        ->middleware('permission:tareas.ver');
    Route::put('/reuniones/{meeting}', [MeetingController::class, 'update'])
        ->name('reuniones.update')
        ->middleware('permission:tareas.editar');
    Route::patch('/reuniones/{meeting}/estado', [MeetingController::class, 'updateStatus'])
        ->name('reuniones.status')
        ->middleware('permission:tareas.editar');
    Route::delete('/reuniones/{meeting}', [MeetingController::class, 'destroy'])
        ->name('reuniones.destroy')
        ->middleware('permission:tareas.eliminar');
    Route::post('/reuniones/{meeting}/actualizar-fecha', [MeetingController::class, 'updateDate'])
        ->name('reuniones.update_date')
        ->middleware('permission:tareas.editar');

    // ─── Bonos ─────────────────────────────────────────────────────────────────
    // Solo admin y gerente pueden ver/gestionar bonos
    Route::get('/bonos', [BonusController::class, 'index'])
        ->name('bonos')
        ->middleware('role:super_admin,admin,gerente');
    Route::patch('/bonos/{bonus}/aprobar', [BonusController::class, 'approve'])
        ->name('bonos.approve')
        ->middleware('role:super_admin,admin');
    Route::patch('/bonos/{bonus}/pagar', [BonusController::class, 'markPaid'])
        ->name('bonos.paid')
        ->middleware('role:super_admin,admin');
    Route::patch('/bonos/{bonus}/rechazar', [BonusController::class, 'reject'])
        ->name('bonos.reject')
        ->middleware('role:super_admin,admin');

    // ─── Alertas ───────────────────────────────────────────────────────────────
    Route::get('/alertas', [AlertController::class, 'index'])
        ->name('alertas')
        ->middleware('permission:proyectos.ver');
    Route::patch('/alertas/{alert}/leer', [AlertController::class, 'markRead'])
        ->name('alertas.read')
        ->middleware('permission:proyectos.ver');
    Route::patch('/alertas/{alert}/resolver', [AlertController::class, 'resolve'])
        ->name('alertas.resolve')
        ->middleware('permission:proyectos.editar');
    Route::patch('/alertas/{alert}/ignorar', [AlertController::class, 'ignore'])
        ->name('alertas.ignore')
        ->middleware('permission:proyectos.editar');

    // ─── Reportes ──────────────────────────────────────────────────────────────
    Route::get('/reportes/activos', [App\Http\Controllers\ReportController::class, 'activeProjects'])
        ->name('reportes.activos')
        ->middleware('permission:reportes.ver');
    Route::get('/reportes/rendimiento', [App\Http\Controllers\ReportController::class, 'performance'])
        ->name('reportes.rendimiento')
        ->middleware('permission:reportes.ver');
    Route::get('/reportes/bonos', [App\Http\Controllers\ReportController::class, 'bonuses'])
        ->name('reportes.bonos')
        ->middleware('permission:reportes.ver');

    // ─── Usuarios ──────────────────────────────────────────────────────────────
    // Solo admins gestionan usuarios
    Route::get('/usuarios', [UserController::class, 'index'])
        ->name('usuarios')
        ->middleware('permission:usuarios.gestionar');
    Route::post('/usuarios', [UserController::class, 'store'])
        ->name('usuarios.store')
        ->middleware('permission:usuarios.gestionar');
    Route::put('/usuarios/{user}', [UserController::class, 'update'])
        ->name('usuarios.update')
        ->middleware('permission:usuarios.gestionar');
    Route::patch('/usuarios/{user}/estado', [UserController::class, 'toggleStatus'])
        ->name('usuarios.status')
        ->middleware('permission:usuarios.gestionar');
    Route::delete('/usuarios/{user}', [UserController::class, 'destroy'])
        ->name('usuarios.destroy')
        ->middleware('permission:usuarios.gestionar');

    // ─── Roles y Permisos ─────────────────────────────────────────────────────
    Route::get('/roles', [RoleController::class, 'index'])
        ->name('roles')
        ->middleware('permission:roles.gestionar');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->name('roles.update')
        ->middleware('permission:roles.gestionar');

    // ─── Configuración ─────────────────────────────────────────────────────────
    Route::get('/configuracion', [SettingController::class, 'index'])
        ->name('configuracion')
        ->middleware('role:super_admin,admin');
    Route::post('/configuracion', [SettingController::class, 'update'])
        ->name('configuracion.update')
        ->middleware('role:super_admin,admin');
});
