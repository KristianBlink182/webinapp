<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthApiController extends Controller
{
    /**
     * Iniciar Sesión desde la App Nativa Móvil
     */
    public function login(Request $request)
    {
        try {
            $email = trim($request->input('email', ''));
            $password = trim($request->input('password', ''));

            if (empty($email) || empty($password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Por favor ingrese correo y contraseña.',
                ], 422);
            }

            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales incorrectas. Verifique su correo o contraseña.',
                ], 401);
            }

            // Crear Token de Acceso Nativo de Sanctum
            $token = $user->createToken('livo_vecino_app')->plainTextToken;

            // Cargar información del Departamento y Condominio
            $dpto = $user->departamento;
            $condo = $dpto?->condominio;

            return response()->json([
                'success' => true,
                'message' => 'Bienvenido a LIVO Vecinos',
                'token'   => $token,
                'user'    => [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'email'               => $user->email,
                    'departamento_id'     => $user->departamento_id,
                    'departamento_numero' => $dpto?->numero ?? '100',
                    'condominio_id'       => $condo?->id ?? 1,
                    'condominio_nombre'   => $condo?->nombre ?? 'Edificio LIVO',
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error 500: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener Datos del Perfil del Vecino Autenticado
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento;
            $condo = $dpto?->condominio;

            return response()->json([
                'success' => true,
                'user'    => [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'email'               => $user->email,
                    'departamento_id'     => $user->departamento_id,
                    'departamento_numero' => $dpto?->numero ?? '100',
                    'condominio_id'       => $condo?->id ?? 1,
                    'condominio_nombre'   => $condo?->nombre ?? 'Edificio LIVO',
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener perfil: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cerrar Sesión en la App Nativa
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión.',
            ], 500);
        }
    }
}