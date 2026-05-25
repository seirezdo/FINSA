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
            <?php echo e(__('Auditoría Global de Pagos')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-600">
                
                <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 uppercase tracking-wider">
                        <i class="bi bi-cash-stack"></i> Flujo de Efectivo Reciente
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Folio / Fecha</th>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Monto</th>
                                <th class="px-6 py-3 text-left">Método</th>
                                <th class="px-6 py-3 text-left bg-indigo-50 text-indigo-800 font-bold border-l border-indigo-100">
                                    Registrado Por (Cajero)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <?php $__empty_1 = true; $__currentLoopData = $pagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pago): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">
                                        #<?php echo e(str_pad($pago->id, 5, '0', STR_PAD_LEFT)); ?> <br>
                                        <span class="text-xs text-gray-500 font-normal">
                                            <?php echo e(\Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y h:i A')); ?>

                                        </span>
                                    </td>
                                    
                                    
                                    <td class="px-6 py-4 text-gray-700">
                                        <?php echo e($pago->cuota->prestamo->cliente->persona->nombre ?? 'N/A'); ?>

                                        <br>
                                        <span class="text-xs text-gray-400">Préstamo #<?php echo e($pago->cuota->prestamo_id ?? ''); ?></span>
                                    </td>
                                    
                                    <td class="px-6 py-4 font-black text-green-600">
                                        + $<?php echo e(number_format($pago->monto_pagado, 2)); ?>

                                    </td>
                                    
                                    <td class="px-6 py-4 uppercase text-xs text-gray-500 font-semibold">
                                        <?php echo e($pago->metodo_pago); ?>

                                    </td>
                                    
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-800 font-medium bg-indigo-50/30 border-l border-indigo-50">
                                        <?php echo e($pago->usuario->persona->nombre ?? 'Sistema Automático'); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No hay pagos registrados en el sistema todavía.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                
                <div class="p-4 border-t bg-gray-50">
                    <?php echo e($pagos->links()); ?>

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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/prestamos/pagos/index.blade.php ENDPATH**/ ?>