<?php

namespace App\Filament\Master\Resources;

use App\Filament\Master\Resources\CondominioResource\Pages;
use App\Models\Condominio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Carbon\Carbon;

class CondominioResource extends Resource
{
    protected static ?string $model = Condominio::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Condominios';
    protected static ?string $pluralModelLabel = 'Gestión de Condominios';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $role = strtolower(auth()->user()->role ?? '');
        return in_array($role, ['super_admin', 'superadmin', 'master']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General del Condominio')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')->label('Nombre del Condominio')->required(),
                        Forms\Components\TextInput::make('direccion')->label('Dirección Completa'),
                        Forms\Components\TextInput::make('ruc')->label('RUC / Registro Fiscal'),
                    ])->columns(3),

                Forms\Components\Section::make('Configuración del Plan SaaS')
                    ->schema([
                        Forms\Components\Select::make('plan_saas')
                            ->label('Plan SaaS')
                            ->options([
                                'Básico'     => 'Plan Básico (Hasta 20 depas)',
                                'Pro'        => 'Plan Pro (Hasta 100 depas)',
                                'Enterprise' => 'Plan Enterprise (Ilimitado)',
                            ])
                            ->default('Pro')
                            ->required(),

                        Forms\Components\TextInput::make('precio_mensual_saas')
                            ->label('Precio Mensual del Software (S/)')
                            ->prefix('S/')
                            ->numeric()
                            ->default(150.00)
                            ->required(),

                        Forms\Components\Select::make('estado_servicio')
                            ->label('Estado de Acceso')
                            ->options([
                                'Activo'     => '🟢 Activo (Acceso Normal)',
                                'Suspendido' => '🔴 Suspendido (Falta de Pago)',
                                'Prueba'     => '🟡 Periodo de Prueba',
                            ])
                            ->default('Activo')
                            ->required(),

                        Forms\Components\DatePicker::make('fecha_vencimiento_saas')
                            ->label('Fecha Vencimiento SaaS'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Condominio')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Condominio $record): string => $record->direccion ?? 'Sin dirección'),

                Tables\Columns\TextColumn::make('plan_saas')
                    ->label('Plan SaaS')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('precio_mensual_saas')
                    ->label('Tarifa Mensual')
                    ->money('PEN')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('estado_servicio')
                    ->label('Estado Acceso')
                    ->badge()
                    ->color(fn (string $state = null): string => match ($state) {
                        'Activo'     => 'success',
                        'Suspendido' => 'danger',
                        'Prueba'     => 'warning',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('departamentos_count')
                    ->counts('departamentos')
                    ->label('Unidades')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                // 📥 DESCARGAR BACKUP / COPIA DE SEGURIDAD JSON
                Tables\Actions\Action::make('descargarBackup')
                    ->label('Backup')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->action(function (Condominio $record) {
                        $datos = [
                            'condominio'     => $record,
                            'departamentos'  => $record->departamentos,
                            'pagos'          => \App\Models\Pago::where('condominio_id', $record->id)->get(),
                            'gastos'         => \App\Models\Gasto::where('condominio_id', $record->id)->get(),
                            'comunicados'    => \App\Models\Comunicado::where('condominio_id', $record->id)->get(),
                            'fecha_respaldo' => now()->toDateTimeString(),
                        ];

                        $json = json_encode($datos, JSON_PRETTY_PRINT);
                        $nombreArchivo = 'Backup-LIVO-' . str($record->nombre)->slug() . '-' . date('Y-m-d') . '.json';

                        return response()->streamDownload(fn () => print($json), $nombreArchivo);
                    }),

                // 🔄 RESTAURAR COPIA DE SEGURIDAD CON CONTRASEÑA Y ADVERTENCIA
                Tables\Actions\Action::make('restaurarBackup')
                    ->label('Restaurar')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->button()
                    ->modalHeading('⚠️ ADVERTENCIA: RESTAURACIÓN DE SEGURIDAD')
                    ->modalDescription('Está a punto de restaurar la base de datos de este condominio. Se reemplazarán los datos actuales. Confirme su contraseña por seguridad.')
                    ->form([
                        Forms\Components\FileUpload::make('archivo_json')
                            ->label('Seleccionar Archivo de Respaldo (.JSON)')
                            ->acceptedFileTypes(['application/json'])
                            ->disk('public')
                            ->directory('temp_backups')
                            ->required(),

                        Forms\Components\TextInput::make('confirm_password')
                            ->label('Ingrese su Contraseña de Superadmin para Confirmar')
                            ->password()
                            ->required(),
                    ])
                    ->action(function (Condominio $record, array $data) {
                        $user = auth()->user();

                        // 🛡️ VERIFICACIÓN DE CONTRASEÑA DEL SUPERADMIN
                        if (!\Illuminate\Support\Facades\Hash::check($data['confirm_password'], $user->password)) {
                            Notification::make()
                                ->title('⛔ Contraseña Incorrecta')
                                ->body('La contraseña ingresada no coincide. Restauración cancelada por seguridad.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $filePath = storage_path('app/public/' . $data['archivo_json']);

                        if (file_exists($filePath)) {
                            $backupData = json_decode(file_get_contents($filePath), true);

                            if (isset($backupData['condominio'])) {
                                $record->update($backupData['condominio']);

                                Notification::make()
                                    ->title('🟢 Restauración Completada')
                                    ->body('La base de datos del condominio ' . $record->nombre . ' ha sido restaurada con éxito.')
                                    ->success()
                                    ->send();
                                return;
                            }
                        }

                        Notification::make()
                            ->title('Error de Lectura')
                            ->body('El archivo de respaldo JSON no tiene un formato válido.')
                            ->danger()
                            ->send();
                    }),

                // MODO FANTASMA: INGRESO AL PANEL DEL EDIFICIO EN 1 CLIC
                Tables\Actions\Action::make('ingresarAdmin')
                    ->label('Ver Panel Admin')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(function (Condominio $record): string {
                        $slug = rawurlencode($record->nombre);
                        if (str_contains(request()->getHost(), 'test')) {
                            return "http://sistema-condominio.test/admin/edificio/{$slug}";
                        }
                        return "https://admin.livo.com.pe/edificio/{$slug}";
                    })
                    ->openUrlInNewTab(),

                // KILL SWITCH: SUSPENDER / REACTIVAR
                Tables\Actions\Action::make('toggleEstado')
                    ->label(fn (Condominio $record) => $record->estado_servicio === 'Suspendido' ? 'Reactivar' : 'Suspender')
                    ->color(fn (Condominio $record) => $record->estado_servicio === 'Suspendido' ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (Condominio $record) {
                        $nuevo = $record->estado_servicio === 'Suspendido' ? 'Activo' : 'Suspendido';
                        $record->update(['estado_servicio' => $nuevo]);
                    }),

                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCondominios::route('/'),
            'create' => Pages\CreateCondominio::route('/create'),
            'edit'   => Pages\EditCondominio::route('/{record}/edit'),
        ];
    }
}