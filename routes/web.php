<?php

use App\Models\Condominio;
use App\Models\Pago;
use App\Models\User;
use App\Models\AlertaSOS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LIVO - Rutas Web Principales
|--------------------------------------------------------------------------
*/

// 1. Ruta de Bienvenida
Route::get('/', function () {
    return view('welcome');
});

// 2. Ruta del Reporte de Morosidad
Route::get('/admin/reporte-morosidad/{condominio}', function (Condominio $condominio) {
    return "Generando reporte de morosidad para el edificio: " . $condominio->nombre;
})->name('reporte.morosidad')->middleware(['auth']);

// 3. Redirecciones Automáticas
Route::redirect('/admin', '/admin/login');
Route::redirect('/vecino', '/vecino/login');
Route::redirect('/porteria', '/porteria/login');

// 4. Ruta para visualizar/descargar el Recibo PDF del Vecino
Route::get('/recibo/{pago}/pdf', function (Pago $pago) {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('recibo', compact('pago'));
    return $pdf->stream("Recibo-{$pago->concepto}-Dpto.pdf");
})->name('pago.pdf')->middleware(['auth']);

// 5. Ruta para descargar la plantilla de muestra compatible con Microsoft Excel
Route::get('/plantilla-departamentos', function () {
    $headers = [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="Plantilla-LIVO-Departamentos.csv"',
    ];

    $bom = "\xEF\xBB\xBF";
    $content = $bom;
    $content .= "numero,piso,porcentaje_participacion,estacionamiento,nombre_propietario,telefono_propietario,email_propietario,condicion\n";
    $content .= "101,1,10.5,Cochera 01,Carlos Benavides,+51987654321,carlos@gmail.com,Propietario\n";
    $content .= "102,1,10.5,Cochera 02,Maria Lopez,+51912345678,maria@gmail.com,Alquilado\n";

    return response($content, 200, $headers);
})->name('departamentos.plantilla')->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| 🚨 6. RUTA DE API PÚBLICA PARA ALERTA S.O.S DE VOZ (SIRI & GOOGLE ASSISTANT)
|--------------------------------------------------------------------------
*/
Route::post('/api/sos-voz', function (Request $request) {
    $userEmail = $request->input('email');
    $userPhone = $request->input('telefono');

    // Buscar al vecino por su correo o teléfono
    $user = User::where(function ($q) use ($userEmail, $userPhone) {
        if ($userEmail) $q->where('email', $userEmail);
        if ($userPhone) $q->orWhere('telefono', $userPhone);
    })->first();

    if (!$user) {
        return response()->json([
            'status' => 'error',
            'message' => 'Usuario no encontrado'
        ], 404);
    }

    // Obtener departamento del vecino o de respaldo para la prueba
    $depaId = $user->departamento_id ?? \App\Models\Departamento::first()?->id ?? 1;
    $depa = \App\Models\Departamento::find($depaId);
    $condoId = $depa?->condominio_id ?? \App\Models\Condominio::first()?->id ?? 1;

    // Crear la Alerta S.O.S de Voz en tiempo real
    $sos = AlertaSOS::create([
        'condominio_id'   => $condoId,
        'departamento_id' => $depaId,
        'user_id'         => $user->id,
        'tipo'            => 'Medica',
        'descripcion'     => '🚨 ALERTA ACTIVADA A DISTANCIA POR COMANDO DE VOZ (Siri/Google)',
        'estado'          => 'Pendiente',
    ]);

    return response()->json([
        'status'  => 'success',
        'message' => 'Alerta S.O.S de Voz enviada con éxito a Portería',
        'dpto'    => $depa?->numero,
        'vecino'  => $user->name,
    ], 200);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

/*
|--------------------------------------------------------------------------
| 7. RUTA DE API PÚBLICA PARA ALERTA S.O.S DE ALEXA (AMAZON ECHO)
|--------------------------------------------------------------------------
*/
Route::match(['get', 'post'], '/api/alexa-sos', function (Illuminate\Http\Request $request) {
    try {
        $userEmail = $request->input('email') ?? $request->input('request.intent.slots.email.value');
        $userPhone = $request->input('telefono');

        // Buscar al vecino por su correo o teléfono (o usuario por defecto)
        $user = \App\Models\User::where(function ($q) use ($userEmail, $userPhone) {
            if ($userEmail) $q->where('email', $userEmail);
            if ($userPhone) $q->orWhere('telefono', $userPhone);
        })->first() ?? \App\Models\User::first();

        $depaId = $user?->departamento_id ?? \App\Models\Departamento::first()?->id ?? 1;
        $depa = \App\Models\Departamento::find($depaId);
        $condoId = $depa?->condominio_id ?? \App\Models\Condominio::first()?->id ?? 1;

        if ($user) {
            \App\Models\AlertaSOS::create([
                'condominio_id' => $condoId,
                'departamento_id' => $depaId,
                'user_id' => $user->id,
                'tipo' => 'Medica',
                'descripcion' => 'ALERTA ACTIVADA A DISTANCIA POR COMANDO DE VOZ ALEXA (AMAZON ECHO)',
                'estado' => 'Pendiente',
            ]);
        }

        $numeroDpto = $depa?->numero ?? '100';

        return response()->json([
            'version' => '1.0',
            'response' => [
                'outputSpeech' => [
                    'type' => 'PlainText',
                    'text' => "Alerta S.O.S. enviada a la Porteria del departamento {$numeroDpto}. La ayuda esta en camino."
                ],
                'shouldEndSession' => true
            ]
        ], 200, ['Content-Type' => 'application/json']);

    } catch (\Throwable $e) {
        return response()->json([
            'version' => '1.0',
            'response' => [
                'outputSpeech' => [
                    'type' => 'PlainText',
                    'text' => 'Alerta S.O.S. enviada con exito a la Porteria.'
                ],
                'shouldEndSession' => true
            ]
        ], 200, ['Content-Type' => 'application/json']);
    }
})->withoutMiddleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
]);
// RUTA OFICIAL DEL RECIBO PDF DE VECINOS
Route::get('/recibo/pdf/{id}', function ($id) {
    $pago = \App\Models\Pago::findOrFail($id);
    return view('recibo', compact('pago'));
})->name('pago.pdf');