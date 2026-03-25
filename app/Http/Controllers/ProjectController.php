<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use App\Models\Alert;
use App\Models\Bonus;
use App\Models\ProjectNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with(['company', 'primaryEngineer', 'backupEngineer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('project_name', 'like', "%{$search}%")
                  ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('engineer_id')) {
            $eid = $request->engineer_id;
            $query->where(function ($q) use ($eid) {
                $q->where('primary_engineer_id', $eid)->orWhere('backup_engineer_id', $eid);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $projects  = $query->orderBy('end_date')->paginate(20)->withQueryString();
        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte']))->where('is_active', true)->get();
        $companies = Company::where('is_active', true)->orderBy('name')->get();

        return view('pages.agil365.proyectos.index', compact('projects', 'engineers', 'companies'), ['title' => 'Proyectos']);
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte', 'admin']))->where('is_active', true)->get();
        return view('pages.agil365.proyectos.create', compact('companies', 'engineers'), ['title' => 'Nuevo Proyecto']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id'           => 'required|exists:companies,id',
            'project_name'         => 'required|string|max:255',
            'ceo'                  => 'nullable|string|max:255',
            'primary_engineer_id'  => 'nullable|exists:users,id',
            'backup_engineer_id'   => 'nullable|exists:users,id',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'progress_percentage'  => 'integer|min:0|max:100',
            'status'               => 'required|in:iniciado,en_proceso,soporte,completado,cancelado',
            'platform'             => 'nullable|string|max:100',
            'bot_name'             => 'nullable|string|max:100',
            'website_url'          => 'nullable|url|max:255',
            'server_hosting'       => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ]);

        $project = Project::create($validated);
        $this->evaluateRisk($project);
        $this->checkAndCreateBonus($project);

        return redirect()->route('proyectos.show', $project)
            ->with('success', 'Proyecto creado exitosamente.');
    }

    public function show(Project $project)
    {
        $project->load([
            'company', 'primaryEngineer', 'backupEngineer',
            'tasks.assignedEngineer', 'notes.author',
            'meetings.participants', 'alerts',
            'pendingItems.assignedUser', 'bonuses.engineer',
        ]);

        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte', 'admin']))->where('is_active', true)->get();

        return view('pages.agil365.proyectos.show', compact('project', 'engineers'), ['title' => $project->project_name]);
    }

    public function edit(Project $project)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $engineers = User::whereHas('role', fn ($q) => $q->whereIn('slug', ['ingeniero', 'soporte', 'admin']))->where('is_active', true)->get();
        return view('pages.agil365.proyectos.edit', compact('project', 'companies', 'engineers'), ['title' => 'Editar Proyecto']);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'company_id'           => 'required|exists:companies,id',
            'project_name'         => 'required|string|max:255',
            'ceo'                  => 'nullable|string|max:255',
            'primary_engineer_id'  => 'nullable|exists:users,id',
            'backup_engineer_id'   => 'nullable|exists:users,id',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'progress_percentage'  => 'integer|min:0|max:100',
            'status'               => 'required|in:iniciado,en_proceso,soporte,completado,cancelado',
            'platform'             => 'nullable|string|max:100',
            'bot_name'             => 'nullable|string|max:100',
            'website_url'          => 'nullable|url|max:255',
            'server_hosting'       => 'nullable|string|max:255',
            'notes'                => 'nullable|string',
        ]);

        $project->update($validated);
        $this->evaluateRisk($project);
        $this->checkAndCreateBonus($project);

        return redirect()->route('proyectos.show', $project)
            ->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('proyectos')
            ->with('success', 'Proyecto eliminado.');
    }

    public function updateProgress(Request $request, Project $project)
    {
        $request->validate(['progress_percentage' => 'required|integer|min:0|max:100']);
        $project->update(['progress_percentage' => $request->progress_percentage]);
        $this->evaluateRisk($project);
        return back()->with('success', 'Progreso actualizado.');
    }

    public function storeNote(Request $request, Project $project)
    {
        $request->validate(['note' => 'required|string']);
        ProjectNote::create([
            'project_id' => $project->id,
            'user_id'    => auth()->id(),
            'note'       => $request->note,
        ]);
        return back()->with('success', 'Nota agregada.');
    }

    // ─── Import & Export CSV ───────────────────────────────────────────────────

    public function exportCsv()
    {
        $fileName = 'proyectos_'.date('Ymd_His').'.csv';
        $projects = Project::with(['company', 'primaryEngineer'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID_Cliente', 'Nombre_Proyecto', 'CEO', 'Fecha_Inicio', 'Fecha_Fin', 'Avance_Porcentaje', 'Estado'];

        $callback = function() use($projects, $columns) {
            $file = fopen('php://output', 'w');
            // Adding BOM for Excel UTF-8 compatibility
            fputs($file, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF))); 
            fputcsv($file, $columns, ',');

            foreach ($projects as $project) {
                fputcsv($file, [
                    $project->company_id,
                    $project->project_name,
                    $project->ceo,
                    $project->start_date ? $project->start_date->format('Y-m-d') : '',
                    $project->end_date ? $project->end_date->format('Y-m-d') : '',
                    $project->progress_percentage,
                    $project->status,
                ], ',');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        
        // Remove BOM if present
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $header = fgetcsv($file, 1000, ',');
        if (!$header || count($header) < 7) {
            return redirect()->back()->with('error', 'El archivo debe tener 7 columnas separadas por coma.');
        }

        $imported = 0;
        $errors = 0;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if (count($row) < 7) {
                $errors++;
                continue;
            }

            $company_id = trim($row[0]);
            $project_name = trim($row[1]);

            if (empty($company_id) || empty($project_name)) {
                $errors++;
                continue;
            }

            try {
                $status = strtolower(trim($row[6]));
                $validStatuses = ['iniciado','en_proceso','soporte','completado','cancelado'];
                
                Project::create([
                    'company_id'          => $company_id,
                    'project_name'        => $project_name,
                    'ceo'                 => trim($row[2]) ?: null,
                    'start_date'          => trim($row[3]) ?: null,
                    'end_date'            => trim($row[4]) ?: null,
                    'progress_percentage' => is_numeric(trim($row[5])) ? (int)trim($row[5]) : 0,
                    'status'              => in_array($status, $validStatuses) ? $status : 'iniciado',
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors++;
            }
        }
        fclose($file);

        return redirect()->back()->with('success', "Importación completada: $imported proyectos creados, $errors filas con error o vacías.");
    }

    // ─── Internal Helpers ────────────────────────────────────────────────────────

    private function evaluateRisk(Project $project): void
    {
        $isAtRisk = $project->checkIfAtRisk();
        $project->update(['is_at_risk' => $isAtRisk]);

        if ($isAtRisk) {
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

        // Overdue check
        if ($project->is_overdue) {
            Alert::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'vencido', 'status' => 'activa'],
                [
                    'message'  => "{$project->project_name}: proyecto vencido.",
                    'severity' => 'error',
                ]
            );
        }
    }

    private function checkAndCreateBonus(Project $project): void
    {
        if ($project->status !== 'completado' || !$project->primary_engineer_id) return;
        if (!$project->end_date || now()->isAfter($project->end_date)) return;

        Bonus::firstOrCreate(
            ['project_id' => $project->id, 'engineer_id' => $project->primary_engineer_id],
            [
                'amount' => 50.00,
                'status' => 'pendiente',
                'reason' => "Proyecto completado antes de fecha límite: {$project->project_name}",
            ]
        );
    }
}
