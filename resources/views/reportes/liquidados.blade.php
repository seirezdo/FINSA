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
                                <th class="px-6 py-3">Semanas</th>
                                 <th class="px-6 py-3">Finalizacion de credito</th>
                            </tr>
                        </thead>
     <tbody class="bg-white divide-y divide-gray-200">
    @forelse($liquidados as $prestamo)
    <tr onclick="window.location='{{ route('prestamos.show', $prestamo->id) }}'" 
        class="hover:bg-blue-50 cursor-pointer transition group">
        
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $prestamo->cliente->persona->nombre }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
            {{ $prestamo->grupo->nombre ?? 'N/A' }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold text-center line-through">
            ${{ number_format($prestamo->monto_total_pagar, 2) }}
        </td>

        {{-- Confirmación de recuperación total --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-bold text-center">
            ${{ number_format($prestamo->calendarioPagos->sum('monto_esperado'), 2) }}
        </td>

        {{-- Estado finalizado --}}
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-500 uppercase">
            {{ $prestamo->semanas }} / {{ $prestamo->semanas }} Pagadas
        </td>

         <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-500 uppercase">
           FINALIZADO EL {{ $prestamo->updated_at->format('d/m/Y') }} →
        </td>
       
    </tr>
    @empty
    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No hay préstamos liquidados en el historial.</td></tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>