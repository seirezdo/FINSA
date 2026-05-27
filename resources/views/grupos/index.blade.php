<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Grupos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alerta de Éxito -->
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- CONTENEDOR ÚNICO BLANCO (Agrupa la barra superior y la tabla) -->
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg">
                
                <!-- Barra superior con el buscador y el botón -->
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center gap-4">
                    <form method="GET" action="{{ route('grupos.index') }}" class="w-full md:flex-grow flex">
                        <input type="text" name="buscar" placeholder="Buscar por nombre de grupo..." value="{{ request('buscar') }}" 
                               class="w-full rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <button type="submit" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                            Buscar
                        </button>
                    </form>

                    <a href="{{ route('grupos.create') }}" class="md:w-auto text-center whitespace-nowrap flex-shrink-0 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm">
                        + Nuevo Grupo
                    </a>
                </div>

                <!-- Tabla (Conectada directamente debajo de la barra sin espacios) -->
                <div class="overflow-x-auto">
                    <table class="table-auto w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 uppercase text-sm leading-normal">
                                <th class="p-4 font-semibold">ID</th>
                                <th class="p-4 font-semibold">Nombre del Grupo</th>
                                <th class="p-4 font-semibold">Día de Reunión</th>
                                <th class="p-4 font-semibold">Plaza</th>
                                <th class="p-4 font-semibold">Promotora</th>
                                <th class="p-4 font-semibold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 dark:text-gray-400 text-sm font-light">
                            @forelse($grupos as $grupo)
                                <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                    <td class="p-4">{{ $grupo->id }}</td>
                                    <td class="p-4 font-bold">{{ $grupo->nombre }}</td>
                                    <td class="p-4">
                                        <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">{{ $grupo->dia_reunion }}</span>
                                    </td>
                                    <td class="p-4">{{ $grupo->plaza->nombre ?? 'Sin Plaza' }}</td>
                                    <td class="p-4">{{ $grupo->promotora->name ?? 'Sin Asignar' }}</td>
                                    <td class="p-4 text-center">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900 font-bold mx-1">Ver</a>
                                        <a href="#" class="text-yellow-600 hover:text-yellow-900 font-bold mx-1">Editar</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center p-6 text-gray-500 font-medium">
                                        No hay grupos registrados en el sistema.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación (Se muestra solo si hay suficientes registros) -->
                @if($grupos->hasPages())
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $grupos->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>