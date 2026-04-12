<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// ESTO ES LO QUE SUELE FALTAR:
use App\Models\User;
use App\Models\JobOffer;

class AdminController extends Controller
{
    public function getDashboardStats()
    {
        try {
            return response()->json([
                'totalUsers' => User::count(),
                'activeOffers' => JobOffer::count(), // O JobOffer::where('is_active', 1)->count()
                'reportedMessages' => 0
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

    public function getJobOffers()
    {
        // Devolvemos todas las ofertas
        return response()->json(JobOffer::all());
    }
}
