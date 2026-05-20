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
            <!-- Botón de Gestión Administrativa -->
            <?php if(auth()->user()->role !== \App\Enums\UserRole::CLIENTE): ?>
                <div class="mb-6">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                        Gestión Administrativa
                    </button>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 shadow-xl sm:rounded-xl border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Registrar Nuevo Cliente</h2>
                
                <form action="<?php echo e(route('clientes.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Datos Personales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" value="<?php echo e(old('apellido_paterno')); ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                            <input type="text" name="apellido_materno" value="<?php echo e(old('apellido_materno')); ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                        </div>

                        <!-- Identificación y Ubicación Operativa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">N° Documento (CURP/INE)</label>
                            <input type="text" name="numero_documento" value="<?php echo e(old('numero_documento')); ?>" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            <?php $__errorArgs = ['numero_documento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asignar a Grupo</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                <option value="">Seleccione un grupo...</option>
                                <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($grupo->id); ?>"><?php echo e($grupo->nombre); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo">Bajo</option>
                                <option value="medio">Medio</option>
                                <option value="alto">Alto</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección Completa</label>
                            <textarea name="direccion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500"><?php echo e(old('direccion')); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
                            Guardar Expediente de Cliente
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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/clientes/create.blade.php ENDPATH**/ ?>