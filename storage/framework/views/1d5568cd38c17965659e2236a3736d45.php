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
            <?php echo e(__('Registrar Nuevo Cliente')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    
     <?php $__env->slot('backUrl', null, []); ?> 
        <?php echo e(route('clientes.index')); ?>

     <?php $__env->endSlot(); ?>
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
                        <!-- Nombre Completo (Unificado) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <input type="text" name="nombre" value="<?php echo e(old('nombre')); ?>" placeholder="Ej. Sergio García" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            <?php $__errorArgs = ['nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- CURP (Antes numero_documento) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CURP</label>
                            <input type="text" name="curp" value="<?php echo e(old('curp')); ?>" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 uppercase" required>
                            <?php $__errorArgs = ['curp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" name="telefono" value="<?php echo e(old('telefono')); ?>" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            <?php $__errorArgs = ['telefono'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Asignar a Grupo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asignar a Grupo</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                <option value="">Seleccione un grupo...</option>
                                <?php $__currentLoopData = $grupos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grupo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    
                                    <option value="<?php echo e($grupo->id); ?>" <?php echo e(old('grupo_id') == $grupo->id ? 'selected' : ''); ?>>
                                        <?php echo e($grupo->nombre); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['grupo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <!-- Dirección (Opcional, si la requieres en la creación) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" name="direccion" value="<?php echo e(old('direccion')); ?>" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            <?php $__errorArgs = ['direccion'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                   

                       <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo" <?php echo e(old('perfil_riesgo') == 'bajo' ? 'selected' : ''); ?>>Bajo</option>
                                <option value="medio" <?php echo e(old('perfil_riesgo') == 'medio' ? 'selected' : ''); ?>>Medio</option>
                                <option value="alto" <?php echo e(old('perfil_riesgo') == 'alto' ? 'selected' : ''); ?>>Alto</option>
                            </select>
                            <?php $__errorArgs = ['perfil_riesgo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                    </div> <!-- Cierre del grid -->

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-end mt-6 border-t pt-4">
                        <a href="<?php echo e(route('clientes.index')); ?>" class="text-gray-600 hover:text-gray-900 mr-4 font-medium transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-150">
                            Guardar Cliente
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
<?php endif; ?><?php /**PATH C:\laragon\www\FINSA\resources\views/clientes/create.blade.php ENDPATH**/ ?>