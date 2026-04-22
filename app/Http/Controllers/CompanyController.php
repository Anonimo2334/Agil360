<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $query = Company::withCount('projects')
            ->withAvg('projects', 'progress_percentage')
            ->withCount(['projects as projects_completed_count' => function ($q) {
                $q->where('status', 'completado');
            }]);

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('contact_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        $companies = $query->orderBy('name')->paginate(15)->withQueryString();
        return view('pages.agil365.clientes.index', compact('companies'), ['title' => 'Clientes']);
    }

    public function create()
    {
        return view('pages.agil365.clientes.create', ['title' => 'Nuevo Cliente']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'whatsapp'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:255',
            'country'      => 'nullable|string|max:100',
            'address'      => 'nullable|string',
            'notes'        => 'nullable|string',
        ]);

        Company::create($validated);

        return redirect()->route('clientes')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Company $company)
    {
        $company->load(['projects.primaryEngineer', 'projects.tasks']);
        return view('pages.agil365.clientes.show', compact('company'), ['title' => $company->name]);
    }

    public function edit(Company $company)
    {
        return view('pages.agil365.clientes.edit', compact('company'), ['title' => 'Editar Cliente']);
    }

    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'whatsapp'     => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'website'      => 'nullable|url|max:255',
            'country'      => 'nullable|string|max:100',
            'address'      => 'nullable|string',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('clientes')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('clientes')
            ->with('success', 'Cliente eliminado.');
    }

    /**
     * API: Quick-create a company from the project form (returns JSON)
     */
    public function quickCreate(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $company = Company::firstOrCreate(
            ['name' => trim($request->name)],
            ['is_active' => true]
        );
        return response()->json(['id' => $company->id, 'name' => $company->name]);
    }

    /**
     * API: Search companies for autocomplete (returns JSON)
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $companies = Company::where('name', 'like', "%{$q}%")
            ->where('is_active', true)
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);
        return response()->json($companies);
    }

    /**
     * Import clients from CSV file
     */
    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $bom = fread($file, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($file);
        }

        $imported = 0;
        $errors   = 0;
        $header   = null;

        while (($row = fgetcsv($file, 1000, ',')) !== false) {
            if (empty(array_filter($row))) continue;
            if (!$header) { $header = $row; continue; }

            $name = trim($row[0] ?? '');
            if (empty($name)) { $errors++; continue; }

            try {
                Company::firstOrCreate(['name' => $name], [
                    'contact_name' => trim($row[1] ?? '') ?: null,
                    'email'        => trim($row[2] ?? '') ?: null,
                    'phone'        => trim($row[3] ?? '') ?: null,
                    'country'      => trim($row[4] ?? '') ?: null,
                    'is_active'    => true,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors++;
            }
        }
        fclose($file);

        return redirect()->back()->with('success', "Importación completada: {$imported} clientes creados, {$errors} errores.");
    }
}

