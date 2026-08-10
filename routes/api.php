<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VecinoApiController;

/*
|--------------------------------------------------------------------------
| RUTAS API RESTFUL - APP NATIVA LIVO VECINOS
|--------------------------------------------------------------------------
*/

// RUTA DE PRUEBA EN BARRAS DE NAVEGADOR (GET)
Route::get('v1/auth/login', function () {
    return response()->json([
        'success' => true,
        'message' => 'API RESTful LIVO Vecinos v1 Operativa. Utilice el método POST para iniciar sesión.',
        'status'  => 'ONLINE',
    ]);
});

// 1. RUTAS PÚBLICAS (POST)
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
});

// 2. RUTAS PROTEGIDAS CON TOKEN DE SANCTUM (VECINOS)
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // Autenticación
    Route::get('/auth/me', [AuthApiController::class, 'me']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    // Dashboard & Módulos Vecino
    Route::get('/vecino/dashboard', [VecinoApiController::class, 'dashboard']);
    Route::get('/vecino/pagos', [VecinoApiController::class, 'misPagos']);
    Route::post('/vecino/pagos/{id}/reportar', [VecinoApiController::class, 'reportarPago']);
    Route::post('/vecino/sos', [VecinoApiController::class, 'dispararSOS']);
    Route::get('/vecino/comunicados', [VecinoApiController::class, 'comunicados']);
    Route::get('/vecino/mascotas', [VecinoApiController::class, 'mascotas']);
    Route::get('/vecino/reclamos', [VecinoApiController::class, 'reclamos']);
});