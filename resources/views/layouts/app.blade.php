<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
           @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                        <div class="flex-1">
                            {{ $header }}
                        </div>

                        {{-- Botón de Volver: AHORA ES DINÁMICO Y OPCIONAL --}}
                        @isset($backUrl)
                            <div class="ml-4">
                                <a href="{{ $backUrl }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                                    &larr; Volver
                                </a>
                            </div>
                        @endisset
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                
                // Si el controlador envía un 'error' (como el rechazo del préstamo)
                @if(session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: '¡Acción Denegada!',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#ef4444', // Color rojo Tailwind
                        background: '#ffffff',
                    });
                @endif

                // Si el controlador envía un 'success' (como cuando se crea bien el préstamo)
                @if(session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#22c55e', // Color verde Tailwind
                        timer: 3000, // Se cierra solo en 3 segundos
                        showConfirmButton: false
                    });
                @endif

            });
        </script>
    </body>
</html>