<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','string','min:6','confirmed'],
            'role' => ['required', 'in:user,company'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Lógica de Perfiles Automática
        if ($user->role === 'company') {
            $user->companyProfile()->create([
                'company_name' => $user->name,
                'description' => 'Nueva empresa en la plataforma',
            ]);
        } else {
            $user->profile()->create([
                'title' => 'Buscando nuevas oportunidades',
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado correctamente',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'password' => ['required','string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciales incorrectas.'],
            ]);
        }

        // borra tokens anteriores
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login correcto',
            'user' => $user,
            'token' => $token
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout correcto'
        ]);
    }

    // USER LOGGED
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    public function getSuggestions()
    {
        $currentUserId = auth()->id();

        // Filtramos:
        // 1. Que no sea el usuario actual
        // 2. Que el rol sea 'user' (ajusta esto según tus nombres de roles)
        // 3. Limitamos a 5 para que no sea una lista infinita
        $users = User::where('id', '!=', $currentUserId)
            ->where('role', 'user')
            ->limit(5)
            ->get();

        return response()->json($users);
    }
}
