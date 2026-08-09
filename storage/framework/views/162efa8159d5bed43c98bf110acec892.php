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
        $urlAvisos = \App\Filament\Resources\ComunicadoResource::getUrl('index');
        $urlMarketplace = \App\Filament\Resources\AnuncioResource::getUrl('index');
        $urlVotaciones = \App\Filament\Resources\VotacionResource::getUrl('index');
        $urlDocumentos = \App\Filament\Resources\DocumentoResource::getUrl('index');
        $urlMascotas = \App\Filament\Resources\MascotaResource::getUrl('index');
        $urlReclamos = \App\Filament\Resources\ReclamoResource::getUrl('index');
    ?>

    <style>
        .livo-hub-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        @media (max-width: 600px) {
            .livo-hub-grid { grid-template-columns: 1fr; }
        }

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
            'icon' => '👥',
            'badge' => 'VIDA EN COMUNIDAD',
            'title' => 'Comunidad & Servicios del Condominio',
            'description' => 'Accede a los avisos oficiales, marketplace vecinal, votaciones y mascotas.',
            'actions' => null,
        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="livo-hub-grid">
            
            <a href="<?php echo e($urlAvisos); ?>" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(13, 148, 136, 0.2); color: #14b8a6; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    📢
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Muro de Avisos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Comunicados oficiales</div>
                </div>
            </a>

            
            <a href="<?php echo e($urlMarketplace); ?>" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(245, 158, 11, 0.2); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🛒
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Marketplace Vecinal</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Compra y venta entre vecinos</div>
                </div>
            </a>

            
            <a href="<?php echo e($urlVotaciones); ?>" class="livo-hub-card">
                <div style="width: 52px; height: 52px; border-radius: 1rem; background: rgba(56, 189, 248, 0.2); color: #38bdf8; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🗳️
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Votaciones & Acuerdos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Decisiones de la junta</div>
                </div>
            </a>

            
            <a href="<?php echo e($urlDocumentos); ?>" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(168, 85, 247, 0.2); color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    📁
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Biblioteca de Documentos</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Reglamentos y actas PDF</div>
                </div>
            </a>

            
            <a href="<?php echo e($urlMascotas); ?>" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(236, 72, 153, 0.2); color: #ec4899; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    🐾
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Mis Mascotas</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Registro del padrón</div>
                </div>
            </a>

            
            <a href="<?php echo e($urlReclamos); ?>" class="livo-hub-card">
                <div style="width: 50px; height: 50px; border-radius: 1rem; background: rgba(16, 185, 129, 0.2); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.6rem;">
                    💬
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem;">Reclamos & Reportes</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Sugerencias a la junta</div>
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
<?php endif; ?><?php /**PATH C:\laragon\www\sistema-condominio\resources\views/filament/vecino/pages/comunidad-hub.blade.php ENDPATH**/ ?>