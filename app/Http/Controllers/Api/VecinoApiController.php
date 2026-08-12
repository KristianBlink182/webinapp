<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Comunicado;
use App\Models\AlertaSOS;
use App\Models\Mascota;
use App\Models\Reclamo;
use App\Models\Visita;
use App\Models\Anuncio;
use App\Models\Votacion;
use App\Models\Documento;
use App\Models\AreaComun;
use Illuminate\Http\Request;

class VecinoApiController extends Controller
{
    /** 1. DASHBOARD PRINCIPAL - SINCRONIZADO EN TIEMPO REAL CON LA WEB */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento ?? \App\Models\Departamento::find($user->departamento_id);
            $condominio = $dpto?->condominio;
            $dptoId = $dpto?->id ?? $user->departamento_id;

            $saldoPendiente = Pago::where(function ($q) use ($user, $dptoId) {
                    if ($dptoId) { $q->where('departamento_id', $dptoId); }
                    $q->orWhere('user_id', $user->id);
                })
                ->whereIn('estado', ['Pendiente', 'pendiente'])
                ->sum('monto') ?? 0;

            $ultimoComunicado = Comunicado::where('condominio_id', $condominio?->id)->latest()->first();
            $alertaActiva = AlertaSOS::where('departamento_id', $dptoId)->where('estado', 'Pendiente')->first();

            return response()->json([
                'success' => true,
                'data'    => [
                    'vecino_nombre'       => $user->name,
                    'departamento_numero' => $dpto?->numero ?? '100',
                    'condominio_nombre'   => $condominio?->nombre ?? 'Jorge Chavez',
                    'url_camara'          => $condominio?->url_camara_principal ?? 'https://www.youtube.com/embed/live_stream',
                    'estado_cuenta'       => [
                        'monto_pendiente'  => (float) $saldoPendiente,
                        'monto_formateado' => 'S/ ' . number_format((float)$saldoPendiente, 2, '.', ''),
                        'esta_al_dia'      => $saldoPendiente <= 0,
                    ],
                    'ultimo_comunicado'   => $ultimoComunicado ? [
                        'id'        => $ultimoComunicado->id,
                        'titulo'    => $ultimoComunicado->titulo,
                        'contenido' => $ultimoComunicado->contenido,
                        'fecha'     => $ultimoComunicado->created_at->format('d/m/Y'),
                    ] : null,
                    'alerta_sos_activa'   => $alertaActiva ? true : false,
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 2. MIS PAGOS Y RECIBOS - JALA TODOS LOS RECIBOS DE ENERO, FEBRERO Y MESES EMITIDOS */
    public function misPagos(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento ?? \App\Models\Departamento::find($user->departamento_id);
            $dptoId = $dpto?->id ?? $user->departamento_id;

            $pagos = Pago::where(function ($q) use ($user, $dptoId) {
                    if ($dptoId) { $q->where('departamento_id', $dptoId); }
                    $q->orWhere('user_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($pago) {
                    return [
                        'id'               => $pago->id,
                        'concepto'         => $pago->concepto ?? ("Cuota de Mantenimiento - " . ($pago->mes ?? '') . " " . ($pago->anio ?? '')),
                        'monto_total'      => (float) $pago->monto,
                        'monto_formateado' => 'S/ ' . number_format((float)$pago->monto, 2, '.', ''),
                        'estado'           => $pago->estado ?? 'Pendiente',
                        'fecha_vencimiento' => '12 de cada mes',
                    ];
                });

            return response()->json(['success' => true, 'data' => $pagos], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 3. DISPARAR S.O.S. (ENVÍA ALERTA ROJA EN TIEMPO REAL A PORTERÍA) */
    public function dispararSOS(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento ?? \App\Models\Departamento::find($user->departamento_id);
            $condoId = $dpto?->condominio_id ?? 1;

            $alerta = AlertaSOS::create([
                'condominio_id'   => $condoId,
                'departamento_id' => $dpto?->id ?? $user->departamento_id ?? 1,
                'user_id'         => $user->id,
                'tipo'            => 'S.O.S. App Nativa',
                'descripcion'     => "¡ALERTA S.O.S! El residente {$user->name} del Dpto. {$dpto?->numero} requiere asistencia inmediata.",
                'estado'          => 'Pendiente',
            ]);

            return response()->json([
                'success' => true,
                'message' => '¡Alerta S.O.S. enviada a Portería! La ayuda está en camino.',
                'alerta'  => $alerta,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error SOS: ' . $e->getMessage()], 500);
        }
    }

    /** 4. INVITADOS */
    public function invitados(Request $request)
    {
        $user = $request->user();
        $invitados = Visita::where('departamento_id', $user->departamento_id)->latest()->get()->map(function ($v) {
            return [
                'id'               => $v->id,
                'nombre_visitante' => $v->nombre_visitante ?? 'Invitado',
                'dni_visitante'    => $v->dni_visitante ?? 'S/D',
                'tipo_visita'      => $v->tipo_visita ?? 'Peatonal',
                'estado'           => $v->estado ?? 'Pre-Autorizado',
                'fecha'            => $v->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json(['success' => true, 'data' => $invitados], 200);
    }

    public function registrarInvitado(Request $request)
    {
        try {
            $user = $request->user();
            $visita = Visita::create([
                'condominio_id'    => $user->departamento?->condominio_id ?? 1,
                'departamento_id' => $user->departamento_id ?? 1,
                'user_id'          => $user->id,
                'nombre_visitante' => $request->input('nombre'),
                'dni_visitante'    => $request->input('dni', 'S/D'),
                'tipo_visita'      => $request->input('tipo', 'Peatonal'),
                'estado'           => 'Pre-Autorizado',
            ]);

            return response()->json(['success' => true, 'message' => 'Invitado pre-autorizado con éxito. Ya figura en Portería.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 5. ÁREAS COMUNES */
    public function areasComunes(Request $request)
    {
        $user = $request->user();
        $condoId = $user->departamento?->condominio_id ?? 1;

        $areas = AreaComun::where('condominio_id', $condoId)->get()->map(function ($a) {
            return [
                'id'          => $a->id,
                'nombre'      => $a->nombre ?? 'Área Común',
                'descripcion' => $a->descripcion ?? 'Parrillas, SUM, Gimnasio',
                'capacidad'   => $a->capacidad ?? '10 personas',
                'costo'       => 'S/ ' . number_format((float)($a->costo_reserva ?? 0), 2),
                'estado'      => $a->estado ?? 'Disponible',
            ];
        });

        return response()->json(['success' => true, 'data' => $areas], 200);
    }

    /** 6. CÁMARA EN VIVO */
    public function camara(Request $request)
    {
        $user = $request->user();
        $condo = $user->departamento?->condominio;

        return response()->json([
            'success' => true,
            'data'    => [
                'nombre'     => '🔴 EN VIVO — Puerta Principal',
                'stream_url' => $condo?->url_camara_principal ?? 'https://www.youtube.com/embed/live_stream',
            ]
        ], 200);
    }

    /** 7. COMUNICADOS */
    public function comunicados(Request $request)
    {
        $condoId = $request->user()->departamento?->condominio_id ?? 1;
        $comunicados = Comunicado::where('condominio_id', $condoId)->latest()->get()->map(function ($com) {
            return [
                'id'        => $com->id,
                'titulo'    => $com->titulo,
                'contenido' => $com->contenido,
                'fecha'     => $com->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json(['success' => true, 'data' => $comunicados], 200);
    }

    /** 8. MARKETPLACE */
    public function marketplace(Request $request)
    {
        $condoId = $request->user()->departamento?->condominio_id ?? 1;
        $anuncios = Anuncio::where('condominio_id', $condoId)->latest()->get()->map(function ($a) {
            return [
                'id'          => $a->id,
                'titulo'      => $a->titulo,
                'precio'      => 'S/ ' . number_format((float)($a->precio ?? $a->monto ?? 0), 2),
                'descripcion' => $a->descripcion,
                'contacto'    => $a->contacto ?? $a->telefono ?? 'WhatsApp',
            ];
        });

        return response()->json(['success' => true, 'data' => $anuncios], 200);
    }

    public function registrarMarketplace(Request $request)
    {
        try {
            $user = $request->user();
            $anuncio = Anuncio::create([
                'condominio_id'   => $user->departamento?->condominio_id ?? 1,
                'departamento_id' => $user->departamento_id ?? 1,
                'user_id'        => $user->id,
                'titulo'         => $request->input('titulo'),
                'precio'         => (float) $request->input('precio', 0),
                'descripcion'    => $request->input('descripcion'),
                'contacto'       => $request->input('contacto', $user->telefono ?? 'WhatsApp'),
                'estado'         => 'Activo',
            ]);

            return response()->json(['success' => true, 'message' => 'Producto publicado en el Marketplace Vecinal.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 9. VOTACIONES */
    public function votaciones(Request $request)
    {
        $condoId = $request->user()->departamento?->condominio_id ?? 1;
        $votaciones = Votacion::where('condominio_id', $condoId)->latest()->get()->map(function ($v) {
            return [
                'id'          => $v->id,
                'titulo'      => $v->titulo,
                'descripcion' => $v->descripcion,
                'estado'      => $v->estado ?? 'Activa',
            ];
        });

        return response()->json(['success' => true, 'data' => $votaciones], 200);
    }

    /** 10. DOCUMENTOS */
    public function documentos(Request $request)
    {
        $condoId = $request->user()->departamento?->condominio_id ?? 1;
        $docs = Documento::where('condominio_id', $condoId)->latest()->get()->map(function ($d) {
            return [
                'id'     => $d->id,
                'titulo' => $d->titulo,
                'tipo'   => $d->tipo ?? 'Reglamento PDF',
                'url'    => $d->archivo ? asset('storage/' . $d->archivo) : '#',
            ];
        });

        return response()->json(['success' => true, 'data' => $docs], 200);
    }

    /** 11. MASCOTAS */
    public function mascotas(Request $request)
    {
        $user = $request->user();
        $mascotas = Mascota::where('departamento_id', $user->departamento_id)->get()->map(function ($m) {
            return [
                'id'     => $m->id,
                'nombre' => $m->nombre,
                'tipo'   => $m->tipo ?? 'Mascota',
                'raza'   => $m->raza ?? 'N/A',
            ];
        });

        return response()->json(['success' => true, 'data' => $mascotas], 200);
    }

    public function registrarMascota(Request $request)
    {
        try {
            $user = $request->user();
            $mascota = Mascota::create([
                'condominio_id'   => $user->departamento?->condominio_id ?? 1,
                'departamento_id' => $user->departamento_id ?? 1,
                'user_id'        => $user->id,
                'nombre'         => $request->input('nombre'),
                'tipo'           => $request->input('tipo', 'Mascota'),
                'raza'           => $request->input('raza', 'Raza'),
            ]);

            return response()->json(['success' => true, 'message' => 'Mascota registrada en el padrón.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** 12. RECLAMOS */
    public function reclamos(Request $request)
    {
        $user = $request->user();
        $reclamos = Reclamo::where('departamento_id', $user->departamento_id)->latest()->get()->map(function ($r) {
            return [
                'id'          => $r->id,
                'asunto'      => $r->asunto,
                'descripcion' => $r->descripcion,
                'estado'      => $r->estado ?? 'Pendiente',
            ];
        });

        return response()->json(['success' => true, 'data' => $reclamos], 200);
    }

    public function registrarReclamo(Request $request)
    {
        try {
            $user = $request->user();
            $reclamo = Reclamo::create([
                'condominio_id'   => $user->departamento?->condominio_id ?? 1,
                'departamento_id' => $user->departamento_id ?? 1,
                'user_id'        => $user->id,
                'asunto'         => $request->input('asunto'),
                'descripcion'    => $request->input('descripcion'),
                'estado'         => 'Pendiente',
            ]);

            return response()->json(['success' => true, 'message' => 'Sugerencia enviada a la administración.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}