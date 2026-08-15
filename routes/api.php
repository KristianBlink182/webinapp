<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\VecinoApiController;

/*
|--------------------------------------------------------------------------
| RUTAS API RESTFUL - APP NATIVA LIVO VECINOS (ACCESO DIRECTO)
|--------------------------------------------------------------------------
*/

// RUTA DE PRUEBA EN NAVEGADOR
Route::get('/v1/test/pagos', function () {
    $pagos = \App\Models\Pago::orderBy('created_at', 'desc')->get();
    return response()->json([
        'success' => true,
        'total_recibos_bd' => $pagos->count(),
        'data' => $pagos,
    ]);
});

// 1. AUTENTICACIÓN
Route::prefix('v1/auth')->group(function () {
    Route::post('/login', [AuthApiController::class, 'login']);
});

// 2. MÓDULOS DE VECINO (RESPUESTA DIRECTA GARANTIZADA)
Route::prefix('v1')->group(function () {
    // Dashboard & SOS
    Route::get('/vecino/dashboard', [VecinoApiController::class, 'dashboard']);
    Route::post('/vecino/sos', [VecinoApiController::class, 'dispararSOS']);

    // Pagos & Recibos
    Route::get('/vecino/pagos', [VecinoApiController::class, 'misPagos']);
    Route::post('/vecino/pagos/reportar', [VecinoApiController::class, 'reportarPago']);
    Route::get('/vecino/pagos/{id}/pdf', [VecinoApiController::class, 'descargarPdf']);


    // Invitados (Portería)
    Route::get('/vecino/invitados', [VecinoApiController::class, 'invitados']);
    Route::post('/vecino/invitados', [VecinoApiController::class, 'registrarInvitado']);

    // Áreas Comunes & Cámara
    Route::get('/vecino/areas-comunes', [VecinoApiController::class, 'areasComunes']);
    Route::get('/vecino/camara', [VecinoApiController::class, 'camara']);
    Route::post('/vecino/areas-comunes/reservar', [VecinoApiController::class, 'reservarAreaComun']);

    // Comunicados, Marketplace, Votaciones, Documentos
    Route::get('/vecino/comunicados', [VecinoApiController::class, 'comunicados']);
    Route::get('/vecino/marketplace', [VecinoApiController::class, 'marketplace']);
    Route::post('/vecino/marketplace', [VecinoApiController::class, 'registrarMarketplace']);
    Route::get('/vecino/votaciones', [VecinoApiController::class, 'votaciones']);
    Route::get('/vecino/documentos', [VecinoApiController::class, 'documentos']);

    // Mascotas
    Route::get('/vecino/mascotas', [VecinoApiController::class, 'mascotas']);
    Route::post('/vecino/mascotas', [VecinoApiController::class, 'registrarMascota']);

    // Reclamos
    Route::get('/vecino/reclamos', [VecinoApiController::class, 'reclamos']);
    Route::post('/vecino/reclamos', [VecinoApiController::class, 'registrarReclamo']);
});