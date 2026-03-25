<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = [
            'clientes.ver' => 'Ver Clientes',
            'clientes.crear' => 'Crear Clientes',
            'clientes.editar' => 'Editar Clientes',
            'clientes.eliminar' => 'Eliminar Clientes',
            'proyectos.ver' => 'Ver Proyectos',
            'proyectos.crear' => 'Crear Proyectos',
            'proyectos.editar' => 'Editar Proyectos',
            'proyectos.eliminar' => 'Eliminar Proyectos',
            'tareas.ver' => 'Ver Tareas',
            'tareas.crear' => 'Crear Tareas',
            'tareas.editar' => 'Editar Tareas',
            'tareas.eliminar' => 'Eliminar Tareas',
            'usuarios.gestionar' => 'Gestionar Usuarios',
            'roles.gestionar' => 'Gestionar Roles',
            'reportes.ver' => 'Ver Reportes',
        ];

        return view('pages.agil365.roles', [
            'title' => 'Roles y Permisos',
            'roles' => $roles,
            'availablePermissions' => $permissions
        ]);
    }

    public function update(Request $request, Role $role)
    {
        // Administradores can edit permissions
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($role->slug === 'super_admin') {
            return redirect()->route('roles')->with('error', 'El rol de Super Administrador no puede ser modificado.');
        }

        $validated = $request->validate([
            'permissions' => 'array'
        ]);

        $role->update([
            'permissions' => $validated['permissions'] ?? []
        ]);

        return redirect()->route('roles')->with('success', 'Permisos actualizados correctamente.');
    }
}
