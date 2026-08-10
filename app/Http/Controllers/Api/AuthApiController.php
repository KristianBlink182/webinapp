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
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas. Verifique su correo o contraseña.',
            ], 401);
        }

        // Crear Token de Acceso Nativo de Sanctum
        $token = $user->createToken('livo_vecino_app')->plainTextToken;

        // Cargar información del Departamento y Condominio
        $dpto = $user->departamento;
        $condominio = $dpto?->condominio;

        return response()->json([
            'success' => true,
            'message' => 'Bienvenido a LIVO Vecinos',
            'token'   => $token,
            'user'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'departamento_id'     => $user->departamento_id,
                'departamento_numero' => $dpto?->numero ?? 'S/N',
                'condominio_id'       => $condominio?->id,
                'condominio_nombre'   => $condominio?->nombre ?? 'Edificio LIVO',
            ]
        ], 200);
    }

    /**
     * Obtener Datos del Perfil del Vecino Autenticado
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $dpto = $user->departamento;
        $condominio = $dpto?->condominio;

        return response()->json([
            'success' => true,
            'user'    => [
                'id'                  => $user->id,
                'name'                => $user->name,
                'email'               => $user->email,
                'departamento_id'     => $user->departamento_id,
                'departamento_numero' => $dpto?->numero ?? 'S/N',
                'condominio_id'       => $condominio?->id,
                'condominio_nombre'   => $condominio?->nombre ?? 'Edificio LIVO',
            ]
        ], 200);
    }

    /**
     * Cerrar Sesión en la App Nativa
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente.',
        ], 200);
    }
}