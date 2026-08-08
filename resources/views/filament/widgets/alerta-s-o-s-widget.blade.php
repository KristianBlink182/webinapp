<x-filament-widgets::widget>
    <style>
        .livo-sismo-card {
            background: linear-gradient(135deg, #0c1626 0%, #15233b 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .livo-sismo-title {
            color: #ffffff !important;
        }

        .livo-sismo-subtitle {
            color: #94a3b8 !important;
        }

        /* MODO CLARO */
        html:not(.dark) .livo-sismo-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2);
        }

        html:not(.dark) .livo-sismo-title {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-sismo-subtitle {
            color: #475569 !important;
        }
    </style>

    <div style="font-family: system-ui, -apple-system, sans-serif; margin-bottom: 1.5rem; width: 100%;">
        <!-- BANNER DINÁMICO DE ACTIVACIÓN Y FINALIZACIÓN DE SISMO PARA EL ADMIN -->
        <div class="livo-sismo-card" style="display: flex; justify-content: space-between; align-items: center; border-radius: 1.25rem; padding: 1rem 1.5rem; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem; transition: all 0.3s ease;">
            <div>
                <span class="livo-sismo-title" style="font-size: 0.85rem; font-weight: 800; display: block;">Control de Alerta Sísmica del Edificio</span>
                <p class="livo-sismo-subtitle" style="font-size: 0.75rem; margin: 0.2rem 0 0 0;">Presione para activar o finalizar la evacuación en las pantallas de todos los vecinos.</p>
            </div>

            @if(!empty($sismoActivo) && $sismoActivo)
                <button wire:click="finalizarAlertaSismo" type="button" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #ffffff; font-weight: 900; font-size: 0.8rem; padding: 0.65rem 1.25rem; border-radius: 0.85rem; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);">
                    🟢 FINALIZAR ALERTA SISMO
                </button>
            @else
                <button wire:click="activarAlertaSismo" type="button" style="background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: #ffffff; font-weight: 900; font-size: 0.8rem; padding: 0.65rem 1.25rem; border-radius: 0.85rem; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4);">
                    🚨 ACTIVAR ALERTA SISMO
                </button>
            @endif
        </div>

        <!-- MONITOR DE EMERGENCIAS (SOLO LECTURA PARA EL ADMIN) -->
        @if(($alertasSOS->count() ?? 0) > 0 || ($auxiliosSismo->count() ?? 0) > 0)
            <div style="background: linear-gradient(135deg, #991b1b 0%, #7f1d1d 100%); border: 2px solid #f87171; border-radius: 1.25rem; padding: 1.25rem 1.75rem; box-shadow: 0 10px 30px -5px rgba(220, 38, 38, 0.5);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="background: rgba(255, 255, 255, 0.2); padding: 0.75rem; border-radius: 0.875rem; color: #ffffff;">
                        <x-heroicon-m-bell-alert style="width: 2rem; height: 2rem;" />
                    </div>
                    <div>
                        <h3 style="font-size: 1.05rem; font-weight: 900; color: #ffffff; margin: 0;">🚨 MONITOR EN VIVO: EMERGENCIAS Y AUXILIO SÍSMICO (MONITOREO ADMIN)</h3>
                        @foreach($alertasSOS as $sos)
                            <p style="font-size: 0.8rem; color: #fecdd3; margin-top: 0.25rem;">
                                <strong>Dpto. {{ $sos->departamento?->numero ?? 'N/A' }}</strong> — {{ $sos->user?->name }} (Emergencia SOS: {{ $sos->tipo }})
                            </p>
                        @endforeach
                        @foreach($auxiliosSismo as $auxilio)
                            <p style="font-size: 0.8rem; color: #fca5a5; margin-top: 0.25rem;">
                                <strong>Dpto. {{ $auxilio->departamento?->numero }}</strong> — {{ $auxilio->user?->name }} (Pide Auxilio por Sismo)
                            </p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>