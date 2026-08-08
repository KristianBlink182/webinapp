<x-filament-widgets::widget>
    @if($pago)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-filament::badge color="danger" icon="heroicon-m-exclamation-circle">
                        Pago Pendiente
                    </x-filament::badge>
                    <span class="text-xs text-gray-500 font-medium">
                        {{ $pago->concepto ?? 'Cuota de Mantenimiento' }}
                    </span>
                </div>
            </x-slot>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 py-2">
                <div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Monto Total a Pagar</span>
                    <p class="text-4xl font-black text-primary-600 dark:text-primary-400 mt-1">
                        S/ {{ number_format($pago->monto, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                        <x-heroicon-m-building-office-2 class="w-4 h-4 text-primary-500" />
                        {{ $condominio->nombre ?? 'Tu Condominio' }}
                    </p>
                </div>

                <div class="flex items-center">
                    <x-filament::button
                        href="{{ $urlPago }}"
                        tag="a"
                        icon="heroicon-m-arrow-up-tray"
                        size="lg"
                        color="primary"
                    >
                        Reportar / Subir Voucher
                    </x-filament::button>
                </div>
            </div>

            @if($condominio && ($condominio->banco || $condominio->numero_cuenta))
                <x-slot name="footer">
                    <div class="flex flex-wrap items-center justify-between gap-4 text-xs text-gray-600 dark:text-gray-300">
                        <div>
                            <span class="font-bold">Datos de Abono:</span> {{ $condominio->banco }} — <code class="font-mono bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-primary-600 dark:text-primary-400">{{ $condominio->numero_cuenta }}</code>
                        </div>
                        @if($condominio->cci)
                            <div>
                                <span class="font-bold">CCI:</span> <code class="font-mono bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded text-gray-500">{{ $condominio->cci }}</code>
                            </div>
                        @endif
                    </div>
                </x-slot>
            @endif
        </x-filament::section>
    @else
        <x-filament::section>
            <div class="flex items-center gap-4 py-2">
                <div class="p-3 bg-emerald-500/10 text-emerald-500 rounded-xl">
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">¡Estás al día! 🎉</h3>
                    <p class="text-xs text-gray-500">No tienes recibos o cuotas pendientes de pago.</p>
                </div>
            </div>
        </x-filament::section>
    @endif
</x-filament-widgets::widget>