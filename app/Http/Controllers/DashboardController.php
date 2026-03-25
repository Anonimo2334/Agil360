<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\Alert;
use App\Models\Meeting;
use App\Models\User;
use App\Models\PendingItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(\App\Services\AlertService $alertService)
    {
        // Actualizar dinámicamente todas las alertas antes de mostrar el dashboard
        $alertService->scanAll();

        $user = auth()->user();

        // ── Admin / Gerente / Super Admin: vista global ─────────────────────────
        if ($user->isAdmin() || $user->hasAnyRole(['super_admin', 'admin', 'gerente'])) {
            return $this->adminDashboard($user);
        }

        // ── Ingeniero / Soporte: vista personal ─────────────────────────────────
        return $this->engineerDashboard($user);
    }

    // ─── ADMIN DASHBOARD ──────────────────────────────────────────────────────────
    private function adminDashboard($user)
    {
        // KPIs globales
        $totalProjects     = Project::count();
        $activeProjects    = Project::whereIn('status', ['iniciado', 'en_proceso'])->count();
        $supportProjects   = Project::where('status', 'soporte')->count();
        $completedProjects = Project::where('status', 'completado')->count();
        $atRiskProjects    = Project::where('is_at_risk', true)->count();

        // Tasks KPIs
        $totalTasks     = Task::count();
        $pendingTasks   = Task::where('status', 'pendiente')->count();
        $inProgressTasks = Task::where('status', 'en_progreso')->count();
        $completedTasks = Task::where('status', 'completada')->count();
        $overdueTasks   = Task::where('status', '!=', 'completada')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', today())
            ->count();

        // Projects table (principales + en riesgo primero)
        $projects = Project::with(['company', 'primaryEngineer', 'backupEngineer'])
            ->orderByRaw("FIELD(status, 'en_proceso', 'iniciado', 'soporte', 'completado', 'cancelado')")
            ->orderByDesc('is_at_risk')
            ->orderBy('end_date')
            ->take(10)
            ->get();

        // Alertas activas
        $alerts = Alert::with('project')
            ->where('status', 'activa')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Top ingenieros activos por carga de trabajo
        $engineers = User::whereHas('role', fn($q) => $q->whereIn('slug', ['ingeniero', 'soporte', 'admin']))
            ->where('is_active', true)
            ->withCount([
                'primaryProjects as active_projects_count' => fn($q) => $q->whereNotIn('status', ['completado', 'cancelado']),
            ])
            ->withCount([
                'assignedTasks as completed_tasks_count' => fn($q) => $q->where('status', 'completada'),
            ])
            ->withCount([
                'assignedTasks as pending_tasks_count' => fn($q) => $q->whereIn('status', ['pendiente', 'en_progreso']),
            ])
            ->orderByDesc('active_projects_count')
            ->take(6)
            ->get();

        // Reuniones de hoy
        $todayMeetings = Meeting::with('project.company')
            ->whereDate('meeting_date', today())
            ->where('status', 'programada')
            ->orderBy('meeting_time')
            ->take(5)
            ->get();

        // Pending items count
        $pendingItems = PendingItem::where('status', 'pendiente')->count();

        // Progreso promedio de todos los proyectos activos
        $avgProgress = Project::whereIn('status', ['iniciado', 'en_proceso'])
            ->avg('progress_percentage') ?? 0;

        // Proyectos completados este mes
        $completedThisMonth = Project::where('status', 'completado')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Tareas completadas esta semana
        $tasksCompletedThisWeek = Task::where('status', 'completada')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('pages.agil365.dashboard', [
            'title'                 => 'Dashboard',
            'viewType'              => 'admin',
            // KPIs
            'totalProjects'         => $totalProjects,
            'activeProjects'        => $activeProjects,
            'supportProjects'       => $supportProjects,
            'completedProjects'     => $completedProjects,
            'atRiskProjects'        => $atRiskProjects,
            'totalTasks'            => $totalTasks,
            'pendingTasks'          => $pendingTasks,
            'inProgressTasks'       => $inProgressTasks,
            'completedTasks'        => $completedTasks,
            'overdueTasks'          => $overdueTasks,
            // Tables & panels
            'projects'              => $projects,
            'alerts'                => $alerts,
            'engineers'             => $engineers,
            'todayMeetings'         => $todayMeetings,
            'pendingItems'          => $pendingItems,
            'avgProgress'           => round($avgProgress),
            'completedThisMonth'    => $completedThisMonth,
            'tasksCompletedThisWeek'=> $tasksCompletedThisWeek,
        ]);
    }

    // ─── ENGINEER DASHBOARD ───────────────────────────────────────────────────────
    private function engineerDashboard($user)
    {
        // Proyectos donde es ingeniero principal o de respaldo
        $myProjects = Project::with(['company', 'primaryEngineer', 'backupEngineer'])
            ->where(fn($q) => $q->where('primary_engineer_id', $user->id)
                ->orWhere('backup_engineer_id', $user->id))
            ->orderByRaw("FIELD(status, 'en_proceso', 'iniciado', 'soporte', 'completado', 'cancelado')")
            ->get();

        // Mis KPIs de proyectos
        $myTotalProjects     = $myProjects->count();
        $myActiveProjects    = $myProjects->whereIn('status', ['iniciado', 'en_proceso'])->count();
        $myCompletedProjects = $myProjects->where('status', 'completado')->count();
        $myAtRiskProjects    = $myProjects->where('is_at_risk', true)->count();

        // Mis tareas asignadas
        $myTasks = Task::with('project.company')
            ->where('assigned_engineer_id', $user->id)
            ->orderBy('due_date')
            ->get();

        $myPendingTasks   = $myTasks->where('status', 'pendiente')->count();
        $myInProgressTasks = $myTasks->where('status', 'en_progreso')->count();
        $myCompletedTasks = $myTasks->where('status', 'completada')->count();

        // Tareas urgentes (vencidas o que vencen hoy)
        $urgentTasks = $myTasks->filter(fn($t) =>
            $t->status !== 'completada' &&
            $t->due_date &&
            $t->due_date->lte(today())
        )->take(5);

        // Tareas recientes (últimas 8 no completadas)
        $recentTasks = $myTasks->whereNotIn('status', ['completada'])->take(8)->values();

        // Rendimiento personal: tareas completadas esta semana
        $myTasksThisWeek = Task::where('assigned_engineer_id', $user->id)
            ->where('status', 'completada')
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        // Tareas completadas este mes
        $myTasksThisMonth = Task::where('assigned_engineer_id', $user->id)
            ->where('status', 'completada')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // Progreso promedio de sus proyectos activos
        $myAvgProgress = $myProjects
            ->whereIn('status', ['iniciado', 'en_proceso'])
            ->avg('progress_percentage') ?? 0;

        // Mis pendientes asignados
        $myPendingItems = PendingItem::with('project.company')
            ->where('assigned_to', $user->id)
            ->where('status', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Reuniones de hoy (de mis proyectos)
        $todayMeetings = Meeting::with('project')
            ->whereHas('project', fn($q) =>
                $q->where('primary_engineer_id', $user->id)
                  ->orWhere('backup_engineer_id', $user->id)
            )
            ->whereDate('meeting_date', today())
            ->where('status', 'programada')
            ->orderBy('meeting_time')
            ->take(5)
            ->get();
        // Mis alertas activas (solo de mis proyectos)
        $myProjectIds = $myProjects->pluck('id')->toArray();
        $alerts = Alert::with('project')
            ->whereIn('project_id', $myProjectIds)
            ->where('status', 'activa')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('pages.agil365.dashboard', [
            'title'               => 'Mi Dashboard',
            'viewType'            => 'engineer',
            // KPIs
            'myTotalProjects'     => $myTotalProjects,
            'myActiveProjects'    => $myActiveProjects,
            'myCompletedProjects' => $myCompletedProjects,
            'myAtRiskProjects'    => $myAtRiskProjects,
            'myPendingTasks'      => $myPendingTasks,
            'myInProgressTasks'   => $myInProgressTasks,
            'myCompletedTasks'    => $myCompletedTasks,
            'myTasksThisWeek'     => $myTasksThisWeek,
            'myTasksThisMonth'    => $myTasksThisMonth,
            'myAvgProgress'       => round($myAvgProgress),
            // Data
            'myProjects'         => $myProjects,
            'recentTasks'        => $recentTasks,
            'urgentTasks'        => $urgentTasks,
            'myPendingItems'     => $myPendingItems,
            'todayMeetings'      => $todayMeetings,
            'alerts'             => $alerts,
            // Compatibility — some view sections need these
            'atRiskProjects'     => $myAtRiskProjects,
        ]);
    }
}
