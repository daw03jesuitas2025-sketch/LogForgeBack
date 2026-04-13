<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication; // <--- ¡ESTO ES VITAL!
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_offer_id' => 'required|exists:job_offers,id',
            'message'      => 'nullable|string' // En el front mandaremos 'message'
        ]);

        // Usamos el ID del usuario logueado (puedes usar auth()->id() o $request->user()->id)
        $application = JobApplication::create([
            'user_id'      => Auth::id() ?? 1, // Si no hay login aún, usamos el 1 para pruebas
            'job_offer_id' => $validated['job_offer_id'],
            'cover_letter' => $validated['message'] // Guardamos 'message' en 'cover_letter'
        ]);

        return response()->json([
            'message' => 'Postulación enviada con éxito',
            'data'    => $application
        ], 201);
    }
    public function myApplications()
    {
        // Por ahora usamos el ID 1 para pruebas, luego será Auth::id()
        $userId = 1;

        // Traemos las postulaciones con la relación de la oferta de trabajo
        $applications = JobApplication::with('jobOffer.user.companyProfile')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json($applications);
    }
}
