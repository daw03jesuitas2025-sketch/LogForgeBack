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

    // USUARIOS
    public function getUsers()
    {
        // Devolvemos todos los usuarios para la tabla
        return response()->json(User::all());
    }

    public function storeUser(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role' => 'required|string|in:admin,company,candidate'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            return response()->json([
                'message' => 'Usuario creado con éxito',
                'user' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno: ' . $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|string',
            'password' => 'nullable|string|min:6' // Password opcional al editar
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();
        return response()->json(['message' => 'Usuario actualizado', 'user' => $user]);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }

    public function getMessages()
    {
        // Cargamos la relación 'toUser' que definiste en el modelo Message
        $messages = \App\Models\Message::with('toUser')->latest()->get();

        return $messages->map(function ($msg) {
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

    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        return response()->json(['message' => 'Eliminado']);
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

    public function destroyOffer($id)
    {
        $offer = JobOffer::findOrFail($id);
        $offer->delete(); // Si usas SoftDeletes, se marcará como eliminada

        return response()->json(['message' => 'Oferta eliminada correctamente']);
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

    public function updateCompanyProfile(Request $request, $id)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|url',
            'description' => 'nullable|string|min:10',
        ]);

        $profile = CompanyProfile::updateOrCreate(
            ['user_id' => $id],
            $validated
        );

        return response()->json([
            'message' => 'Perfil actualizado con éxito',
            'profile' => $profile
        ]);
    }
}
