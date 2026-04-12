<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $profile = Auth::user()->profile;

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'biography'        => 'nullable|string',
            'location'         => 'nullable|string|max:255',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $profile->update($validated);
        return response()->json($profile);
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
}
