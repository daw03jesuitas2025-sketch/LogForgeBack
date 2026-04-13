<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyProfile;

class JobOfferController extends Controller
{
    // Listar todas las ofertas (GET /api/job-offers)
    public function index()
    {
        // Traemos también los datos de la empresa (relación company)
        return response()->json(JobOffer::with('user.companyProfile')->where('is_active', true)->latest()->get());    }

    // Crear oferta (POST /api/job-offers)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
        ]);

        $offer = JobOffer::create($validated + [
                'is_active' => true,
                'user_id' => Auth::id()
            ]);
        return response()->json($offer, 201);
    }

    // Ver una oferta de trabajo (GET /api/job-offers/{id})
    public function show(JobOffer $jobOffer)
    {
        return response()->json($jobOffer->load('company'));
    }

    // Actualizar (PUT /api/job-offers/{id})
    public function update(Request $request, JobOffer $jobOffer)
    {
        $jobOffer->update($request->all());
        return response()->json($jobOffer);
    }

    // Borrar (DELETE /api/job-offers/{id})
    public function destroy(JobOffer $jobOffer)
    {
        $jobOffer->delete();
        return response()->json(['message' => 'Eliminado']);
    }
}
