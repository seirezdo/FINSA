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
            <?php echo e(__('Expediente Detallado: ')); ?> <?php echo e($prestamo->cliente->persona->nombre); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Panel de Resumen del Crédito -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2 uppercase text-gray-600">Estado de Cuenta</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Total a Recuperar</span>
                                <span class="block text-2xl font-bold text-indigo-700">$<?php echo e(number_format($prestamo->monto_total_pagar, 2)); ?></span>
                            </div>
                            
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Monto Recuperado</span>
                                <span class="block text-xl font-bold text-green-600">
                                    $<?php echo e(number_format($prestamo->calendarioPagos->flatMap->pagos->sum('monto_pagado'), 2)); ?>

                                </span>
                            </div>

                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Estatus Global</span>
                                <span class="mt-1 px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                    <?php echo e($prestamo->estado === 'liquidado' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo e(strtoupper($prestamo->estado)); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Comportamiento Semanal -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg shadow-lg">
                        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
                            <span class="font-bold">Historial de Cuotas</span>
                            <span class="text-xs uppercase text-gray-400">Auditoría de Pagos Reales</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-center">
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Semana</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Esperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-green-700">Recuperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-center">
                                    <?php $__currentLoopData = $prestamo->calendarioPagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($cuota->numero_semana > 12 ? 'bg-red-50' : 'hover:bg-gray-50'); ?> transition">
                                        
                                        <!-- Identificador de Semana -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">Semana <?php echo e($cuota->numero_semana); ?></div>
                                            <?php if($cuota->numero_semana > 12): ?>
                                                <span class="text-[10px] font-black text-red-600 uppercase animate-pulse">Extension por Mora</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Monto Esperado -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            $<?php echo e(number_format($cuota->monto_esperado, 2)); ?>

                                        </td>

                                        <!-- Comportamiento de Pago (Suma de abonos) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                            $<?php echo e(number_format($cuota->pagos->sum('monto_pagado'), 2)); ?>

                                            <?php if($cuota->pagos->count() > 1): ?>
                                                <span class="block text-[9px] text-gray-400 italic">(<?php echo e($cuota->pagos->count()); ?> abonos)</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Estatus con Badges dinámicos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                                <?php echo e($cuota->estado === 'pagado' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                                   ($cuota->estado === 'parcial' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                                   'bg-gray-100 text-gray-400')); ?>">
                                                <?php echo e(strtoupper($cuota->estado)); ?>

                                            </span>
                                        </td>

                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/prestamos/show.blade.php ENDPATH**/ ?>