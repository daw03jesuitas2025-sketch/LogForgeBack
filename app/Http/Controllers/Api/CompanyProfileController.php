<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobOffer;
use App\Models\CompanyProfile;
use App\Models\User;

class CompanyController extends Controller
{
    // Obtener ofertas de la empresa logueada
    public function getMyOffers()
    {
        $offers = JobOffer::where('user_id', auth()->id())->latest()->get();
        return response()->json($offers);
    }

    // Obtener perfil de la empresa
    public function getMyProfile()
    {
        $profile = CompanyProfile::where('user_id', auth()->id())->first();

        if (!$profile) {
            return response()->json(['error' => 'Perfil no encontrado'], 404);
        }

        return response()->json($profile);
    }

    // Actualizar perfil
    public function updateProfile(Request $request)
    {
        $profile = CompanyProfile::where('user_id', auth()->id())->first();

        $profile->update([
            'company_name' => $request->company_name,
            'website' => $request->website,
            'description' => $request->description,
        ]);

        return response()->json($profile);
    }

    // Obtener candidatos
    public function getCandidates()
    {
        // ejemplo simple: usuarios que NO son empresa
        $candidates = User::where('role', 'user')->get();

        return response()->json($candidates);
    }
}