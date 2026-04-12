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
            'demo_url'    => 'nullable|url',
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
            'demo_url'    => 'nullable|url',
        ]);

        $project->update($validated);
        return response()->json($project);
    }

    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) return response()->json(['message' => 'No autorizado'], 403);
        $project->delete();
        return response()->json(['message' => 'Proyecto eliminado']);
    }
}
