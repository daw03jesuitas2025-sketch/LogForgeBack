<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function sendInterviewRequest(Request $request) {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'message' => 'required|string|min:10',
        ]);

        // Obtenemos el usuario autenticado para rellenar los campos obligatorios
        $sender = auth()->user();

        $message = Message::create([
            'from_user_id' => $sender->id,
            'to_user_id'   => $validated['to_user_id'],
            'from_name'    => $sender->name,
            'from_email'   => $sender->email,
            'subject'      => 'Nueva solicitud de entrevista',
            'message'      => $validated['message'],
        ]);

        return response()->json(['message' => 'Invitación enviada con éxito', 'data' => $message]);
    }
    public function getMyMessages()
    {
        // Obtenemos los mensajes donde 'to_user_id' es el ID del usuario autenticado
        // Usamos 'orderBy' para que los más nuevos salgan primero
        return Message::where('to_user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
