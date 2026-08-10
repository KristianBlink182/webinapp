<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VecinoApiController;

// PRUEBA BROWSER
Route::get('v1/auth/login', fn () => response()->json(['success' => true, 'status' => 'ONLINE']));

// 1. AUTENTICACIÓN
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
});

// 2. MÓDULOS PROTEGIDOS DE LA APP NATIVA
Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('/auth/me', [AuthApiController::class, 'me']);
    Route::post('/auth/logout', [AuthApiController::class, 'logout']);

    // Dashboard & SOS
    Route::get('/vecino/dashboard', [VecinoApiController::class, 'dashboard']);
    Route::post('/vecino/sos', [VecinoApiController::class, 'dispararSOS']);

    // Pagos
    Route::get('/vecino/pagos', [VecinoApiController::class, 'misPagos']);

    // Invitados
    Route::get('/vecino/invitados', [VecinoApiController::class, 'invitados']);
    Route::post('/vecino/invitados', [VecinoApiController::class, 'registrarInvitado']);

    // Comunicados
    Route::get('/vecino/comunicados', [VecinoApiController::class, 'comunicados']);

    // Marketplace
    Route::get('/vecino/marketplace', [VecinoApiController::class, 'marketplace']);
    Route::post('/vecino/marketplace', [VecinoApiController::class, 'registrarMarketplace']);

    // Votaciones
    Route::get('/vecino/votaciones', [VecinoApiController::class, 'votaciones']);

    // Documentos
    Route::get('/vecino/documentos', [VecinoApiController::class, 'documentos']);

    // Mascotas
    Route::get('/vecino/mascotas', [VecinoApiController::class, 'mascotas']);
    Route::post('/vecino/mascotas', [VecinoApiController::class, 'registrarMascota']);

    // Reclamos
    Route::get('/vecino/reclamos', [VecinoApiController::class, 'reclamos']);
    Route::post('/vecino/reclamos', [VecinoApiController::class, 'registrarReclamo']);
});