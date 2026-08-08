<?php

namespace App\Filament\Resources\DepartamentoResource\Pages;

use App\Filament\Resources\DepartamentoResource;
use App\Models\Departamento;
use App\Models\Condominio;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Contracts\View\View;

class ListDepartamentos extends ListRecords
{
    protected static string $resource = DepartamentoResource::class;

    public function getTitle(): string
    {
        return '';
    }

    // 🏛️ CABECERA EJECUTIVA EN RECUADRO CON LOS 3 BOTONES A LA DERECHA
    public function getHeader(): ?View
    {
        return view('filament.components.header-card', [
            'icon'        => '🏢',
            'badge'       => 'Estructura del Edificio',
            'title'       => 'Padrón de Departamentos & Residentes',
            'description' => 'Directorio oficial de propietarios, inquilinos y accesos a la App del Vecino.',
            'actions'     => $this->getCachedHeaderActions(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('importarExcel')
                ->label('Importar desde Excel')
                ->icon('heroicon-m-document-arrow-up')
                ->color('success')
                ->modalHeading('📋 Carga Masiva de Departamentos')
                ->modalDescription('Suba el archivo con la lista de departamentos para registrar todos los datos automáticamente.')
                ->form([
                    Forms\Components\Placeholder::make('paso1')
                        ->label('Manual de Procedimiento')
                        ->content(new HtmlString('
                            <div style="background: #1e293b; border: 1px solid #334155; border-radius: 0.875rem; padding: 1rem; margin-bottom: 0.75rem; color: #cbd5e1; font-size: 0.8rem; line-height: 1.5;">
                                <p style="margin: 0 0 0.5rem 0; font-weight: 800; color: #ffffff;">Instrucciones para la carga masiva:</p>
                                1. Presione el botón verde de abajo para descargar el archivo de muestra.<br>
                                2. Complete los datos de sus vecinos en Microsoft Excel.<br>
                                3. En Excel vaya a <strong>Archivo ➔ Guardar como</strong> y seleccione <strong>CSV (delimitado por comas)</strong>.<br>
                                4. Adjunte ese archivo en el Paso 2 para procesar la lista completa.
                                <div style="margin-top: 0.75rem;">
                                    <a href="' . route('departamentos.plantilla') . '" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #059669; color: #ffffff; padding: 0.65rem 1.25rem; border-radius: 0.75rem; text-decoration: none; font-weight: 800; font-size: 0.8rem; box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);">
                                        📥 Descargar Plantilla (.CSV)
                                    </a>
                                </div>
                            </div>
                        ')),

                    Forms\Components\FileUpload::make('archivo_excel')
                        ->label('Paso 2: Adjuntar archivo Excel guardado (.csv)')
                        ->disk('public')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = storage_path('app/public/' . $data['archivo_excel']);

                    if (!file_exists($filePath)) {
                        Notification::make()->title('Archivo no encontrado')->danger()->send();
                        return;
                    }

                    $tenant = \Filament\Facades\Filament::getTenant();
                    $condoId = $tenant?->id ?? auth()->user()->condominio_id ?? 1;

                    $condominio = Condominio::find($condoId);
                    $plan = $condominio?->plan_saas ?? 'Básico';

                    $limiteMaximo = match ($plan) {
                        'Básico' => 20,
                        'Pro' => 100,
                        'Enterprise' => 999999,
                        default => 20,
                    };

                    $handle = fopen($filePath, 'r');
                    if ($handle === false) {
                        Notification::make()->title('Error al abrir el archivo')->danger()->send();
                        return;
                    }

                    $firstLine = fgets($handle);
                    $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';
                    rewind($handle);

                    $filasNuevas = 0;
                    while (($line = fgetcsv($handle, 1000, $delimiter)) !== false) {
                        if (!empty($line[0]) && trim($line[0]) !== 'numero' && trim($line[0]) !== 'N° Dpto') {
                            $filasNuevas++;
                        }
                    }
                    rewind($handle);

                    $actuales = Departamento::where('condominio_id', $condoId)->count();

                    if (($actuales + $filasNuevas) > $limiteMaximo) {
                        Notification::make()
                            ->title('⛔ Límite del Plan Excedido')
                            ->body("Su edificio está registrado en el Plan {$plan} (Límite: {$limiteMaximo} departamentos) e intenta subir {$filasNuevas} unidades teniendo ya {$actuales} registradas. Contacte a Soporte LIVO para solicitar una ampliación de plan.")
                            ->danger()
                            ->persistent()
                            ->send();
                        return;
                    }

                    $count = 0;
                    $rowNumber = 0;

                    while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                        $rowNumber++;

                        if ($rowNumber === 1 || empty($row[0])) {
                            continue;
                        }

                        $numero = trim($row[0] ?? '');
                        $piso = (int) trim($row[1] ?? 1);
                        $porcentaje = (float) str_replace(['%', ' '], '', trim($row[2] ?? 0));
                        $estacionamiento = trim($row[3] ?? '');
                        $nombrePropietario = trim($row[4] ?? '');
                        $telefonoPropietario = trim($row[5] ?? '');
                        $emailPropietario = trim($row[6] ?? '');
                        $condicion = trim($row[7] ?? 'Propietario');

                        if (!empty($numero)) {
                            Departamento::updateOrCreate(
                                [
                                    'condominio_id' => $condoId,
                                    'numero'        => $numero,
                                ],
                                [
                                    'piso'                     => $piso,
                                    'porcentaje_participacion' => $porcentaje,
                                    'estacionamiento'          => $estacionamiento,
                                    'nombre_propietario'       => $nombrePropietario,
                                    'telefono_propietario'     => $telefonoPropietario,
                                    'email_propietario'        => $emailPropietario,
                                    'condicion'                => empty($condicion) ? 'Propietario' : $condicion,
                                ]
                            );
                            $count++;
                        }
                    }

                    fclose($handle);

                    Notification::make()
                        ->title('¡Importación Exitosa!')
                        ->body("Se han registrado {$count} departamentos en la base de datos.")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('vaciarPadron')
                ->label('Vaciar Padrón')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('¿Eliminar TODOS los departamentos?')
                ->modalDescription('Esta acción eliminará de inmediato todos los departamentos de este edificio.')
                ->action(function () {
                    $tenant = \Filament\Facades\Filament::getTenant();
                    $condoId = $tenant?->id ?? auth()->user()->condominio_id;

                    Departamento::where('condominio_id', $condoId)->delete();

                    Notification::make()
                        ->title('Padrón Vaciado')
                        ->body('Se han eliminado todos los departamentos.')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Nuevo Departamento')
                ->createAnother(false),
        ];
    }
}