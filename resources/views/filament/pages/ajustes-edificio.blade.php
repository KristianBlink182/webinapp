<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div style="margin-top: 1.5rem;">
            <button type="submit" style="background: #4f46e5; color: #ffffff; font-weight: 800; font-size: 0.875rem; padding: 0.75rem 2rem; border-radius: 0.75rem; border: none; cursor: pointer; box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);">
                💾 Guardar Configuración del Edificio
            </button>
        </div>
    </form>
</x-filament-panels::page>