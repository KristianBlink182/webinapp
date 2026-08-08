<?php if (isset($component)) { $__componentOriginalb525200bfa976483b4eaa0b7685c6e24 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widget','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widget'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $user = auth()->user();
        $condominio = \Filament\Facades\Filament::getTenant();
        $nombreCondominio = $condominio?->nombre ?? $user?->departamento?->condominio?->nombre ?? 'Sin Condominio';
    ?>

    <style>
        /* Estilos Adaptativos LIVO (Modo Claro y Oscuro) */
        .livo-welcome-card {
            background: linear-gradient(135deg, #0c1626 0%, #15233b 100%);
            border: 1px solid rgba(56, 189, 248, 0.2);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 0 15px rgba(56, 189, 248, 0.1);
        }

        /* Cuando la web está en MODO CLARO (sin clase .dark) */
        html:not(.dark) .livo-welcome-card {
            background: linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%);
            border: 1px solid rgba(148, 163, 184, 0.3);
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.25), 0 0 20px rgba(56, 189, 248, 0.12);
        }

        html:not(.dark) .livo-welcome-title {
            color: #0f172a !important;
        }

        html:not(.dark) .livo-welcome-subtitle {
            color: #475569 !important;
        }
    </style>

    <div class="livo-welcome-card" style="border-radius: 1.25rem; padding: 1.5rem 2rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; transition: all 0.3s ease;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 3.5rem; height: 3.5rem; background: linear-gradient(135deg, #38bdf8 0%, #1d4ed8 100%); border-radius: 1rem; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; box-shadow: 0 4px 15px rgba(56, 189, 248, 0.35);">
                👨‍💼
            </div>
            <div>
                <h1 class="livo-welcome-title" style="font-size: 1.5rem; font-weight: 800; color: #ffffff; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <span>¡Bienvenido, <?php echo e($user->name); ?>! 👋</span>
                </h1>
                <p class="livo-welcome-subtitle" style="font-size: 0.95rem; color: #94a3b8; margin-top: 0.25rem; font-weight: 600;">
                    Panel de Administración y Control Operativo de <span style="color: #0284c7; font-weight: 800;"><?php echo e($nombreCondominio); ?></span>.
                </p>
            </div>
        </div>

        <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid #10b981; color: #059669; padding: 0.5rem 1.25rem; border-radius: 9999px; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; gap: 0.5rem; letter-spacing: 0.05em; text-transform: uppercase; box-shadow: 0 0 15px rgba(16, 185, 129, 0.2);">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
            PANEL DE CONTROL ACTIVO
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $attributes = $__attributesOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__attributesOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24)): ?>
<?php $component = $__componentOriginalb525200bfa976483b4eaa0b7685c6e24; ?>
<?php unset($__componentOriginalb525200bfa976483b4eaa0b7685c6e24); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\sistema-condominio\resources\views/filament/widgets/bienvenida-admin.blade.php ENDPATH**/ ?>