<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Profile;

class ProfileController extends Controller
{
    // Obtener el perfil completo para mostrar en Angular (LinkedIn View)
    public function show()
    {
        // Cargamos al usuario con sus 3 relaciones clave
        $user = auth()->user()->load(['profile','educations', 'experiences', 'skills']);

        return response()->json($user);
    }
    // Actualizar datos básicos (Titular, Bio, Ubicación)
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Validamos. Si falla aquí, Laravel devuelve un 422 automáticamente (no un 500)
            $request->validate([
                'name'      => 'required|string|max:255',
                'title'     => 'nullable|string|max:255',
                'biography' => 'nullable|string',
                'location'  => 'nullable|string|max:255',
            ]);

            // 1. Actualizar el nombre del usuario (tabla users)
            $user->update([
                'name' => $request->input('name')
            ]);

            // 2. Actualizar o crear el perfil (tabla profiles)
            // Usamos input() para evitar el error "Undefined array key"
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'title'     => $request->input('title'),
                    'biography' => $request->input('biography'),
                    'location'  => $request->input('location'),
                ]
            );

            // 3. Retornar el usuario con sus relaciones para que Angular se refresque
            return response()->json($user->load(['profile', 'educations', 'experiences', 'skills']));

        } catch (\Exception $e) {
            // Si algo falla, ahora nos dirá exactamente el qué en la consola
            return response()->json([
                'error' => 'Error en el servidor',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    public function addSkill(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $skill = \App\Models\Skill::firstOrCreate(['name' => $validated['name']]);

        // La asociamos al usuario (evitando duplicados con syncWithoutDetaching)
        auth()->user()->skills()->syncWithoutDetaching([$skill->id]);

        return response()->json($skill, 201);
    }

    // Generar y descargar un PDF con el perfil completo del usuario
    public function resume()
    {
        // Usamos load() para traer la información más reciente de la base de datos
        $user = auth()->user()->load(['profile', 'educations', 'experiences', 'skills']);

        // Importante: Si acabas de actualizar el perfil en la misma sesión,
        // a veces es necesario refrescar el modelo
        $user->refresh();

        $pdf = Pdf::loadView('pdf.resume', ['user' => $user]);

        return $pdf->download('resume_' . $user->id . '.pdf');
    }
}
