<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Departamento;
use App\Models\Pago;
use App\Models\Comunicado;
use App\Models\Mascota;
use App\Models\Reclamo;
use App\Models\Invitado;
use App\Models\AreaComun;
use App\Models\Documento;
use App\Models\Votacion;
use App\Models\Marketplace;
use App\Models\Banco;
use App\Models\User;
use App\Models\AlertaSOS;
use Laravel\Sanctum\PersonalAccessToken;

class VecinoApiController extends Controller
{
    /**
     * Helper privado para autenticar al usuario por Bearer Token Sanctum
     */
    private function getAuthenticatedUser(Request $request)
    {
        // 1. Si Sanctum ya resolvió el usuario
        $user = $request->user();
        if ($user) {
            return $user;
        }

        // 2. Resolver manualmente desde la tabla personal_access_tokens
        $token = $request->bearerToken();
        if (!empty($token)) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable) {
                return $accessToken->tokenable;
            }
        }

        // 3. Fallback al usuario por defecto (Eduardo Ibañez)
        return User::where('email', 'eduardo@gmail.com')->first();
    }

    /**
     * 1. DASHBOARD PRINCIPAL (VECINOS / PROPIETARIOS)
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            // Buscar departamento por correo del usuario o por id
            $dpto = null;
            if ($user && !empty($user->email)) {
                $dpto = Departamento::where('email_propietario', $user->email)
                    ->orWhere('email_inquilino', $user->email)
                    ->first();
            }

            if (!$dpto && $user && !empty($user->departamento_id)) {
                $dpto = Departamento::find($user->departamento_id);
            }

            if (!$dpto) {
                $dpto = Departamento::where('numero', '100')->first();
            }

            $dptoId = $dpto->id ?? 1;

            // Sumar cuotas pendientes del departamento
            $deudaAcumulada = Pago::where('departamento_id', $dptoId)
                ->whereNotIn('estado', ['Pagado', 'pagado', 'Aprobado', 'aprobado'])
                ->sum('monto');

            $ultimoComunicado = Comunicado::latest()->first();

            return response()->json([
                'success' => true,
                'vecino_nombre' => $user->name ?? $dpto->nombre_propietario ?? 'Eduardo Ibañez',
                'departamento_numero' => $dpto->numero ?? '100',
                'deuda_acumulada' => (float) $deudaAcumulada,
                'monto_formateado' => 'S/ ' . number_format($deudaAcumulada, 2, '.', ','),
                'esta_al_dia' => $deudaAcumulada <= 0,
                'ultimo_comunicado' => [
                    'titulo' => $ultimoComunicado->titulo ?? '',
                    'contenido' => $ultimoComunicado->contenido ?? '',
                    'fecha' => $ultimoComunicado?->created_at?->format('d/m') ?? ''
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error Dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

   /**
 * 2. MIS PAGOS Y RECIBOS
 */
public function misPagos(Request $request)
{
    try {
        $user = $this->getAuthenticatedUser($request);

        $dpto = null;
        if ($user && !empty($user->email)) {
            $dpto = Departamento::where('email_propietario', $user->email)
                ->orWhere('email_inquilino', $user->email)
                ->first();
        }

        if (!$dpto && $user && !empty($user->departamento_id)) {
            $dpto = Departamento::find($user->departamento_id);
        }

        if (!$dpto) {
            $dpto = Departamento::where('numero', '100')->first();
        }

        $condominioId = $dpto->condominio_id ?? $user->condominio_id ?? 1;
        $dptoId = $dpto->id ?? 1;

        // Cuentas bancarias oficiales
        $cuentasBancarias = Banco::where('condominio_id', $condominioId)->get();

        if ($cuentasBancarias->isEmpty()) {
            $listaCuentas = [
                [
                    'id' => 1,
                    'banco' => 'BCP',
                    'numero_cuenta' => '193-98765432-0-11',
                    'cci' => '00219300987654320111',
                    'titular' => 'Condominio LIVO',
                    'tipo_cuenta' => 'Corriente',
                    'es_yape_plin' => false,
                    'yape_plin_numero' => ''
                ]
            ];
        } else {
           $listaCuentas = $cuentasBancarias->map(function ($b) {
                $esYape = !empty($b->activo_yape_plin) || !empty($b->es_yape_plin) || str_contains(strtolower($b->nombre_banco ?? $b->banco ?? ''), 'yape');

               return [
                    'id' => $b->id,
                    'banco' => $esYape ? 'Yape / Plin' : ($b->nombre_banco ?? $b->banco ?? 'Banco Oficial'),
                    'numero_cuenta' => $esYape ? ($b->yape_plin_numero ?? '997416788') : ($b->numero_cuenta ?? 'N/A'),
                    'cci' => $b->cci ?? 'N/A',
                    'titular' => $esYape ? (trim($b->yape_plin_titular ?? '') ?: ($b->titular ?? 'junta los cedros')) : ($b->titular ?? 'Condominio'),
                    'tipo_cuenta' => $esYape ? 'Billetera Digital' : ($b->tipo_cuenta ?? 'Corriente'),
                    'es_yape_plin' => $esYape,
                    'yape_numero' => $b->yape_plin_numero ?? '997416788',
                    'yape_plin_numero' => $b->yape_plin_numero ?? '997416788'
                ];
            });
        }

        // Recibos con URL absoluta que conecta con tu función descargarPdf($id)
        $pagos = Pago::where('departamento_id', $dptoId)
            ->orderBy('created_at', 'desc')
            ->get();

        $recibosFormat = $pagos->map(function ($pago) {
            return [
                'id' => $pago->id,
                'concepto' => !empty($pago->concepto) ? $pago->concepto : 'Cuota de Mantenimiento',
                'monto' => (float) $pago->monto,
                'monto_formateado' => 'S/ ' . number_format($pago->monto, 2, '.', ','),
                'estado' => $pago->estado ?? 'Pendiente',
                'fecha_vencimiento' => $pago->fecha_vencimiento ?? '12 de cada mes',
                'recibo_pdf_url' => url('/api/v1/vecino/pagos/' . $pago->id . '/pdf')
            ];
        });

        return response()->json([
            'success' => true,
            'cuentas_bancarias' => $listaCuentas,
            'data' => $recibosFormat
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error Pagos: ' . $e->getMessage()
        ], 500);
    }
}
    /**

  /** DESCARGAR O VER RECIBO PDF */
    public function descargarPdf($id)
    {
        try {
            $pago = Pago::with(['departamento'])->find($id);

            if (!$pago) {
                return response()->json(['error' => 'Recibo no encontrado'], 404);
            }

            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('recibo', ['pago' => $pago, 'record' => $pago]);
                return $pdf->stream("recibo-{$pago->id}.pdf");
            }

            if (view()->exists('recibo')) {
                return response(view('recibo', ['pago' => $pago, 'record' => $pago]))->header('Content-Type', 'text/html');
            }

            if (view()->exists('vendor.filament-panels.recibo')) {
                return response(view('vendor.filament-panels.recibo', ['pago' => $pago, 'record' => $pago]))->header('Content-Type', 'text/html');
            }

            return response()->json(['error' => 'Vista de recibo no configurada'], 404);
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
     * 3. DISPARAR BOTÓN DE PÁNICO S.O.S. (VINCULA EL NOMBRE EXACTO DEL RESIDENTE)
     */
    public function dispararSOS(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            $dpto = null;
            if ($user && !empty($user->email)) {
                $dpto = Departamento::where('email_propietario', $user->email)
                    ->orWhere('email_inquilino', $user->email)
                    ->first();
            }

            if (!$dpto && $user && !empty($user->departamento_id)) {
                $dpto = Departamento::find($user->departamento_id);
            }

            if (!$dpto) {
                $dpto = Departamento::where('numero', '100')->first();
            }

            $condominioId = $dpto->condominio_id ?? $user->condominio_id ?? 1;
            $dptoId = $dpto->id ?? 1;
            $nombreResidente = $user->name ?? $dpto->nombre_propietario ?? 'Eduardo Ibañez';

            // Guardar alerta vinculando el usuario autenticado
            AlertaSOS::create([
                'condominio_id' => $condominioId,
                'departamento_id' => $dptoId,
                'user_id' => $user->id ?? null,
                'descripcion' => 'ALERTA S.O.S DE RESIDENTE: ' . $nombreResidente . ' (Dpto. ' . ($dpto->numero ?? '100') . ') solicita ayuda urgente.',
                'estado' => 'Pendiente',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alerta S.O.S. enviada a Portería. La ayuda está en camino.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error S.O.S.: ' . $e->getMessage()
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

    /** 5. ÁREAS COMUNES REALES DEL EDIFICIO */
    public function areasComunes(Request $request)
    {
        try {
            $user = $request->user();
            $dpto = $user?->departamento ?? \App\Models\Departamento::find($user?->departamento_id ?? 1);
            $condoId = $dpto?->condominio_id ?? $user?->condominio_id ?? 1;

            $areas = AreaComun::where('condominio_id', $condoId)->get()->map(function ($a) {
                $costo = (float)($a->costo ?? $a->precio ?? 0);
                return [
                    'id' => $a->id,
                    'nombre' => $a->nombre ?? $a->nombre_area ?? 'Área Común',
                    'descripcion' => $a->descripcion ?? $a->reglas ?? 'Disponible para eventos de residentes.',
                    'precio' => $costo,
                    'precio_formateado' => $costo > 0 ? ('S/ ' . number_format($costo, 2, '.', ',')) : 'Gratuito',
                ];
            });

            return response()->json(['success' => true, 'data' => $areas]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'data' => []]);
        }
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

  /**
     * 8. MARKETPLACE VECINAL (LISTA DE PRODUCTOS PUBLICADOS)
     */
    public function marketplace(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            $dpto = null;
            if ($user && !empty($user->email)) {
                $dpto = Departamento::where('email_propietario', $user->email)
                    ->orWhere('email_inquilino', $user->email)
                    ->first();
            }

            if (!$dpto && $user && !empty($user->departamento_id)) {
                $dpto = Departamento::find($user->departamento_id);
            }

            $condoId = $dpto->condominio_id ?? $user->condominio_id ?? 1;

            $anuncios = Anuncio::where('condominio_id', $condoId)
                ->latest()
                ->get();

            $format = $anuncios->map(function ($a) {
                return [
                    'id' => $a->id,
                    'producto' => $a->producto ?? $a->titulo ?? 'Producto Vecinal',
                    'precio' => (float) ($a->precio ?? 0),
                    'precio_formateado' => 'S/ ' . number_format((float)($a->precio ?? 0), 2, '.', ','),
                    'telefono_whatsapp' => !empty($a->telefono_whatsapp) ? $a->telefono_whatsapp : ($a->user->telefono ?? '98765234'),
                    'descripcion' => $a->descripcion ?? '',
                    'vendedor' => $a->user->name ?? 'Eduardo Ibañez',
                    'departamento_numero' => $a->departamento->numero ?? '100',
                    'imagen_url' => !empty($a->imagen) ? 'https://admin.livo.com.pe/storage/' . ltrim($a->imagen, '/') : null,
                    'fecha' => $a->created_at?->format('d/m/Y') ?? ''
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $format
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error Marketplace: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * REGISTRAR / PUBLICAR NUEVO PRODUCTO EN EL MARKETPLACE
     */
    public function registrarMarketplace(Request $request)
    {
        try {
            $user = $this->getAuthenticatedUser($request);

            $dpto = null;
            if ($user && !empty($user->email)) {
                $dpto = Departamento::where('email_propietario', $user->email)
                    ->orWhere('email_inquilino', $user->email)
                    ->first();
            }

            if (!$dpto && $user && !empty($user->departamento_id)) {
                $dpto = Departamento::find($user->departamento_id);
            }

            $imagePath = null;
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('marketplace', 'public');
            }

            $anuncio = Anuncio::create([
                'condominio_id' => $dpto->condominio_id ?? $user->condominio_id ?? 1,
                'user_id' => $user->id ?? 4,
                'producto' => $request->input('producto') ?? $request->input('titulo') ?? 'Producto Vecinal',
                'precio' => (float) $request->input('precio', 0),
                'telefono_whatsapp' => $request->input('telefono_whatsapp') ?? ($user->telefono ?? '98765234'),
                'descripcion' => $request->input('descripcion', ''),
                'imagen' => $imagePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Producto publicado con éxito en el Marketplace Vecinal.'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al publicar: ' . $e->getMessage()
            ], 500);
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