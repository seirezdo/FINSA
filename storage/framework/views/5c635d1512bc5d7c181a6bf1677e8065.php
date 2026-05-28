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
            Editar Plaza: <?php echo e($plaza->nombre); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
     <?php $__env->slot('backUrl', null, []); ?> 
        <?php echo e(route('plazas.index')); ?>

     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                
                <form action="<?php echo e(route('plazas.update', $plaza->id)); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?> 
                    <?php echo method_field('PUT'); ?> 

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre de la Plaza</label>
                            <input type="text" name="nombre" value="<?php echo e(old('nombre', $plaza->nombre)); ?>" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Zona -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zona / Región</label>
                            <input type="text" name="zona" value="<?php echo e(old('zona', $plaza->zona)); ?>" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Ejecutivo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ejecutivo Asignado</label>
                            <select name="ejecutivo_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <?php $__currentLoopData = $ejecutivos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ejecutivo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($ejecutivo->id); ?>" <?php echo e((old('ejecutivo_id', $plaza->ejecutivo_id) == $ejecutivo->id) ? 'selected' : ''); ?>>
                                        <?php echo e($ejecutivo->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Supervisora -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supervisora Asignada</label>
                            <select name="supervisora_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <?php $__currentLoopData = $supervisoras; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supervisora): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supervisora->id); ?>" <?php echo e((old('supervisora_id', $plaza->supervisora_id) == $supervisora->id) ? 'selected' : ''); ?>>
                                        <?php echo e($supervisora->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="activo" <?php echo e($plaza->estado == 'activo' ? 'selected' : ''); ?>>Activo</option>
                                <option value="inactivo" <?php echo e($plaza->estado == 'inactivo' ? 'selected' : ''); ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 border-t pt-4">
                        <a href="<?php echo e(route('plazas.index')); ?>" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow">
                            Actualizar Plaza
                        </button>
                    </div>
                </form>
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
<?php endif; ?><?php /**PATH C:\laragon\www\FINSA\resources\views/plazas/edit.blade.php ENDPATH**/ ?>