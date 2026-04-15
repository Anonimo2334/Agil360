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

        $imported = 0;
        $errors = 0;
        $mode = 'standard';
        $headerDetected = false;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            // Check for empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Detect if Agil360 custom format (headers start with ID, Cliente)
            if (isset($row[2]) && strtolower(trim($row[2])) === 'cliente') {
                $mode = 'agil360';
                $headerDetected = true;
                continue;
            }
            
            // Detect if Standard format
            if (!$headerDetected && isset($row[1]) && strtolower(trim($row[1])) === 'nombre_proyecto') {
                $headerDetected = true;
                continue;
            }
            
            // Skip title/header decorative rows in Agil360 files
            if (isset($row[1]) && str_contains(strtoupper($row[1]), 'LISTADO')) {
                continue;
            }

            if ($mode === 'agil360') {
                if (count($row) < 7) {
                    $errors++;
                    continue;
                }

                $cliente_name = trim($row[2]);
                if (empty($cliente_name)) {
                    continue;
                }

                $estadoStr = strtolower(trim($row[3]));
                $statusMap = [
                    'inicio' => 'iniciado',
                    'en progreso' => 'en_proceso',
                    'soporte' => 'soporte',
                    'completado' => 'completado',
                    'cancelado' => 'cancelado'
                ];
                $status = $statusMap[$estadoStr] ?? 'iniciado';

                $avanceStr = trim($row[4]);
                $avance = (int) preg_replace('/[^0-9]/', '', $avanceStr);

                $start_date = null;
                if (!empty(trim($row[5])) && strtolower(trim($row[5])) !== 'completado') {
                    try {
                        $parts = explode('/', trim($row[5]));
                        if (count($parts) === 3) {
                            $start_date = \Carbon\Carbon::createFromFormat('n/j/Y', trim($row[5]))->format('Y-m-d');
                        }
                    } catch (\Exception $e) {}
                }

                $end_date = null;
                if (!empty(trim($row[6])) && strtolower(trim($row[6])) !== 'completado' && strtolower(trim($row[6])) !== 'enero') {
                    try {
                        $parts = explode('/', trim($row[6]));
                        if (count($parts) === 3) {
                            $end_date = \Carbon\Carbon::createFromFormat('n/j/Y', trim($row[6]))->format('Y-m-d');
                        }
                    } catch (\Exception $e) {}
                }

                $web = trim($row[7] ?? '');
                if (strtolower($web) === 'por buscar' || str_contains(strtolower($web), 'comprar') || str_contains(strtolower($web), 'esperamos')) {
                    $web = null;
                } elseif (!empty($web) && !str_starts_with($web, 'http')) {
                    $web = 'https://' . $web;
                }
                
                if ($web && strlen($web) > 255) {
                    $web = substr($web, 0, 255);
                }

                $ceo = trim($row[8] ?? '');
                $notes = trim($row[13] ?? '');

                // Find or create company
                $company = Company::firstOrCreate(
                    ['name' => $cliente_name],
                    [
                        'contact_name' => substr($ceo, 0, 255),
                        'website' => $web
                    ]
                );

                // Find engineers
                $primaryEngineerName = trim($row[9] ?? '');
                $primaryEngineerId = null;
                if ($primaryEngineerName) {
                    $eng = User::where('name', 'like', "%{$primaryEngineerName}%")->first();
                    if ($eng) $primaryEngineerId = $eng->id;
                }

                $backupEngineerName = trim($row[10] ?? '');
                $backupEngineerId = null;
                if ($backupEngineerName) {
                    $eng = User::where('name', 'like', "%{$backupEngineerName}%")->first();
                    if ($eng) $backupEngineerId = $eng->id;
                }

                try {
                    Project::create([
                        'company_id'          => $company->id,
                        'project_name'        => $cliente_name,
                        'ceo'                 => substr($ceo, 0, 255) ?: null,
                        'primary_engineer_id' => $primaryEngineerId,
                        'backup_engineer_id'  => $backupEngineerId,
                        'start_date'          => $start_date,
                        'end_date'            => $end_date,
                        'progress_percentage' => $avance,
                        'status'              => $status,
                        'website_url'         => $web,
                        'notes'               => $notes ?: null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                }

            } else {
                // Standard format parsing
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
