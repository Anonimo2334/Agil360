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
            'progress'             => 'integer|min:0|max:100',
        ]);

        $task = Task::create($validated);

        return redirect()->back()->with('success', 'Tarea creada exitosamente.');
    }

    public function update(Request $request, Task $task)
    {
        // Completed tasks are locked — only allow editing non-status fields
        if ($task->status === 'completada') {
            return redirect()->back()->with('error', 'Las tareas completadas no se pueden modificar.');
        }

        $validated = $request->validate([
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'priority'             => 'required|in:baja,media,alta,critica',
            'status'               => 'required|in:pendiente,en_progreso,completada,bloqueada',
            'start_date'           => 'nullable|date',
            'due_date'             => 'nullable|date',
            'progress'             => 'integer|min:0|max:100',
        ]);

        // If marking as complete, force progress to 100
        if ($validated['status'] === 'completada') {
            $validated['progress'] = 100;
        }

        $task->update($validated);

        // Recalculate project progress based on tasks
        $project = $task->project;
        $avgProgress = Task::where('project_id', $project->id)->avg('progress');
        $project->update(['progress_percentage' => (int) round($avgProgress ?? 0)]);

        return redirect()->back()->with('success', 'Tarea actualizada.');
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Completed tasks are permanently locked — cannot be moved to another status
        if ($task->status === 'completada') {
            return response()->json([
                'success' => false,
                'locked'  => true,
                'message' => 'Las tareas completadas no pueden cambiar de estado.',
            ], 403);
        }

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
