<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExperienceController extends Controller
{
    /**
     * Muestra la lista de experiencias del usuario autenticado.
     */
    public function index()
    {
        return response()->json(Auth::user()->experiences()->orderBy('start_date', 'desc')->get());
    }

    /**
     * Guarda una nueva experiencia (Crear).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company'     => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        // Se crea vinculada automáticamente al usuario logueado
        $experience = Auth::user()->experiences()->create($validated);

        return response()->json($experience, 211);
    }

    /**
     * Actualiza una experiencia existente (Editar).
     */
    public function update(Request $request, Experience $experience)
    {
        // Verificar que la experiencia pertenece al usuario
        if ($experience->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $validated = $request->validate([
            'company'     => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $experience->update($validated);

        return response()->json($experience);
    }

    /**
     * Elimina una experiencia.
     */
    public function destroy(Experience $experience)
    {
        if ($experience->user_id !== Auth::id()) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $experience->delete();

        return response()->json(['message' => 'Experiencia eliminada correctamente']);
    }
}
