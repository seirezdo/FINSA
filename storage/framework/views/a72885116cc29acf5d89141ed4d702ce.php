<div class="overflow-x-auto border rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 text-center">
    <thead class="bg-gray-100 text-xs font-medium text-gray-500 uppercase">
        <tr>
            <th class="px-6 py-3 text-left">Nombre Completo</th>
            <th class="px-6 py-3">Documento</th>
            <th class="px-6 py-3">Grupo</th>
            <th class="px-6 py-3">Estado</th>
            <th class="px-6 py-3 text-right">Acciones</th>
        </tr>
    </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr onclick="window.location='<?php echo e(route('clientes.show', $cliente->id)); ?>'" 
                        class="hover:bg-gray-50 cursor-pointer transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            
                            <?php echo e($cliente->nombre); ?>

                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        
                        <?php echo e($cliente->curp); ?>

                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?php echo e($cliente->grupo->nombre ?? 'Sin asignar'); ?>

                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo e($cliente->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                            <?php echo e(ucfirst($cliente->estado)); ?>

                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-medium" onclick="event.stopPropagation();">
                        <a href="<?php echo e(route('clientes.edit', $cliente)); ?>" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Editar</a>
                        
                        
                        
                        <?php if(auth()->user()->role === \App\Enums\UserRole::ADMIN->value): ?>
                            <form action="<?php echo e(route('clientes.destroy', $cliente->id)); ?>" method="POST" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('¿Eliminar cliente?')">
                                    Eliminar
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No se encontraron clientes registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="px-6 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
    <?php if($clientes->hasPages()): ?>
        <?php echo e($clientes->appends(request()->query())->links()); ?>

    <?php else: ?>
        <div class="text-xs text-gray-500 italic">
            Mostrando todos los registros (<?php echo e($clientes->count()); ?>)
        </div>
    <?php endif; ?>
</div><?php /**PATH C:\laragon\www\FINSA\resources\views/clientes/partials/table.blade.php ENDPATH**/ ?>