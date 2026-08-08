<x-filament-panels::page>
    <style>
        .livo-profile-card {
            background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);
            border: 1px solid #312e81;
            border-radius: 1.5rem;
            padding: 1.5rem 2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .livo-profile-title {
            color: #ffffff !important;
        }

        .livo-profile-role {
            color: #a5b4fc !important;
        }

        /* ESTILOS ADAPTATIVOS PARA MODO CLARO */
        html:not(.dark) .livo-profile-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-profile-title {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-profile-role {
            color: #4338ca !important;
        }
    </style>

    <div style="font-family: system-ui, -apple-system, sans-serif; width: 100%;" class="space-y-6">
        @include('filament.components.header-card', [
            'title' => $this->getTitle(),
            'description' => 'Actualiza tu número de celular/WhatsApp de contacto y personaliza tu contraseña de acceso a la App.',
            'badge' => 'CONFIGURACIÓN DE MI CUENTA',
            'actions' => null,
        ])

        <!-- TARJETA DE PRESENTACIÓN DEL PERFIL (ADAPTATIVA MODO CLARO / OSCURO) -->
        <div class="livo-profile-card">
            <div style="display: flex; align-items: center; gap: 1.25rem;">
                <div style="width: 4rem; height: 4rem; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 900; color: #ffffff; box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="livo-profile-title" style="font-size: 1.35rem; font-weight: 900; margin: 0;">{{ auth()->user()->name }}</h2>
                    <span class="livo-profile-role" style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase;">
                        {{ auth()->user()->role === 'residente' ? 'Residente del Edificio' : ucfirst(auth()->user()->role) }}
                    </span>
                </div>
            </div>

            @if(auth()->user()->departamento)
                <div style="background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); padding: 0.75rem 1.25rem; border-radius: 1rem; text-align: right;">
                    <span style="font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 800; display: block;">Unidad Asignada</span>
                    <span style="font-size: 1rem; font-weight: 900; color: #0284c7;">Dpto. {{ auth()->user()->departamento?->numero }}</span>
                </div>
            @endif
        </div>

        <!-- FORMULARIO DE EDICIÓN DE PERFIL -->
        <form wire:submit="submit" class="space-y-6">
            {{ $this->form }}

            <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end;">
                <button type="submit" style="background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: #ffffff; font-weight: 900; font-size: 0.9rem; padding: 0.85rem 1.75rem; border-radius: 0.85rem; border: none; cursor: pointer; box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);">
                    💾 Guardar Cambios en Mi Perfil
                </button>
            </div>
        </form>
    </div>
</x-filament-panels::page>