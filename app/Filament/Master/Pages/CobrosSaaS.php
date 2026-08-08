<?php

namespace App\Filament\Master\Pages;

use Filament\Pages\Page;
use App\Models\Condominio;
use Filament\Notifications\Notification;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;

class CobrosSaaS extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Cobros & Suscripciones';
    protected static ?string $title = 'Control de Pagos de Suscripciones SaaS';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.master.pages.cobros-saas';

    public ?array $data = [];

    public static function getNavigationBadge(): ?string
    {
        $count = Condominio::where('estado_pago_saas', 'Pago por Verificar')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public function mount(): void
    {
        $config = $this->getSaasConfig();
        $this->form->fill($config);
    }

    public function getSaasConfig(): array
    {
        $path = storage_path('app/saas_config.json');
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?? $this->getDefaultConfig();
        }
        return $this->getDefaultConfig();
    }

    public function getDefaultConfig(): array
    {
        return [
            'bcp_soles' => '191-98765432-0-12',
            'cci_bcp' => '002-191-0098765432012-54',
            'bbva_soles' => '0011-0123-0100098765-88',
            'ruc' => '20601234567',
            'razon_social' => 'PROYECTOS LIVO S.A.C.',
            'yape_numero' => '987 654 321',
            'yape_titular' => 'LIVO SaaS Oficial',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('⚙️ Ajustes de Cuentas Bancarias Oficiales LIVO SaaS')
                    ->description('Modifique los datos bancarios que verán los clientes en sus paneles para realizar sus transferencias.')
                    ->schema([
                        Forms\Components\TextInput::make('bcp_soles')->label('Cuenta BCP Soles')->required(),
                        Forms\Components\TextInput::make('cci_bcp')->label('CCI BCP')->required(),
                        Forms\Components\TextInput::make('bbva_soles')->label('Cuenta BBVA Soles')->required(),
                        Forms\Components\TextInput::make('ruc')->label('RUC Empresa')->required(),
                        Forms\Components\TextInput::make('razon_social')->label('Razón Social')->required(),
                        Forms\Components\TextInput::make('yape_numero')->label('Número Yape / Plin')->required(),
                        Forms\Components\TextInput::make('yape_titular')->label('Titular Yape / Plin')->required(),
                    ])->columns(2)
            ])
            ->statePath('data');
    }

    public function guardarConfiguracionBancaria(): void
    {
        $data = $this->form->getState();
        $path = storage_path('app/saas_config.json');
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        Notification::make()
            ->title('Cuentas Bancarias LIVO Actualizadas')
            ->body('Los datos bancarios fueron actualizados con éxito en todos los paneles de clientes.')
            ->success()
            ->send();
    }

    public function getViewData(): array
    {
        $condominios = Condominio::all();

        return [
            'condominios' => $condominios,
        ];
    }

    public function aprobarPago(int $condominioId): void
    {
        $condo = Condominio::find($condominioId);
        if ($condo) {
            $fechaActual = $condo->fecha_vencimiento_saas ? Carbon::parse($condo->fecha_vencimiento_saas) : now();
            $nuevaFecha = $fechaActual->addMonth()->format('Y-m-d');

            $condo->update([
                'estado_servicio' => 'Activo',
                'estado_pago_saas' => 'Al Día',
                'fecha_vencimiento_saas' => $nuevaFecha,
            ]);

            // Actualizar también la tabla del historial de pagos del cliente a APROBADO
            if (\Illuminate\Support\Facades\Schema::hasTable('pago_saas')) {
                \App\Models\PagoSaaS::where('condominio_id', $condo->id)
                    ->where('estado', 'Pago por Verificar')
                    ->update([
                        'estado' => 'Aprobado',
                    ]);
            }

            Notification::make()
                ->title('Pago Aprobado')
                ->body('Se renovó el servicio de ' . $condo->nombre . ' hasta el ' . date('d/m/Y', strtotime($nuevaFecha)))
                ->success()
                ->send();
        }
    }
}