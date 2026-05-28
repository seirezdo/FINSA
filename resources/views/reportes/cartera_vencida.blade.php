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
        
        {{-- 1. Cliente --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $prestamo->cliente->nombre }}
        </td>

        {{-- 2. CORREGIDO: Faltaba la celda del Grupo para que no se desfasaran las columnas --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
            {{ $prestamo->grupo->nombre ?? 'Sin asignar' }}
        </td>

        {{-- 3. Monto Total --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-center">
            ${{ number_format($prestamo->monto_total_pagar, 2) }}
        </td>

        {{-- 4. Pagado (Optimizado sin N+1) --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold text-center">
            ${{ number_format($prestamo->monto_recuperado ?? 0, 2) }}
        </td>

        {{-- 5. Semanas Pagadas (Ahora sí caerá en su columna correcta) --}}
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="px-2 py-1 text-xs font-bold rounded bg-red-100 text-red-800">
                {{ $prestamo->semanas_pagadas }} / {{ $prestamo->semanas }}
            </span>

            {{-- Aviso de mora visual --}}
            @if($prestamo->en_prorroga)
                <span class="block text-[9px] text-red-600 font-black uppercase mt-1">En Prórroga</span>
            @endif
        </td>
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