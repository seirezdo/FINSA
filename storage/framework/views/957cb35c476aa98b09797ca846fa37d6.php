<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Reporte de Cartera Vencida')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-red-600 text-white font-bold">
                    Préstamos con Atraso o en Periodo de Mora (Semana 13+)
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="px-3 py-3">Cliente</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Monto Total</th>
                                <th class="px-6 py-3">Pagado</th>
                                <th class="px-3 py-3">Semanas Pagadas</th>
                            
                            </tr>
                        </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
    <?php $__empty_1 = true; $__currentLoopData = $carteraVencida; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prestamo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    
     <tr onclick="window.location='<?php echo e(route('prestamos.show', $prestamo->id)); ?>'" 
        class="hover:bg-red-50 cursor-pointer transition group">
        
        
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            <?php echo e($prestamo->cliente->nombre); ?>

        </td>

        
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
            <?php echo e($prestamo->grupo->nombre ?? 'Sin asignar'); ?>

        </td>

        
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-center">
            $<?php echo e(number_format($prestamo->monto_total_pagar, 2)); ?>

        </td>

        
        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold text-center">
            $<?php echo e(number_format($prestamo->monto_recuperado ?? 0, 2)); ?>

        </td>

        
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">
                <?php echo e($prestamo->semanas_pagadas); ?> / <?php echo e($prestamo->semanas); ?>

            </span>

            
            <?php if($prestamo->en_prorroga): ?>
                <span class="block text-[9px] text-red-600 font-black uppercase mt-1">En Prórroga</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
            No hay préstamos en cartera vencida actualmente. ¡Excelente gestión!
        </td>
    </tr>
    <?php endif; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\FINSA\resources\views/reportes/cartera_vencida.blade.php ENDPATH**/ ?>