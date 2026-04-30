<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_offer_id' => 'required|exists:job_offers,id',
            'message'      => 'nullable|string|max:1000',
        ]);

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

    /**
     * Para el Candidato: Ver sus propias postulaciones
     */
    public function myApplications()
    {
        $userId = Auth::id();

        $applications = JobApplication::with(['jobOffer.user.companyProfile'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        return response()->json($applications);
    }

    /**
     * Para la Empresa: Ver candidatos de una oferta específica
     */
    public function getApplicantsByOffer($offerId)
    {
        // Verificamos que la oferta pertenezca a la empresa que pregunta
        $offer = JobOffer::where('id', $offerId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $applications = JobApplication::with(['user']) // Cargamos los datos del alumno
        ->where('job_offer_id', $offerId)
            ->get();

        return response()->json($applications);
    }
}
