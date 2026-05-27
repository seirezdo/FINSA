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
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <?php echo e(__('Gestión de Grupos')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alerta de Éxito -->
            <?php if(session('success')): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- CONTENEDOR ÚNICO BLANCO (Agrupa la barra superior y la tabla) -->
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                
                <!-- Barra superior con el buscador y el botón -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="<?php echo e(route('grupos.index')); ?>" class="w-full md:flex-grow flex">
                        <input type="text" name="buscar" placeholder="Buscar por nombre de grupo..." value="<?php echo e(request('buscar')); ?>" 
                               class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                            Buscar
                        </button>
                    </form>

                    <a href="<?php echo e(route('grupos.create')); ?>" class="md:w-auto text-center whitespace-nowrap flex-shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm">
                        + Nuevo Grupo
                    </a>
                </div>

                <!-- Tabla (Conectada directamente debajo de la barra sin espacios) -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Nombre del Grupo</th>
                                <th class="p-4 font-semibold">Día de Reunión</th>
                                <th class="p-4 font-semibold">Plaza</th>
                                <th class="p-4 font-semibold">Promotora</th>
                                <th class="p-4 font-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-400 text-sm font-light">
                            <?php $__empty_1 = true; $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <td class="p-4"><?php echo e($grupo->id); ?></td>
                                    <td class="p-4 font-bold"><?php echo e($grupo->nombre); ?></td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?php echo e($grupo->dia_reunion); ?></span>
                                    </td>
                                    <td class="p-4"><?php echo e($grupo->plaza->nombre ?? 'Sin Plaza'); ?></td>
                                    <td class="p-4"><?php echo e($grupo->promotora->name ?? 'Sin Asignar'); ?></td>
                                    <td class="p-4 text-center">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-bold mx-1">Ver</a>
                                        <a href="#" class="text-yellow-600 hover:text-yellow-900 font-bold mx-1">Editar</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center p-6 text-gray-500 font-medium">
                                        No hay grupos registrados en el sistema.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación (Se muestra solo si hay suficientes registros) -->
                <?php if($grupos->hasPages()): ?>
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <?php echo e($grupos->links()); ?>

                    </div>
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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/grupos/index.blade.php ENDPATH**/ ?>