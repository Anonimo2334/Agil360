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
}
