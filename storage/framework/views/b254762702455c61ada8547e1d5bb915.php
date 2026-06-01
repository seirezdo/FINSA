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
            <?php if($clienteSeleccionado): ?>
                <?php echo e(__('Nuevo Préstamo para: ')); ?> <?php echo e($clienteSeleccionado->nombre); ?>

            <?php else: ?>
                <?php echo e(__('Crear Nuevo Préstamo')); ?>

            <?php endif; ?>
        </h2>
     <?php $__env->endSlot(); ?>

    
     <?php $__env->slot('backUrl', null, []); ?> 
        <?php echo e($clienteSeleccionado ? route('clientes.show', $clienteSeleccionado->id) : route('clientes.index')); ?>

     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Nuevo Préstamo Grupal</h2>

                <form action="<?php echo e(route('prestamos.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- GESTIÓN DINÁMICA DEL CLIENTE -->
                    <div class="mb-4">
                        <?php if($clienteSeleccionado): ?>
                            
                            <p class="mb-2 text-gray-600">Cliente: <strong class="text-gray-900"><?php echo e($clienteSeleccionado->nombre); ?></strong></p>
                            <input type="hidden" name="cliente_id" value="<?php echo e($clienteSeleccionado->id); ?>">
                        <?php else: ?>
                            
                            <label class="block text-gray-700 text-sm font-bold mb-2">Seleccionar Cliente</label>
                            <select name="cliente_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                                <option value="">-- Seleccionar Cliente --</option>
                                <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($c->id); ?>" <?php echo e(old('cliente_id') == $c->id ? 'selected' : ''); ?>>
                                        <?php echo e($c->nombre); ?> (<?php echo e($c->curp); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['cliente_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php endif; ?>
                    </div>

                    <!-- NUEVO: FECHA DE ENTREGA (Para calcular el sábado anterior) -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de Entrega / Desembolso</label>
                        <input type="date" name="fecha_desembolso" value="<?php echo e(date('Y-m-d')); ?>" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">El sistema calculará automáticamente el <strong>sábado anterior</strong> a esta fecha como inicio de la semana 1.</p>
                    </div>

                    <!-- MONTO DEL PRÉSTAMO -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Monto del Préstamo ($)</label>
                        <input type="number" name="monto_prestado" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" placeholder="Ej. 10000" required>
                    </div>
   <!-- 🔥 NUEVO: SELECCIÓN DE AVAL (RF-04) 🔥 -->
                    <div class="mb-6 bg-yellow-50 p-4 border border-yellow-200 rounded-md">
                        <label class="block text-gray-800 text-sm font-bold mb-2">Seleccionar Aval (Garantía Solidaria)</label>
                        <select name="aval_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                            <option value="">-- Seleccionar Aval --</option>
                            <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $aval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                
                                <?php if(!$clienteSeleccionado || $clienteSeleccionado->id !== $aval->id): ?>
                                    <option value="<?php echo e($aval->id); ?>" <?php echo e(old('aval_id') == $aval->id ? 'selected' : ''); ?>>
                                        <?php echo e($aval->nombre); ?> - CURP: <?php echo e($aval->curp); ?>

                                    </option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['aval_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-yellow-700 mt-2">
                            * El sistema bloqueará la solicitud si el aval seleccionado ya tiene un crédito activo, o si ya es aval de otra persona.
                        </p>
                    </div>
                    <!-- AVISO DE CUOTAS -->
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <p class="text-sm text-blue-800 italic">
                            * Al confirmar, se generarán automáticamente <strong>12 cuotas semanales</strong> del <strong>12.5%</strong> cada una.
                        </p>
                        
                        <input type="hidden" name="semanas" value="12">
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex items-center justify-end">
                        <?php if($clienteSeleccionado): ?>
                            <a href="<?php echo e(route('clientes.show', $clienteSeleccionado->id)); ?>" class="mr-4 text-gray-600 hover:underline">Cancelar</a>
                        <?php else: ?>
                            <a href="<?php echo e(route('clientes.index')); ?>" class="mr-4 text-gray-600 hover:underline">Cancelar</a>
                        <?php endif; ?>
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Autorizar Préstamo
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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/prestamos/create.blade.php ENDPATH**/ ?>