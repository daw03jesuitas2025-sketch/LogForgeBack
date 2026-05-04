<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->projects()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'repo_url'    => 'nullable|url',
        ]);

        $project = Auth::user()->projects()->create($validated);
        return response()->json($project, 201);
    }

    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'repo_url'    => 'nullable|url',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

// App\Http\Controllers\Api\ProjectController.php

    public function destroy($id)
    {
        // Buscamos el proyecto manualmente por ID
        $project = Project::find($id);

        // Si no lo encuentra, devolvemos un mensaje claro para saber que entró aquí
        if (!$project) {
            return response()->json([
                'message' => "No se encontró el proyecto con ID: {$id}"
            ], 404);
        }

        // Verificamos que el usuario logueado sea el dueño (ID 4 en tu caso)
        if ($project->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'No tienes permiso para borrar este proyecto',
                'debug' => [
                    'project_owner' => $project->user_id,
                    'current_user' => Auth::id()
                ]
            ], 403);
        }

        $project->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente'
        ]);
    }
}
