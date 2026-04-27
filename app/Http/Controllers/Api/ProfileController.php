<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Barryvdh\DomPDF\Facade\Pdf;

class ProfileController extends Controller
{
    // Obtener el perfil completo para mostrar en Angular (LinkedIn View)
    public function show()
    {
        // Cargamos al usuario con sus 3 relaciones clave
        $user = auth()->user()->load(['educations', 'experiences', 'skills']);

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

    public function downloadResume()
    {
        try {
            $user = auth()->user()->load(['profile', 'experiences', 'educations', 'skills']);

            // ELIMINA O COMENTA ESTE BLOQUE:
            /*
            if (!$user->profile) {
                return response()->json(['error' => 'Perfil no encontrado'], 404);
            }
            */

            $pdf = Pdf::loadView('pdf.resume', compact('user'));
            return $pdf->stream('cv_'.$user->name.'.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
