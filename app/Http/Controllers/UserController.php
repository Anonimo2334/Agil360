<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
        }
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === '1');
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('pages.agil365.usuarios.index', compact('users', 'roles'), ['title' => 'Usuarios']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:8|confirmed',
            'role_id'    => 'nullable|exists:roles,id',
            'phone'      => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        User::create($validated);

        return redirect()->route('usuarios')->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role_id'    => 'nullable|exists:roles,id',
            'phone'      => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'is_active'  => 'boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return redirect()->route('usuarios')->with('success', 'Usuario actualizado.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Usuario {$status}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $user->delete();
        return redirect()->route('usuarios')->with('success', 'Usuario eliminado.');
    }
}
