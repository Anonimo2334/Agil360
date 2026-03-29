<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;

/**
 * AlertService
 *
 * Centralises all alert-generation logic so it can be called from:
 *  - DashboardController (every page load)
 *  - ProjectController   (on project save/update)
 *  - A scheduled command (future: artisan schedule)
 */
class AlertService
{
    /**
     * Scan every active project and generate / resolve alerts as needed.
     * Safe to call frequently — uses firstOrCreate / updateOrCreate.
     */
    public function scanAll(): void
    {
        $projects = Project::with(['tasks', 'primaryEngineer'])
            ->whereNotIn('status', ['completado', 'cancelado'])
            ->get();

        foreach ($projects as $project) {
            $this->evaluateProject($project);
        }

        // Auto-resolve alerts for completed/cancelled projects
        Project::whereIn('status', ['completado', 'cancelado'])
            ->each(function ($p) {
                Alert::where('project_id', $p->id)
                    ->where('status', 'activa')
                    ->whereIn('type', ['riesgo', 'vencido', 'progreso_bajo'])
                    ->update(['status' => 'resuelta']);
            });
    }

    /**
     * Evaluate a single project and create/resolve its alerts.
     */
    public function evaluateProject(Project $project): void
    {
        $this->checkRisk($project);
        $this->checkOverdue($project);
        $this->checkStagnant($project);
        $this->checkOverdueTasks($project);
    }

    // ── Private evaluators ───────────────────────────────────────────────────

    private function checkRisk(Project $project): void
    {
        $atRisk = $project->checkIfAtRisk();

        // Update the flag on the project
        $project->updateQuietly(['is_at_risk' => $atRisk]);

        if ($atRisk) {
            Alert::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'riesgo', 'status' => 'activa'],
                [
                    'message'  => "{$project->project_name}: menos del 50% de avance con menos del 30% de tiempo restante.",
                    'severity' => 'error',
                ]
            );
        } else {
            Alert::where('project_id', $project->id)
                ->where('type', 'riesgo')
                ->where('status', 'activa')
                ->update(['status' => 'resuelta']);
        }
    }

    private function checkOverdue(Project $project): void
    {
        if ($project->is_overdue) {
            Alert::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'vencido', 'status' => 'activa'],
                [
                    'message'  => "{$project->project_name}: proyecto vencido desde " . $project->end_date?->format('d/m/Y') . ".",
                    'severity' => 'error',
                ]
            );
        } else {
            Alert::where('project_id', $project->id)
                ->where('type', 'vencido')
                ->where('status', 'activa')
                ->update(['status' => 'resuelta']);
        }
    }

    private function checkStagnant(Project $project): void
    {
        // Alert if progress is 0% and the project is older than 7 days
        if (
            $project->progress_percentage === 0 &&
            $project->start_date &&
            $project->start_date->diffInDays(now()) > 7
        ) {
            Alert::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'progreso_bajo', 'status' => 'activa'],
                [
                    'message'  => "{$project->project_name}: sin progreso registrado desde que inició.",
                    'severity' => 'warning',
                ]
            );
        } else {
            Alert::where('project_id', $project->id)
                ->where('type', 'progreso_bajo')
                ->where('status', 'activa')
                ->update(['status' => 'resuelta']);
        }
    }

    private function checkOverdueTasks(Project $project): void
    {
        // Tasks that are past due and not completed
        $overdueTaskCount = $project->tasks
            ->where('status', '!=', 'completada')
            ->filter(fn($t) => $t->due_date && Carbon::parse($t->due_date)->isPast())
            ->count();

        if ($overdueTaskCount > 0) {
            // Update or recreate so the count stays current
            $existing = Alert::where('project_id', $project->id)
                ->where('type', 'tareas_atrasadas')
                ->where('status', 'activa')
                ->first();

            $message = "{$project->project_name}: {$overdueTaskCount} tarea(s) vencida(s) sin completar.";

            if ($existing) {
                $existing->update(['message' => $message]);
            } else {
                Alert::create([
                    'project_id' => $project->id,
                    'type'       => 'tareas_atrasadas',
                    'message'    => $message,
                    'severity'   => 'warning',
                    'status'     => 'activa',
                ]);
            }
        } else {
            Alert::where('project_id', $project->id)
                ->where('type', 'tareas_atrasadas')
                ->where('status', 'activa')
                ->update(['status' => 'resuelta']);
        }
    }
}
