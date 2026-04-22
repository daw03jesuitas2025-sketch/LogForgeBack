<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobOffer;
use App\Models\CompanyProfile;

class AdminController extends Controller
{
    public function getDashboardStats()
    {
        try {
            return response()->json([
                'totalUsers' => User::count(),
                'activeOffers' => JobOffer::count(),
                'reportedMessages' => Message::count()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUsers()
    {
        // Devolvemos todos los usuarios para la tabla
        return response()->json(User::all());
    }

    //public function getJobOffers()
    //{
        // Devolvemos todas las ofertas
        //return response()->json(JobOffer::all());
    //}

   public function getMessages()
    {
        // Devolvemos los mensajes para la tabla del admin
        // Usamos with('sender') para traer los datos del usuario que envía
        return response()->json(Message::with('sender')->orderBy('created_at', 'desc')->get());
    }

    public function getJobOffers()
    {
        // Cargamos la relación 'user' para mostrar el nombre de la empresa/reclutador
        // Usamos latest() para ver las más nuevas arriba
        $offers = JobOffer::with('user')->latest()->get();

        return response()->json($offers);
    }

// Extra: Método para activar/desactivar ofertas (útil para moderación)
    public function toggleOfferStatus($id)
    {
        $offer = JobOffer::findOrFail($id);
        $offer->is_active = !$offer->is_active;
        $offer->save();

        return response()->json(['message' => 'Estado actualizado', 'is_active' => $offer->is_active]);
    }

    public function getCompanies()
    {
        try {
            // Opción A: Si quieres listar los perfiles de empresa directamente
            $companies = CompanyProfile::with('user')->get();

            // Opción B (Mejor para LinkedIn): Listar Usuarios que tengan el ROL company
            // $companies = User::where('role', 'company')->with('companyProfile')->get();

            return response()->json($companies);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    // App\Http\Controllers\Api\AdminController.php

    public function getMyProfile()
    {
        try {
            $user = auth()->user();

            // Cargamos el perfil de empresa relacionado con el usuario
            // Asegúrate de que en tu modelo User tengas la relación:
            // public function companyProfile() { return $this->hasOne(CompanyProfile::class); }
            $profile = CompanyProfile::where('user_id', $user->id)->first();

            if (!$profile) {
                return response()->json(['error' => 'Perfil no encontrado'], 404);
            }

            return response()->json($profile);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
