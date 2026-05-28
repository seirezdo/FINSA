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
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Encabezado con navegación de regreso -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Editar Expediente: <?php echo e($cliente->nombre); ?></h2>
                <a href="<?php echo e(route('clientes.index')); ?>" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Volver al listado
                </a>
            </div>

            <div class="bg-white p-8 shadow-xl sm:rounded-xl border border-gray-100">
                <form action="<?php echo e(route('clientes.update', $cliente)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?> <!-- VITAL: Indica a Laravel que es una actualización [3] -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Datos de Identidad del Cliente -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                            <input type="text" name="nombre" value="<?php echo e(old('nombre', $cliente->nombre)); ?>" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">N° Documento (CURP/INE)</label>
                            <input type="text" name="numero_documento" value="<?php echo e(old('numero_documento', $cliente->curp)); ?>" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Este dato es único por cliente para evitar fraudes [4].</p>
                        </div>

                        <!-- Datos Operativos (Tabla Clientes) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grupo Asignado</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($grupo->id); ?>" <?php echo e($cliente->grupo_id == $grupo->id ? 'selected' : ''); ?>>
                                        <?php echo e($grupo->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo" <?php echo e($cliente->perfil_riesgo == 'bajo' ? 'selected' : ''); ?>>Bajo</option>
                                <option value="medio" <?php echo e($cliente->perfil_riesgo == 'medio' ? 'selected' : ''); ?>>Medio</option>
                                <option value="alto" <?php echo e($cliente->perfil_riesgo == 'alto' ? 'selected' : ''); ?>>Alto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado del Cliente</label>
                            <select name="estado" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="activo" <?php echo e($cliente->estado == 'activo' ? 'selected' : ''); ?>>Activo</option>
                                <option value="inactivo" <?php echo e($cliente->estado == 'inactivo' ? 'selected' : ''); ?>>Inactivo</option>
                                <option value="bloqueado" <?php echo e($cliente->estado == 'bloqueado' ? 'selected' : ''); ?>>Bloqueado</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <textarea name="direccion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500"><?php echo e(old('direccion', $cliente->direccion)); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <a href="<?php echo e(route('clientes.index')); ?>" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
                            Actualizar Expediente
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
<?php endif; ?><?php /**PATH C:\laragon\www\FINSA\resources\views/clientes/edit.blade.php ENDPATH**/ ?>