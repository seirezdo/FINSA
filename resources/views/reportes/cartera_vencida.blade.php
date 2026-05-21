<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reporte de Cartera Vencida') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-red-600 text-white font-bold">
                    Préstamos con Atraso o en Periodo de Mora (Semana 13+)
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="px-3 py-3">Cliente</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Monto Total</th>
                                <th class="px-6 py-3">Pagado</th>
                                <th class="px-3 py-3">Semanas Pagadas</th>
                            
                            </tr>
                        </thead>
                      <tbody class="bg-white divide-y divide-gray-200">
    @forelse($carteraVencida as $prestamo)
    {{-- Toda la fila es clickeable y redirige al expediente [3] --}}
    <tr onclick="window.location='{{ route('prestamos.show', $prestamo->id) }}'" 
        class="hover:bg-red-50 cursor-pointer transition group">
        
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $prestamo->cliente->persona->nombre }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
            {{ $prestamo->grupo->nombre ?? 'N/A' }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-center">
            ${{ number_format($prestamo->monto_total_pagar, 2) }}
        </td>

        {{-- Restaurado: Monto recuperado hasta el momento [4] --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold text-center">
            ${{ number_format($prestamo->calendarioPagos->where('estado', 'pagado')->sum('monto_esperado'), 2) }}
        </td>

        {{-- Restaurado: Progreso de semanas (ej. 4/12) [5] --}}
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">
                {{ $prestamo->calendarioPagos->where('estado', 'pagado')->count() }} / {{ $prestamo->semanas }}
            </span>
            {{-- Aviso de mora visual --}}
            @if($prestamo->calendarioPagos->where('numero_semana', '>', 12)->count() > 0)
                <span class="block text-[9px] text-red-600 font-black uppercase mt-1">En Prórroga</span>
            @endif
        </td>

        {{-- Indicador de acción sin botón estorboso --}}
      
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
            No hay préstamos en cartera vencida actualmente. ¡Excelente gestión!
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