<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Comunicado;
use App\Models\AlertaSOS;
use App\Models\Mascota;
use App\Models\Reclamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VecinoApiController extends Controller
{
    /**
     * 1. DATOS DEL DASHBOARD PRINCIPAL DE LA APP NATIVA
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $dpto = $user->departamento;
        $condominio = $dpto?->condominio;

        if (!$dpto) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no tiene un departamento asignado.',
            ], 404);
        }

        // Deuda total acumulada del departamento
        $saldoPendiente = Pago::where('departamento_id', $dpto->id)
            ->whereIn('estado', ['Pendiente', 'pendiente'])
            ->sum('monto') ?? 0;

        // Último comunicado publicado en el condominio
        $ultimoComunicado = Comunicado::where('condominio_id', $condominio?->id)
            ->latest()
            ->first();

        // Alerta S.O.S activa si la hubiera
        $alertaActiva = AlertaSOS::where('departamento_id', $dpto->id)
            ->where('estado', 'Pendiente')
            ->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'vecino_nombre'       => $user->name,
                'departamento_numero' => $dpto->numero,
                'condominio_nombre'   => $condominio?->nombre ?? 'Edificio LIVO',
                'estado_cuenta'       => [
                    'monto_pendiente'  => (float) $saldoPendiente,
                    'monto_formateado' => 'S/ ' . number_format((float)$saldoPendiente, 2, '.', ''),
                    'esta_al_dia'      => $saldoPendiente <= 0,
                ],
                'ultimo_comunicado'   => $ultimoComunicado ? [
                    'id'          => $ultimoComunicado->id,
                    'titulo'      => $ultimoComunicado->titulo,
                    'contenido'   => $ultimoComunicado->contenido,
                    'fecha'       => $ultimoComunicado->created_at->format('d/m/Y'),
                ] : null,
                'alerta_sos_activa'   => $alertaActiva ? true : false,
                'siri_shortcut_url'   => 'https://www.icloud.com/shortcuts/653d6f68abc0490a81e73c2773d36a90',
            ]
        ], 200);
    }

    /**
     * 2. MÓDULO MIS PAGOS Y RECIBOS
     */
    public function misPagos(Request $request)
    {
        $user = $request->user();

        $pagos = Pago::where('departamento_id', $user->departamento_id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($pago) {
                return [
                    'id'                  => $pago->id,
                    'concepto'            => $pago->concepto ?? ("Cuota de Mantenimiento - " . ($pago->mes ?? '') . " " . ($pago->anio ?? '')),
                    'mes'                 => $pago->mes,
                    'anio'                => $pago->anio,
                    'monto_mantenimiento' => (float) ($pago->monto_mantenimiento ?? 0),
                    'monto_luz'           => (float) ($pago->monto_luz ?? 0),
                    'monto_agua'          => (float) ($pago->monto_agua ?? 0),
                    'monto_total'         => (float) $pago->monto,
                    'monto_formateado'    => 'S/ ' . number_format((float)$pago->monto, 2, '.', ''),
                    'estado'              => $pago->estado,
                    'fecha_vencimiento'   => '12 de cada mes',
                    'voucher_url'         => $pago->voucher ? asset('storage/' . $pago->voucher) : null,
                    'recibo_pdf_url'      => route('pago.pdf', $pago->id),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $pagos,
        ], 200);
    }

    /**
     * 3. SUBIR COMPROBANTE DE PAGO (YAPE / PLIN) DESDE LA APP NATIVA
     */
    public function reportarPago(Request $request, $pagoId)
    {
        $validator = Validator::make($request->all(), [
            'voucher' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Por favor adjunte una foto válida del voucher de pago.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $pago = Pago::where('id', $pagoId)
            ->where('departamento_id', $request->user()->departamento_id)
            ->first();

        if (!$pago) {
            return response()->json([
                'success' => false,
                'message' => 'Recibo de pago no encontrado.',
            ], 404);
        }

        // Guardar la foto del voucher
        $path = $request->file('voucher')->store('vouchers', 'public');

        $pago->update([
            'voucher' => $path,
            'estado'  => 'En Revisión',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comprobante de pago enviado con éxito. La administración validará su recibo.',
            'pago'    => [
                'id'          => $pago->id,
                'estado'      => 'En Revisión',
                'voucher_url' => asset('storage/' . $path),
            ]
        ], 200);
    }

    /**
     * 4. BOTÓN DE PÁNICO S.O.S NATIVO (1 TOQUE / SIRI)
     */
    public function dispararSOS(Request $request)
    {
        $user = $request->user();
        $dpto = $user->departamento;
        $condominio = $dpto?->condominio;

        if (!$dpto || !$condominio) {
            return response()->json([
                'success' => false,
                'message' => 'No se pudo identificar la portería del departamento.',
            ], 400);
        }

        $alerta = AlertaSOS::create([
            'condominio_id'   => $condominio->id,
            'departamento_id' => $dpto->id,
            'user_id'         => $user->id,
            'tipo'            => 'S.O.S. App Nativa',
            'descripcion'     => "¡ALERTA S.O.S! El residente {$user->name} del Dpto. {$dpto->numero} requiere asistencia inmediata en Portería.",
            'estado'          => 'Pendiente',
        ]);

        return response()->json([
            'success' => true,
            'message' => '¡Alerta S.O.S. enviada a la Portería! La ayuda está en camino.',
            'alerta'  => [
                'id'         => $alerta->id,
                'dpto'       => $dpto->numero,
                'estado'     => 'Pendiente',
                'created_at' => $alerta->created_at->format('H:i:s'),
            ]
        ], 200);
    }

    /**
     * 5. LISTA DE COMUNICADOS
     */
    public function comunicados(Request $request)
    {
        $user = $request->user();
        $condoId = $user->departamento?->condominio_id;

        $comunicados = Comunicado::where('condominio_id', $condoId)
            ->latest()
            ->get()
            ->map(function ($com) {
                return [
                    'id'        => $com->id,
                    'titulo'    => $com->titulo,
                    'contenido' => $com->contenido,
                    'fecha'     => $com->created_at->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $comunicados,
        ], 200);
    }

    /**
     * 6. LISTA DE MASCOTAS
     */
    public function mascotas(Request $request)
    {
        $user = $request->user();

        $mascotas = Mascota::where('departamento_id', $user->departamento_id)
            ->get()
            ->map(function ($m) {
                return [
                    'id'     => $m->id,
                    'nombre' => $m->nombre,
                    'tipo'   => $m->tipo ?? 'Mascota',
                    'raza'   => $m->raza ?? 'N/A',
                    'foto'   => $m->foto ? asset('storage/' . $m->foto) : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $mascotas,
        ], 200);
    }

    /**
     * 7. LISTA DE RECLAMOS
     */
    public function reclamos(Request $request)
    {
        $user = $request->user();

        $reclamos = Reclamo::where('departamento_id', $user->departamento_id)
            ->latest()
            ->get()
            ->map(function ($r) {
                return [
                    'id'          => $r->id,
                    'asunto'      => $r->asunto,
                    'descripcion' => $r->descripcion,
                    'estado'      => $r->estado ?? 'Pendiente',
                    'fecha'       => $r->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $reclamos,
        ], 200);
    }
}