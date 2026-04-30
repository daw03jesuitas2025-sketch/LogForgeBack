<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobOffer;
use App\Models\CompanyProfile;
use App\Models\User;

class CompanyController extends Controller
{
    /**
     * Obtener el perfil de la empresa logueada
     */
    public function getMyProfile()
    {
        try {
            $user = auth()->user();
            $profile = CompanyProfile::where('user_id', $user->id)->first();

            // Si no existe, devolvemos un objeto con campos vacíos en lugar de un error 404
            if (!$profile) {
                return response()->json([
                    'company_name' => '',
                    'website' => '',
                    'description' => '',
                    'user_id' => $user->id
                ], 200);
            }

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener solo las ofertas de la empresa logueada
     */
    public function getMyOffers()
    {
        try {
            $user = auth()->user();
            // Filtramos por user_id para que la empresa no vea ofertas de otros
            $offers = JobOffer::where('user_id', $user->id)->latest()->get();
            return response()->json($offers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar perfil de empresa
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();

            // 1. Validamos los datos
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'website'      => 'nullable|url',
                'description'  => 'nullable|string',
                'logo'         => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validación de imagen
            ]);

            // 2. Buscamos el perfil existente
            $profile = CompanyProfile::where('user_id', $user->id)->first();

            // 3. Lógica para procesar la imagen si viene en la petición
            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $fileName = time() . '_' . $file->getClientOriginalName();

                // Guardamos físicamente el archivo en public/logos
                $file->move(public_path('logos'), $fileName);

                // Guardamos la ruta que se almacenará en la DB (String)
                $validated['logo'] = '/logos/' . $fileName;
            }

            // 4. Actualizamos o creamos el perfil
            $profile = CompanyProfile::updateOrCreate(
                ['user_id' => $user->id],
                $validated
            );

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getCandidates() {
        // Filtramos para que solo devuelva usuarios con rol 'user'
        return User::where('role', 'user')->with('profile')->get();
    }
}
