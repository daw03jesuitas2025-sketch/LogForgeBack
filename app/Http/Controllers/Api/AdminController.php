<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\JobOffer;

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
        // Traemos el perfil de empresa cargando la relación 'user'
        // Esto evita el problema de las N+1 consultas
        $companies = CompanyProfile::with('user')->get();

        return response()->json($companies);
    }
}
