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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 pb-0"> 
                
                
                <div class="p-6 bg-gray-50 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-700 uppercase">
                            Gestión de Plazas
                        </h2>
                        
                        
                        <div class="relative w-1/3">
                            <input type="text" id="search_plazas" placeholder="Buscar por nombre o zona..." 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 text-sm">
                        </div>

                        <a href="<?php echo e(route('plazas.create')); ?>" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-bold uppercase">
                            + Nueva Plaza
                        </a>
                    </div>
                </div>

                
                <div id="table-container-plazas" class="overflow-x-auto">
                    <?php echo $__env->make('plazas.partials.table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        document.getElementById('search_plazas').addEventListener('input', function(e) {
            let query = e.target.value;
            fetch(`/plazas?search=${query}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('table-container-plazas').innerHTML = html;
            });
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/plazas/index.blade.php ENDPATH**/ ?>