<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Control Financiero') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Sección de Indicadores Rápidos en Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                
                <!-- Card: Total Colocado -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Colocado</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalLent, 2) }}</p>
                    </div>
                </div>

                <!-- Card: Total Recuperado -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Recuperado</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalRecovered, 2) }}</p>
                    </div>
                </div>

                <!-- Card: Tasa de Recuperación -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">% Recuperación</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($recoveryRate, 1) }}%</p>
                    </div>
                </div>

                <!-- Card: Pendiente -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-red-500">
                    <div class="p-6">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Pendiente</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($totalExpected - $totalRecovered, 2) }}</p>
                    </div>
                </div>

            </div>

            <!-- Botonera de Acceso Rápido -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-white uppercase tracking-wider border-b pb-2">Reportes Estratégicos</h3>
                
                <!-- flex-wrap asegura que los botones se acomoden bien en móviles -->
                <div class="flex flex-wrap gap-4 mt-4">
                    
                    <!-- Botón Cartera Vigente (Verde) -->
                    <a href="{{ route('reportes.vigente') }}" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out shadow-sm transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ver Cartera Vigente
                    </a>

                    <!-- Botón Cartera Vencida (Rojo) -->
                    <a href="{{ route('reportes.vencida') }}" class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out shadow-sm transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Ícono de alerta -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Ver Cartera Vencida
                    </a>

                    <!-- Botón Liquidados (Azul) -->
                    <a href="{{ route('reportes.liquidados') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out shadow-sm transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Ícono de check doble o similar -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Ver Liquidados
                    </a>
                    
                    <!-- Botón Extra Sugerido: Ir a Préstamos (Índigo) -->
                    <a href="{{ route('prestamos.index') }}" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-6 rounded-lg transition duration-150 ease-in-out shadow-sm transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <!-- Ícono de carpeta/archivo -->
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Gestión de Préstamos
                    </a>

                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>