<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Bonus;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function activeProjects(Request $request)
    {
        $query = Project::with(['company', 'primaryEngineer'])
            ->whereIn('status', ['iniciado', 'en_proceso', 'soporte']);

        if ($request->filled('engineer_id')) {
            $query->where('primary_engineer_id', $request->engineer_id);
        }
        
        $projects = $query->orderBy('end_date')->get();
        $engineers = User::whereHas('role', fn($q) => $q->whereIn('slug', ['ingeniero', 'soporte']))->get();

        $stats = [
            'total' => $projects->count(),
            'at_risk' => $projects->where('is_at_risk', true)->count(),
            'avg_progress' => $projects->count() > 0 ? round($projects->avg('progress_percentage')) : 0,
            'delayed' => $projects->where('end_date', '<', now())->count(),
        ];

        return view('pages.agil365.reportes.activos', compact('projects', 'engineers', 'stats'), ['title' => 'Proyectos Activos']);
    }

    public function performance(Request $request)
    {
        $engineers = User::whereHas('role', fn($q) => $q->whereIn('slug', ['ingeniero', 'soporte']))
            ->withCount(['primaryProjects', 'assignedTasks', 'bonuses'])
            ->withAvg('primaryProjects', 'progress_percentage')
            ->get();

        return view('pages.agil365.reportes.rendimiento', compact('engineers'), ['title' => 'Rendimiento de Ingenieros']);
    }

    public function bonuses(Request $request)
    {
        $query = Bonus::with(['engineer', 'project']);
        
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        $bonuses = $query->orderByDesc('created_at')->get();

        $stats = [
            'total_amount' => $bonuses->sum('amount'),
            'paid_amount' => $bonuses->where('status', 'pagado')->sum('amount'),
            'pending_amount' => $bonuses->whereIn('status', ['pendiente', 'aprobado'])->sum('amount'),
            'count' => $bonuses->count(),
        ];

        return view('pages.agil365.reportes.bonos', compact('bonuses', 'stats'), ['title' => 'Reporte de Bonos']);
    }
}
