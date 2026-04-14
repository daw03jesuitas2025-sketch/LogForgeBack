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
            'message'      => 'nullable|string|max:1000',
        ]);

        // CAMBIO CRÍTICO: Usamos el ID del usuario REAL autenticado
        $userId = Auth::id();

        $alreadyApplied = JobApplication::where('user_id', $userId)
            ->where('job_offer_id', $validated['job_offer_id'])
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ya te has postulado a esta oferta anteriormente.'
            ], 422);
        }

        try {
            $application = JobApplication::create([
                'user_id'      => $userId, // Guardamos el ID real
                'job_offer_id' => $validated['job_offer_id'],
                'cover_letter' => $validated['message'],
            ]);

            $application->load('jobOffer.user.companyProfile');

            return response()->json([
                'status'  => 'success',
                'message' => '¡Postulación enviada con éxito!',
                'data'    => $application
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al procesar la solicitud.'
            ], 500);
        }
    }

    public function myApplications()
    {
        $userId = Auth::id();

        $applications = JobApplication::with('jobOffer.user.companyProfile')
            ->where('user_id', $userId) // Ahora sí es privado
            ->latest()
            ->get();

        return response()->json($applications);
    }
}
