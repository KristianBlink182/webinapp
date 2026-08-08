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
        $tenant = \Filament\Facades\Filament::getTenant();
        $urlCamara = $tenant?->url_camara_principal;

        $embedUrl = $urlCamara;
        if (!empty($urlCamara) && str_contains($urlCamara, 'watch?v=')) {
            $embedUrl = str_replace('watch?v=', 'embed/', $urlCamara);
        } elseif (!empty($urlCamara) && str_contains($urlCamara, 'youtu.be/')) {
            $embedUrl = str_replace('youtu.be/', 'www.youtube.com/embed/', $urlCamara);
        }
    ?>

    <style>
        .livo-camera-card {
            background: #ffffff;
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.2);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        html.dark .livo-camera-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        }

        @keyframes livo-red-ping {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.15); opacity: 1; box-shadow: 0 0 10px rgba(239, 68, 68, 0.8); }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        .livo-live-badge {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid #ef4444;
            color: #ef4444;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-weight: 800;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .livo-red-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ef4444;
            display: inline-block;
            animation: livo-red-ping 1.5s infinite ease-in-out;
        }
    </style>

    <div class="livo-camera-card">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <div style="font-size: 1rem; font-weight: 800; color: currentColor; display: flex; align-items: center; gap: 0.5rem;">
                📹 Cámara del Edificio
            </div>

            <div class="livo-live-badge">
                <span class="livo-red-dot"></span>
                EN VIVO
            </div>
        </div>

        
        <div style="width: 100%; flex-grow: 1; min-height: 290px; border-radius: 1rem; overflow: hidden; background: #000000; position: relative;">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($embedUrl)): ?>
                <iframe src="<?php echo e($embedUrl); ?>" style="width: 100%; height: 100%; border: none;" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            <?php else: ?>
                <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #94a3b8; padding: 1.5rem; text-align: center;">
                    <svg style="width: 48px; height: 48px; color: #38bdf8; margin-bottom: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <span style="font-size: 0.9rem; font-weight: 800; color: #ffffff;">Monitoreo en Directo</span>
                    <span style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Transmisión en tiempo real de la entrada principal</span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\laragon\www\sistema-condominio\resources\views/filament/widgets/camara-lobby-widget.blade.php ENDPATH**/ ?>