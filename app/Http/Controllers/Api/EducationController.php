<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EducationController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->educations()->orderBy('start_date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree'      => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $education = Auth::user()->educations()->create($validated);
        return response()->json($education, 201);
    }

    public function show(Education $education)
    {
        if ($education->user_id !== Auth::id()) return response()->json(['message' => 'No autorizado'], 403);
        return response()->json($education);
    }

    public function update(Request $request, Education $education)
    {
        if ($education->user_id !== Auth::id()) return response()->json(['message' => 'No autorizado'], 403);

        $validated = $request->validate([
            'institution' => 'required|string|max:255',
            'degree'      => 'required|string|max:255',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $education->update($validated);
        return response()->json($education);
    }

    public function destroy(Education $education)
    {
        if ($education->user_id !== Auth::id()) return response()->json(['message' => 'No autorizado'], 403);
        $education->delete();
        return response()->json(['message' => 'Educación eliminada']);
    }
}
