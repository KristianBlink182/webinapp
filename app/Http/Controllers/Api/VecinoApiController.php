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
    /** 1. DASHBOARD PRINCIPAL */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento ?? \App\Models\Departamento::find($user->departamento_id ?? 1);
            $dptoId = $user->departamento_id ?? $user->departamento?->id ?? 1;
            $condominio = $dpto?->condominio;

            // Consultar saldo pendiente acumulado
            $saldoPendiente = Pago::where('departamento_id', $dptoId)
                ->whereIn('estado', ['Pendiente', 'pendiente'])
                ->sum('monto') ?? 0;

            $ultimoComunicado = Comunicado::where('condominio_id', $condominio?->id ?? 1)->latest()->first();

            return response()->json([
                'success' => true,
                'data'    => [
                    'vecino_nombre'       => $user->name ?? 'Giancarlo Veliz',
                    'departamento_numero' => $dpto?->numero ?? '100',
                    'condominio_nombre'   => $condominio?->nombre ?? 'Jorge Chavez',
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
                    'alerta_sos_activa'   => false,
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error Dashboard: ' . $e->getMessage()], 500);
        }
    }
/** 2. MIS PAGOS Y RECIBOS */
    public function misPagos(Request $request)
    {
        try {
            $user = $request->user();
            $dptoId = $user->departamento_id ?? 1;

            $pagos = Pago::where('departamento_id', $dptoId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($pago) {
                    return [
                        'id' => $pago->id,
                        'concepto' => $pago->concepto ?? $pago->concepto_pago ?? 'Cuota de Mantenimiento',
                        'mes' => $pago->mes ?? 'Mes Actual',
                        'monto' => (float)($pago->monto ?? $pago->monto_mantenimiento ?? 0),
                        'monto_formateado' => 'S/ ' . number_format((float)($pago->monto ?? $pago->monto_mantenimiento ?? 0), 2, '.', ','),
                        'estado' => $pago->estado ?? 'Pendiente',
                        'fecha_vencimiento' => $pago->fecha_vencimiento ?? '12 de cada mes',
                        'recibo_pdf_url' => url('/api/v1/vecino/pagos/' . $pago->id . '/pdf'),
                    ];
                });

            return response()->json(['success' => true, 'data' => $pagos], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error Pagos: ' . $e->getMessage()], 500);
        }
    }

    /** DESCARGAR O VER RECIBO PDF */
    public function descargarPdf($id)
    {
        try {
            $pago = Pago::with(['departamento', 'condominio'])->find($id);

            if (!$pago) {
                return response()->json(['error' => 'Recibo no encontrado'], 404);
            }

            if (view()->exists('vendor.filament-panels.recibo')) {
                if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vendor.filament-panels.recibo', ['pago' => $pago]);
                    return $pdf->stream("recibo-{$pago->id}.pdf");
                }
                return view('vendor.filament-panels.recibo', ['pago' => $pago]);
            }

            return response()->json(['message' => 'Vista de recibo no configurada'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al generar recibo: ' . $e->getMessage()], 500);
        }
    }

    /** REPORTAR / ADJUNTAR COMPROBANTE DE PAGO */
    public function reportarPago(Request $request)
    {
        try {
            $request->validate([
                'pago_id' => 'required',
                'voucher' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

            $pago = Pago::find($request->pago_id);
            if (!$pago) {
                return response()->json(['success' => false, 'message' => 'Recibo no encontrado'], 404);
            }

            if ($request->hasFile('voucher')) {
                $path = $request->file('voucher')->store('vouchers', 'public');
                $pago->comprobante_pago = $path;
                $pago->estado = 'revision';
                $pago->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Comprobante adjuntado con éxito. Queda en estado Validando.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir voucher: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
 * 3. DISPARAR S.O.S. (LÓGICA IDÉNTICA A LA WEB - ALERTA REAL A PORTERÍA Y ADMIN)
 */
public function dispararSOS(Request $request)
{
    try {
        $user = $request->user();
        $dpto = $user->departamento ?? \App\Models\Departamento::find($user->departamento_id ?? 1);
        $dptoId = $dpto?->id ?? $user->departamento_id ?? 1;
        $condoId = $dpto?->condominio_id ?? 1;

        $alerta = AlertaSOS::create([
            'condominio_id'   => $condoId,
            'departamento_id' => $dptoId,
            'user_id'         => $user->id,
            'tipo'            => 'S.O.S. App Nativa',
            'descripcion'     => "¡ALERTA S.O.S! El residente {$user->name} del Dpto. " . ($dpto?->numero ?? '100') . " requiere asistencia inmediata en Portería.",
            'estado'          => 'Pendiente',
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Alerta S.O.S. enviada a Portería! La ayuda está en camino.',
            'alerta'  => $alerta,
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al disparar S.O.S: ' . $e->getMessage(),
        ], 500);
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

   /** 8. MARKETPLACE VECINAL (100% MAPEADO CON LA WEB) */
    public function marketplace(Request $request)
    {
        try {
            $user = $request->user();
            $condoId = $user->departamento?->condominio_id ?? 1;

            $anuncios = Anuncio::where('condominio_id', $condoId)
                ->latest()
                ->get()
                ->map(function ($a) {
                    return [
                        'id'                => $a->id,
                        'producto'          => $a->producto ?? 'Producto',
                        'precio'            => (float) ($a->precio ?? 0),
                        'precio_formateado' => 'S/ ' . number_format((float)($a->precio ?? 0), 2, '.', ''),
                        'telefono_whatsapp' => $a->telefono_whatsapp ?? $a->user?->telefono ?? '987654321',
                        'descripcion'       => $a->descripcion ?? '',
                        'imagen_url'        => $a->imagen ? asset('storage/' . $a->imagen) : null,
                        'vendedor'          => $a->user?->name ?? 'Vecino',
                        'fecha'             => $a->created_at->format('d/m/Y'),
                    ];
                });

            return response()->json(['success' => true, 'data' => $anuncios], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function registrarMarketplace(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user->departamento;

            $imagePath = null;
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('marketplace', 'public');
            }

            $anuncio = Anuncio::create([
                'condominio_id'     => $dpto?->condominio_id ?? 1,
                'user_id'           => $user->id,
                'producto'          => $request->input('producto') ?? $request->input('titulo'),
                'precio'            => (float) $request->input('precio', 0),
                'telefono_whatsapp' => $request->input('telefono_whatsapp', $user->telefono ?? '987654321'),
                'descripcion'       => $request->input('descripcion'),
                'imagen'            => $imagePath,
            ]);

            return response()->json(['success' => true, 'message' => 'Producto publicado con éxito en el Marketplace Vecinal.'], 200);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error al publicar: ' . $e->getMessage()], 500);
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