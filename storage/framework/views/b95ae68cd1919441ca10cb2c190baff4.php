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
            <?php echo e(__('Expediente Detallado: ')); ?> <?php echo e($prestamo->cliente->nombre); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- ============================================== -->
                <!-- COLUMNA IZQUIERDA: Panel de Resumen y Modal    -->
                <!-- ============================================== -->
                <div class="w-full md:w-1/3" x-data="{ openModal: false }">
                    
                    <!-- Tarjeta de Estado de Cuenta -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2 uppercase text-gray-600">Estado de Cuenta</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Total a Recuperar</span>
                                <span class="block text-2xl font-bold text-indigo-700">$<?php echo e(number_format($prestamo->monto_total_pagar, 2)); ?></span>
                            </div>
                            
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Monto Recuperado</span>
                                <span class="block text-xl font-bold text-green-600">
                                    $<?php echo e(number_format($prestamo->calendarioPagos->flatMap->pagos->sum('monto_pagado'), 2)); ?>

                                </span>
                            </div>

                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Estatus Global</span>
                                <span class="mt-1 px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                    <?php echo e($prestamo->estado === 'liquidado' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'); ?>">
                                    <?php echo e(strtoupper($prestamo->estado)); ?>

                                </span>
                            </div>

                            <!-- BOTÓN PARA REGISTRAR ABONO -->
                            <?php if($prestamo->estado !== 'liquidado'): ?>
                            <div class="pt-4 mt-4 border-t border-gray-200">
                                <button @click="openModal = true" class="w-full flex justify-center items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg shadow-md transition transform hover:scale-105">
                                    <i class="bi bi-currency-dollar mr-2"></i> Registrar Abono
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- VENTANA EMERGENTE (MODAL) DE ALPINE.JS -->
                    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <!-- Fondo oscuro -->
                            <div x-show="openModal" @click="openModal = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                            
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            
                            <!-- Contenedor del Formulario -->
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form action="<?php echo e(route('pagos.store')); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <!-- ID oculto del préstamo -->
                                    <input type="hidden" name="prestamo_id" value="<?php echo e($prestamo->id); ?>">
                                    
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                            Registrar Nuevo Abono
                                        </h3>
                                        
                                        <div class="space-y-4">
                                            <!-- NUEVO CAMPO: Fecha de Recuperación -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Fecha del Abono / Recuperación</label>
                                                <input type="date" name="fecha_pago" required
                                                    value="<?php echo e(date('Y-m-d')); ?>" 
                                                    max="<?php echo e(date('Y-m-d')); ?>"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                                <p class="text-[10px] text-gray-400 mt-1">Selecciona la fecha exacta en la que se recibió el dinero.</p>
                                            </div>

                                            <!-- Monto del Pago -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Monto a Cobrar ($)</label>
                                                <input type="number" step="0.01" name="monto_pagado" required
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    placeholder="Ej. 500.00">
                                            </div>

                                            <!-- Método de Pago -->
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                                                <select name="metodo_pago" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="efectivo">Efectivo</option>
                                                    <option value="transferencia">Transferencia</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> <!-- FIN CONTENEDOR MODAL CORREGIDO -->
                                    
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                                            Guardar Pago
                                        </button>
                                        <button type="button" @click="openModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- COLUMNA DERECHA: Tabla de Comportamiento       -->
                <!-- ============================================== -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg shadow-lg">
                        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
                            <span class="font-bold">Historial de Cuotas</span>
                            <span class="text-xs uppercase text-gray-400">Auditoría de Pagos Reales</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-center">
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Semana</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Esperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-green-700">Recuperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-center">
                                    <?php $__currentLoopData = $prestamo->calendarioPagos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cuota): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                       <?php
                                        $totalAbonado = $cuota->pagos->sum('monto_pagado');
                                        $restante = $cuota->monto_esperado - $totalAbonado;
                                        
                                        // 1. Extraemos la fecha real de la cuota
                                        $vencimiento = \Carbon\Carbon::parse($cuota->fecha_vencimiento);
                                        
                                        // 2. 🔥 NUEVA LÓGICA DE CORTE 🔥
                                        // Calculamos cuál fue el último sábado que pasó (o el de hoy si es sábado)
                                        $sabadoObjetivo = now()->isSaturday() 
                                                            ? now()->startOfDay() 
                                                            : now()->previous('Saturday')->startOfDay();
                                        
                                        // 3. Verificamos si esta cuota es exactamente la del sábado con el que estamos trabajando
                                        $esSemanaActual = $vencimiento->isSameDay($sabadoObjetivo);
                                    ?>
                                    
                                    <tr class="<?php echo e($cuota->numero_semana > 12 ? 'bg-red-50' : 'hover:bg-gray-50'); ?> transition">
                                        
                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                <?php echo e($vencimiento->format('d/m/Y')); ?>

                                            </div>
                                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">
                                                Semana <?php echo e($cuota->numero_semana); ?>

                                            </div>
                                            <?php if($cuota->numero_semana > 12): ?>
                                                <span class="text-[10px] font-black text-red-600 uppercase animate-pulse">Extensión por Mora</span>
                                            <?php endif; ?>
                                        </td>

                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            $<?php echo e(number_format($cuota->monto_esperado, 2)); ?>

                                        </td>
                                        
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                            $<?php echo e(number_format($totalAbonado, 2)); ?>

                                            <?php if($cuota->pagos->count() > 1): ?>
                                                <span class="block text-[9px] text-gray-400 italic">(<?php echo e($cuota->pagos->count()); ?> abonos)</span>
                                            <?php endif; ?>
                                        </td>

                                        
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm 
                                                <?php echo e(in_array($cuota->estado, ['pagado', 'recuperado']) ? 'bg-green-100 text-green-800 border border-green-200' : 
                                                  ($cuota->estado === 'pendiente' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                                  'bg-red-100 text-red-800 border border-red-200')); ?>">
                                                <?php echo e(strtoupper($cuota->estado)); ?>

                                            </span>
                                        </td>
                                        
                                        
                                        <td class="px-2 py-4">
                                            <div class="flex flex-wrap items-center justify-center gap-2">
                                                
                                                
                                                <?php if(in_array($cuota->estado, ['pendiente', 'falla', 'falla_penalizada']) && $restante > 0): ?>
                                                    
                                                    
                                                    <form action="<?php echo e(route('pagos.registrar')); ?>" method="POST" class="flex items-center gap-1 m-0">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="calendario_pago_id" value="<?php echo e($cuota->id); ?>">
                                                        <input type="number" name="monto_pagado" step="0.01" min="1" max="<?php echo e($restante); ?>" value="<?php echo e($restante > 0 ? $restante : ''); ?>" class="border border-gray-300 rounded px-1 py-1 w-16 text-xs text-center" required placeholder="$">
                                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-2 py-1 rounded shadow-sm transition text-xs font-semibold">
                                                            Abonar
                                                        </button>
                                                    </form>

                                                    
                                                    <?php if($cuota->estado === 'pendiente'): ?>
                                                        <?php if($esSemanaActual): ?>
                                                            <form action="<?php echo e(route('pagos.update', $cuota->id)); ?>" method="POST" class="m-0">
                                                                <?php echo csrf_field(); ?>
                                                                <?php echo method_field('PUT'); ?>
                                                                <input type="hidden" name="accion" value="falla">
                                                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded shadow-sm transition text-xs font-semibold" onclick="return confirm('¿Confirmar falla por los $<?php echo e(number_format($restante, 2)); ?> restantes?')">
                                                                    Falla
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-[9px] text-gray-400 italic px-1 text-center leading-tight">Solo<br>actual</span>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    
                                                    <?php if(in_array($cuota->estado, ['falla', 'falla_penalizada'])): ?>
                                                        <form action="<?php echo e(route('pagos.update', $cuota->id)); ?>" method="POST" class="m-0">
                                                            <?php echo csrf_field(); ?>
                                                            <?php echo method_field('PUT'); ?>
                                                            <input type="hidden" name="accion" value="recuperado">
                                                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded shadow-sm transition text-xs font-semibold">
                                                                Recuperar
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>

                                                
                                                <?php elseif(in_array($cuota->estado, ['pagado', 'recuperado'])): ?>
                                                    <span class="text-green-600 font-bold flex items-center text-xs">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                                                        Cerrada
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
<?php endif; ?><?php /**PATH D:\laragon\www\Prueba1\resources\views/prestamos/show.blade.php ENDPATH**/ ?>