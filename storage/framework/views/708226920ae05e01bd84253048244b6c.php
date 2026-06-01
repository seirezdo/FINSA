<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <!-- Page Heading -->
           <?php if(isset($header)): ?>
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex-1">
                            <?php echo e($header); ?>

                        </div>

                        
                        <?php if(isset($backUrl)): ?>
                            <div class="ml-4">
                                <a href="<?php echo e($backUrl); ?>" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                                    &larr; Volver
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main>
                <?php echo e($slot); ?>

            </main>
        </div>

      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Si el controlador envía un 'error' (tu código anterior)
            <?php if(session('error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: '¡Acción Denegada!',
                    text: '<?php echo session('error'); ?>',
                    confirmButtonColor: '#ef4444', 
                    background: '#ffffff',
                });
            <?php endif; ?>

            // 2. Si el controlador envía un 'success' (tu código anterior)
            <?php if(session('success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: '<?php echo session('success'); ?>',
                    confirmButtonColor: '#22c55e', 
                    timer: 3000, 
                    showConfirmButton: false
                });
            <?php endif; ?>

            // 3. Disparador de Alertas de Error del Aval (Nuevo)
            <?php if(session('swal_error')): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Operación Rechazada',
                    text: '<?php echo session('swal_error'); ?>',
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'Entendido'
                });
            <?php endif; ?>

            // 4. Disparador de Alertas de Éxito del Aval (Nuevo)
            <?php if(session('swal_success')): ?>
                Swal.fire({
                    icon: 'success',
                    title: '¡Operación Exitosa!',
                    text: '<?php echo session('swal_success'); ?>',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: 'Continuar'
                });
            <?php endif; ?>

        });
    </script>
    </body>
</html><?php /**PATH D:\laragon\www\Prueba1\resources\views/layouts/app.blade.php ENDPATH**/ ?>