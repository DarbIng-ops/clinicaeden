<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Mostrar la vista de perfil del usuario autenticado.
     */
    public function show()
    {
        // Ruta de regreso según el rol del usuario
        $backRoute = match(Auth::user()->role ?? '') {
            'admin'                => route('admin.dashboard'),
            'recepcionista'        => route('recepcion.dashboard'),
            'medico_general'       => route('medico_general.dashboard'),
            'medico_especialista'  => route('medico_especialista.dashboard'),
            default                => url('/dashboard'),
        };

        return view('profile.show', compact('backRoute'));
    }

    /**
     * Actualizar información personal del perfil.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'                          => 'required|string|max:255',
            'apellido'                      => 'required|string|max:255',
            'dni'                           => 'required|string|max:20|unique:users,dni,' . $user->id,
            'fecha_nacimiento'              => 'nullable|date|before:today',
            'sexo'                          => 'nullable|in:M,F,O',
            'telefono'                      => 'nullable|string|max:30',
            'tipo_sangre'                   => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'direccion'                     => 'nullable|string|max:255',
            'contacto_emergencia_nombre'    => 'nullable|string|max:255',
            'contacto_emergencia_telefono'  => 'nullable|string|max:30',
        ]);

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    /**
     * Cambiar la contraseña del usuario autenticado.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.'])
                         ->withInput();
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    /**
     * Actualizar la foto de perfil del usuario.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Eliminar foto anterior si existe
        $previous = Auth::user()->profile_photo_path;
        if ($previous && Storage::disk('public')->exists($previous)) {
            Storage::disk('public')->delete($previous);
        }

        $path = $request->file('photo')->store('profile-photos', 'public');

        Auth::user()->update(['profile_photo_path' => $path]);

        return back()->with('success', 'Foto de perfil actualizada.');
    }
}
