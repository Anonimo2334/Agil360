<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::with(['project.company', 'assignedEngineer']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('engineer_id')) {
            $query->where('assigned_engineer_id', $request->engineer_id);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks     = $query->orderBy('due_date')->paginate(20)->withQueryString();
        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte']))->where('is_active', true)->get();
        $projects  = Project::whereNotIn('status', ['completado', 'cancelado'])->orderBy('project_name')->get();

        return view('pages.agil365.tareas.index', compact('tasks', 'engineers', 'projects'), ['title' => 'Tareas']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'           => 'required|exists:projects,id',
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'priority'             => 'required|in:baja,media,alta,critica',
            'status'               => 'required|in:pendiente,en_progreso,completada,bloqueada',
            'start_date'           => 'nullable|date',
            'due_date'             => 'nullable|date',
            'progress'             => 'nullable|integer|min:0|max:100',
        ]);

        // Auto-set progress to 100 when creating as completed
        if (isset($validated['status']) && $validated['status'] === 'completada') {
            $validated['progress'] = 100;
        }

        $task = Task::create($validated);

        return redirect()->back()->with('success', 'Tarea creada exitosamente.');
    }

    /**
     * Search suggestions for live autocomplete
     */
    public function suggestions(Request $request)
    {
        $q = $request->get('q', '');
        $tasks = Task::with('project')
            ->where('title', 'like', "%{$q}%")
            ->limit(8)
            ->get(['id', 'title', 'status', 'project_id']);

        return response()->json($tasks->map(fn($t) => [
            'id'           => $t->id,
            'title'        => $t->title,
            'status'       => $t->status,
            'project_name' => $t->project->project_name ?? '—',
        ]));
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'documentation'        => 'nullable|string',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'priority'             => 'required|in:baja,media,alta,critica',
            'status'               => 'required|in:pendiente,en_progreso,completada,bloqueada',
            'start_date'           => 'nullable|date',
            'due_date'             => 'nullable|date',
            'progress'             => 'nullable|integer|min:0|max:100',
            'project_id'           => 'nullable|exists:projects,id',
        ]);

        // If marking as complete, force progress to 100
        if ($validated['status'] === 'completada') {
            $validated['progress'] = 100;
        }

        $task->update($validated);

        // Recalculate project progress based on tasks
        $project = $task->project;
        if ($project) {
            $avgProgress = Task::where('project_id', $project->id)->avg('progress');
            $project->update(['progress_percentage' => (int) round($avgProgress ?? 0)]);
        }

        return redirect()->back()->with('success', 'Tarea actualizada.');
    }

    /**
     * Save documentation/notes for a task (AJAX)
     */
    public function document(Request $request, Task $task)
    {
        $request->validate(['documentation' => 'nullable|string']);
        $task->update(['documentation' => $request->documentation]);
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request, Task $task)
    {

        $validated = $request->validate([
            'status' => 'required|in:pendiente,en_progreso,completada,bloqueada',
        ]);

        // Auto-set progress to 100 when completing
        $updates = ['status' => $validated['status']];
        if ($validated['status'] === 'completada') {
            $updates['progress'] = 100;
        }

        $task->update($updates);

        // Recalculate project progress
        $project = $task->project;
        $avgProgress = Task::where('project_id', $project->id)->avg('progress');
        $project->update(['progress_percentage' => (int) round($avgProgress ?? 0)]);

        return response()->json(['success' => true, 'progress' => $task->fresh()->progress]);
    }

    public function updateDate(Request $request, Task $task)
    {
        if ($task->status === 'completada') {
            return response()->json(['success' => false, 'message' => 'No se puede cambiar fecha a tarea completada.'], 403);
        }
        $request->validate(['new_date' => 'required|date']);
        $task->update(['due_date' => $request->new_date]);
        return response()->json(['success' => true]);
    }

    public function destroy(Task $task)
    {
        $projectId = $task->project_id;
        $task->delete();
        return redirect()->back()->with('success', 'Tarea eliminada.');
    }
}
