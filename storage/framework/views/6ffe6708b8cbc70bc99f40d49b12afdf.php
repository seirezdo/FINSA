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
            Expediente del Cliente: <?php echo e($cliente->nombre); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
     <?php $__env->slot('backUrl', null, []); ?> 
        <?php echo e(route('clientes.index')); ?>

     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
         <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Información Personal</h3>
                
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                    <div>
                        <span class="font-bold text-gray-500">Nombre del Cliente:</span> <br> 
                        <span class="text-gray-900 font-semibold"><?php echo e($cliente->nombre); ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Grupo:</span> <br> 
                        <span class="text-gray-900"><?php echo e($cliente->grupo->nombre ?? 'Sin grupo'); ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Promotora:</span> <br> 
                        
                        <span class="text-gray-900"><?php echo e($cliente->grupo->promotora->name ?? 'Sin asignar'); ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Fecha de Registro/Crédito:</span> <br> 
                        <span class="text-gray-900"><?php echo e(\Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y')); ?></span>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm pt-4 border-t border-gray-100">
                    <div>
                        <span class="font-bold text-gray-500">Documento (CURP):</span> <br> 
                        <span class="text-gray-900"><?php echo e($cliente->curp); ?></span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Teléfono:</span> <br> 
                        <span class="text-gray-900"><?php echo e($cliente->telefono ?? 'No registrado'); ?></span>
                    </div>
                </div>
            </div>
<div class="flex justify-between items-center border-b pb-2 mb-4">
    <h3 class="text-lg font-bold text-gray-700">Historial de Créditos</h3>
    
    
    <a href="<?php echo e(route('prestamos.create', ['cliente_id' => $cliente->id])); ?>" 
       class="px-4 py-2 bg-green-600 text-white rounded-md text-xs font-bold uppercase hover:bg-green-700 transition">
        + Nuevo Préstamo
    </a>
</div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Historial de Créditos</h3>
                
                <?php $__empty_1 = true; $__currentLoopData = $prestamos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prestamo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded-lg p-4 mb-4 flex justify-between items-center <?php echo e($prestamo->estado == 'liquidado' ? 'bg-blue-50 border-blue-200' : 'bg-gray-50'); ?>">
                        
                        
                        <div>
                            <p class="font-bold text-gray-800 text-lg">Préstamo #$<?php echo e(number_format($prestamo->monto_total_pagar, 2)); ?></p>
                            <p class="text-sm text-gray-500">Recuperado: <span class="text-green-600 font-bold">$<?php echo e(number_format($prestamo->monto_recuperado ?? 0, 2)); ?></span></p>
                        </div>

                        
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase font-bold">Progreso</p>
                            <span class="px-3 py-1 text-sm font-bold rounded bg-indigo-100 text-indigo-800">
                                <?php echo e($prestamo->semanas_pagadas); ?> / <?php echo e($prestamo->semanas); ?> Semanas
                            </span>
                            <?php if($prestamo->en_prorroga && $prestamo->estado != 'liquidado'): ?>
                                <span class="block text-[10px] text-red-600 font-black uppercase mt-1">En Prórroga / Atraso</span>
                            <?php endif; ?>
                        </div>

                        
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold">Estatus</p>
                            <?php if($prestamo->estado === 'liquidado'): ?>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                    LIQUIDADO
                                </span>
                            <?php elseif($prestamo->estado === 'activo'): ?>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                    ACTIVO
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-gray-100 text-gray-800">
                                    <?php echo e(strtoupper($prestamo->estado)); ?>

                                </span>
                            <?php endif; ?>
                            
                            
                            <div class="mt-2">
                                <a href="<?php echo e(route('prestamos.show', $prestamo->id)); ?>" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">Ver pagos &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 italic text-center py-4">Este cliente aún no tiene historial de créditos.</p>
                <?php endif; ?>
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
<?php endif; ?><?php /**PATH C:\laragon\www\FINSA\resources\views/clientes/show.blade.php ENDPATH**/ ?>