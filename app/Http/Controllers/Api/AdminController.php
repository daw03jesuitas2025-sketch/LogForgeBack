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
        // Cargamos la relación 'toUser' que definiste en el modelo Message
        $messages = \App\Models\Message::with('toUser')->latest()->get();

        return $messages->map(function($msg) {
            return [
                'id' => $msg->id,
                'from_name' => $msg->from_name,
                'from_email' => $msg->from_email,
                'from_user_id' => $msg->from_user_id,
                'to_user_id' => $msg->to_user_id,
                'to_name' => $msg->toUser ? $msg->toUser->name : 'Usuario no encontrado',
                'subject' => $msg->subject,
                'message' => $msg->message,
                'created_at' => $msg->created_at,
            ];
        });
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
}
