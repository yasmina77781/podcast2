<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Inscription d'un utilisateur",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"first_name","last_name","email","password","password_confirmation"},
 *             @OA\Property(property="first_name", type="string", example="Ahmed"),
 *             @OA\Property(property="last_name", type="string", example="Benani"),
 *             @OA\Property(property="email", type="string", example="ahmed@test.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="password_confirmation", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé avec succès"
 *     )
 * )
 */
    public function register(RegisterRequest $request)
    {
        if (!in_array(auth()->user()->role ?? 'user', ['animateur', 'admin'])) {
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => $request->password,
                
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Inscription réussie',
            'user' => $user,
            'token' => $token
        ];
    }
/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Connexion d'un utilisateur",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="admin@test.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Connexion réussie"
 *     )
 * )
 */

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return ['message' => 'Email ou mot de passe incorrect'];
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Connexion réussie',
            'user' => $user,
            'token' => $token
        ];
    }
/**
 * @OA\Post(
 *     path="/api/logout",
 *     summary="Déconnexion",
 *     tags={"Authentification"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Déconnexion réussie"
 *     )
 * )
 */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ['message' => 'Déconnexion réussie'];
    }
}