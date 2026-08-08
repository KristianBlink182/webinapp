<?php
    $tenant = \Filament\Facades\Filament::getTenant();
    $hasCustomLogo = $tenant && !empty($tenant->logo) && file_exists(storage_path('app/public/' . $tenant->logo));
?>

<style>
    .livo-brand-word {
        color: #ffffff !important;
        font-weight: 900 !important;
        font-size: 1.4rem !important;
        letter-spacing: 0.05em !important;
    }
    html:not(.dark) .livo-brand-word {
        color: #0f172a !important;
    }
</style>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasCustomLogo): ?>
    <img src="<?php echo e(asset('storage/' . $tenant->logo)); ?>" alt="Logo" class="h-9 w-auto object-contain">
<?php else: ?>
    <div style="display: flex; align-items: center; gap: 0.6rem;">
        
        <img src="<?php echo e(asset('favicon.ico')); ?>" alt="LIVO" style="height: 32px; width: 32px; object-fit: contain; filter: none !important;">
        
        
        <span class="livo-brand-word">LIVO</span>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php /**PATH C:\laragon\www\sistema-condominio\resources\views/filament/components/brand-logo.blade.php ENDPATH**/ ?>