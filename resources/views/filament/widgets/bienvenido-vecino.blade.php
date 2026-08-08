<x-filament-widgets::widget>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <!-- Tarjeta 1: Estado de Cuenta -->
        <x-filament::section>
            <div class="flex items-center gap-3">
                <x-filament::badge :color="$deudaTotal > 0 ? 'danger' : 'success'" size="lg">
                    S/ {{ number_format($deudaTotal, 2) }}
                </x-filament::badge>
                <div>
                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">Estado de Cuenta</h4>
                    <p class="text-xs text-gray-500">
                        {{ $deudaTotal > 0 ? 'Tienes deudas pendientes' : '¡Estás al día!' }}
                    </p>
                </div>
            </div>
        </x-filament::section>

        <!-- Tarjeta 2: Último Comunicado -->
        <x-filament::section>
            <div class="flex items-center gap-3">
                <x-filament::badge color="info" size="lg">
                    Aviso
                </x-filament::badge>
                <div class="overflow-hidden">
                    <h4 class="font-bold text-sm text-gray-900 dark:text-white truncate">
                        {{ $ultimoAviso ? $ultimoAviso->titulo : 'Sin comunicados' }}
                    </h4>
                    <p class="text-xs text-gray-500">Comunicación oficial</p>
                </div>
            </div>
        </x-filament::section>

        <!-- Tarjeta 3: Mi Unidad -->
        <x-filament::section>
            <div class="flex items-center gap-3">
                <x-filament::badge color="gray" size="lg">
                    Dpto. {{ $departamento }}
                </x-filament::badge>
                <div>
                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">Mi Ubicación</h4>
                    <p class="text-xs text-gray-500 truncate">{{ $condominio }}</p>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-widgets::widget>