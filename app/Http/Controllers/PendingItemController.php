<?php

namespace App\Http\Controllers;

use App\Models\PendingItem;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class PendingItemController extends Controller
{
    public function byClient(Request $request)
    {
        $query = PendingItem::with(['project.company'])
            ->where('type', 'cliente');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pending  = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $projects = Project::whereNotIn('status', ['completado', 'cancelado'])->orderBy('project_name')->get();

        return view('pages.agil365.pendientes.cliente', compact('pending', 'projects'), ['title' => 'Pendientes por Cliente']);
    }

    public function byEngineer(Request $request)
    {
        $query = PendingItem::with(['project.company', 'assignedUser'])
            ->where('type', 'ingeniero');

        if ($request->filled('engineer_id')) {
            $query->where('assigned_to', $request->engineer_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pending   = $query->orderByDesc('created_at')->paginate(20)->withQueryString();
        $projects  = Project::whereNotIn('status', ['completado', 'cancelado'])->orderBy('project_name')->get();

        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte']))
            ->where('is_active', true)
            ->withCount(['primaryProjects as proyectos_count', 'assignedTasks as tareas_count', 'meetings as reuniones_count'])
            ->with([
                'assignedTasks' => fn($q) => $q->where('status', '!=', 'completada')->latest()->take(3),
                'meetings' => fn($q) => $q->where('meeting_date', '>=', today())->orderBy('meeting_date')->orderBy('meeting_time')->take(2),
            ])
            ->get();

        return view('pages.agil365.pendientes.ingeniero', compact('pending', 'projects', 'engineers'), ['title' => 'Pendientes por Ingeniero']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'  => 'required|exists:projects,id',
            'type'        => 'required|in:cliente,ingeniero',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        PendingItem::create($request->only('project_id', 'type', 'description', 'assigned_to') + ['status' => 'pendiente']);

        return redirect()->back()->with('success', 'Pendiente registrado.');
    }

    public function resolve(Request $request, PendingItem $pendingItem)
    {
        $request->validate([
            'resolution_note' => 'required|string',
        ]);
        $pendingItem->update([
            'status' => 'completado',
            'resolution_note' => $request->resolution_note,
        ]);
        return redirect()->back()->with('success', 'Pendiente marcado como completado y documentado.');
    }

    public function destroy(PendingItem $pendingItem)
    {
        $pendingItem->delete();
        return redirect()->back()->with('success', 'Pendiente eliminado.');
    }
}
