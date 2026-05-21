<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Clientes Liquidados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500">
                <div class="p-6 bg-gray-50 border-b font-bold text-gray-700 uppercase">
                    Préstamos Finalizados con Éxito
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead class="bg-gray-100 text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Monto Recuperado</th>
                                <th class="px-6 py-3">Fecha de Cierre</th>
                                <th class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
    @forelse($liquidados as $prestamo)
    <!-- Añadimos el evento onclick y la clase cursor-pointer -->
    <tr onclick="window.location='{{ route('prestamos.show', $prestamo->id) }}'" 
        class="hover:bg-blue-50 cursor-pointer transition group">
        
        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
            {{ $prestamo->cliente->persona->nombre }}
        </td>
        
        <td class="px-6 py-4 text-sm text-gray-500 text-center">
            {{ $prestamo->grupo->nombre ?? 'N/A' }}
        </td>
        
        <td class="px-6 py-4 text-sm font-bold text-blue-600 text-center">
            ${{ number_format($prestamo->monto_total_pagar, 2) }}
        </td>
        
        <td class="px-6 py-4 text-sm text-gray-500 text-center">
            {{ $prestamo->updated_at->format('d/m/Y') }}
        </td>
        
        <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 text-xs font-bold rounded bg-blue-100 text-blue-800 uppercase">
                {{ strtoupper($prestamo->estado) }}
            </span>
            <!-- Pequeño indicador visual que aparece al pasar el mouse -->
           
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
            No hay registros para mostrar.
        </td>
    </tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>