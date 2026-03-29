<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->query('tab', 'profile'); // Defaults to 'profile'
        
        // Cargar modelo si no existe la relacion lo creamos dummy o solo consultamos
        // Para esto necesitamos tener googleCalendarIntegration en el User model. 
        $googleIntegration = \App\Models\GoogleCalendarIntegration::where('user_id', $user->id)->first();

        return view('pages.agil365.profile.index', compact('user', 'tab', 'googleIntegration'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            // 'avatar' => 'nullable|image|max:2048', // If they want profile picture handling later
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('profile.index', ['tab' => 'profile'])->with('success', 'Perfil actualizado exitosamente.');
    }

    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.index', ['tab' => 'settings'])->with('success', 'Contraseña actualizada exitosamente.');
    }
}
