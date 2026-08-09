<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php
        $urlPagos = \App\Filament\Resources\PagoResource::getUrl('index');
    ?>

    <style>
        .livo-hub-card {
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 1.25rem;
            padding: 1.5rem;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }

        html:not(.dark) .livo-hub-card {
            background: #ffffff !important;
            border: 1px solid rgba(148, 163, 184, 0.3) !important;
            color: #0f172a !important;
            box-shadow: 0 10px 25px -5px rgba(148, 163, 184, 0.15) !important;
        }

        html:not(.dark) .livo-hub-card div { color: #0f172a !important; }
    </style>

    <div class="space-y-6">
        <?php echo $__env->make('filament.components.header-card', [
            'icon' => '💰',
            'badge' => 'ESTADO FINANCIERO',
            'title' => 'Mis Finanzas & Recibos de Mantenimiento',
            'description' => 'Consulta tus estados de cuenta, descarga tu recibo oficial en PDF y sube tus comprobantes de pago.',
            'actions' => null,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
            <a href="<?php echo e($urlPagos); ?>" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(124, 58, 237, 0.2); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    💳
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Mis Pagos & Recibos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Consulta de cuotas y vouchers</div>
                </div>
            </a>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\sistema-condominio\resources\views/filament/vecino/pages/finanzas-hub.blade.php ENDPATH**/ ?>