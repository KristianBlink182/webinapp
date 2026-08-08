<style>
    .livo-header-card {
        background: linear-gradient(135deg, #0c1626 0%, #15233b 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1.25rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .livo-header-title {
        color: #ffffff !important;
    }

    .livo-header-desc {
        color: #94a3b8 !important;
    }

    /* ESTILOS ADAPTATIVOS PARA MODO CLARO */
    html:not(.dark) .livo-header-card {
        background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%) !important;
        border: 1px solid rgba(148, 163, 184, 0.3) !important;
        box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2), 0 0 20px rgba(56, 189, 248, 0.1) !important;
    }

    html:not(.dark) .livo-header-title {
        color: #0f172a !important;
    }

    html:not(.dark) .livo-header-desc {
        color: #475569 !important;
    }
</style>

<div class="livo-header-card">
    <!-- LADO IZQUIERDO: ÍCONO, BADGE, TÍTULO Y DESCRIPCIÓN -->
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div style="width: 52px; height: 52px; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
            {{ $icon ?? '🏢' }}
        </div>

        <div>
            <div style="margin-bottom: 0.25rem;">
                <span style="padding: 0.25rem 0.65rem; background: rgba(56, 189, 248, 0.15); color: #0284c7; font-weight: 800; font-size: 0.7rem; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ $badge ?? 'MÓDULO' }}
                </span>
            </div>

            <h1 class="livo-header-title" style="font-size: 1.5rem; font-weight: 800; margin: 0; line-height: 1.2;">
                {{ $title }}
            </h1>
            <p class="livo-header-desc" style="font-size: 0.8rem; margin: 0.25rem 0 0 0;">
                {{ $description }}
            </p>
        </div>
    </div>

    <!-- LADO DERECHO: LOS BOTONES DE ACCIÓN (IMPORTAR EXCEL, VACIAR, NUEVO DEPARTAMENTO, ETC.) -->
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <x-filament-actions::actions :actions="$actions" />
    </div>
</div>