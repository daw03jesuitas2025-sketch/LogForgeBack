<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    // Listar todas las habilidades disponibles en el sistema para el selector
    public function index()
    {
        // Solo enviamos lo que Angular realmente necesita para el selector
        return response()->json(Skill::select('id', 'name', 'category')->get());
    }
    // Sincronizar las habilidades del usuario (Añadir/Quitar)
    public function sync(Request $request)
    {
        $request->validate([
            'skills' => 'required|array',
            'skills.*' => 'exists:skills,id'
        ]);

        // sync() se encarga de la tabla pivote automáticamente
        Auth::user()->skills()->sync($request->skills);

        return response()->json(['message' => 'Habilidades actualizadas', 'skills' => Auth::user()->skills]);
    }
    public function destroy($id)
    {
        try {
            // Obtenemos al usuario autenticado
            $user = Auth::user();

            // En lugar de borrar la Skill de la tabla 'skills',
            // la quitamos de la relación en la tabla pivote
            $user->skills()->detach($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Habilidad quitada del perfil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al desvincular la habilidad',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
