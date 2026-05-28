<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ejecutivo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisora</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        <?php $__empty_1 = true; $__currentLoopData = $plazas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plaza): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4"><?php echo e($plaza->nombre); ?></td>
            <td class="px-6 py-4"><?php echo e($plaza->zona ?? 'N/A'); ?></td>
            
            
            <td class="px-6 py-4 text-sm"><?php echo e($plaza->ejecutivo->name ?? 'Sin asignar'); ?></td>
            <td class="px-6 py-4 text-sm"><?php echo e($plaza->supervisora->name ?? 'Sin asignar'); ?></td>

            <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($plaza->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'); ?>">
                    <?php echo e(ucfirst($plaza->estado)); ?>

                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <a href="<?php echo e(route('plazas.edit', $plaza)); ?>" class="text-indigo-600 hover:text-indigo-900 font-bold mr-3">Editar</a>
                
                <form action="<?php echo e(route('plazas.destroy', $plaza)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('¿Seguro que deseas eliminar esta plaza?')">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron plazas.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>


<div class="mt-4 px-4 pb-4">
    <?php echo e($plazas->appends(request()->query())->links()); ?>

</div>
<?php /**PATH C:\laragon\www\FINSA\resources\views/plazas/partials/table.blade.php ENDPATH**/ ?>