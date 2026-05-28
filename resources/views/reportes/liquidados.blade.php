<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Clientes Liquidados') }}
        </h2>
    </x-slot>
  {{-- BOTÓN DE VOLVER: Como no hay menú de reportes, regresamos al inicio --}}
    <x-slot name="backUrl">
        {{ route('dashboard') }}
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
            {{-- CORREGIDO: Faltaba el título para el Monto Total --}}
            <th class="px-6 py-3">Monto Total</th>
            <th class="px-6 py-3">Monto Recuperado</th>
            <th class="px-6 py-3">Semanas</th>
            <th class="px-6 py-3">Finalización de crédito</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($liquidados as $prestamo)
        <tr onclick="window.location='{{ route('prestamos.show', $prestamo->id) }}'" 
            class="hover:bg-blue-50 cursor-pointer transition group">
            
            {{-- 1. Cliente --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $prestamo->cliente->nombre }}
            </td>

            {{-- 2. CORREGIDO: Agregamos la celda del Grupo que faltaba --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                {{ $prestamo->grupo->nombre ?? 'Sin asignar' }}
            </td>

            {{-- 3. Monto Total (Tachado como querías) --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-bold text-center line-through">
                ${{ number_format($prestamo->monto_total_pagar, 2) }}
            </td>

            {{-- 4. Monto Recuperado (CORREGIDO: Optimizado sin N+1) --}}
            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-bold text-center">
                ${{ number_format($prestamo->monto_recuperado ?? 0, 2) }}
            </td>

            {{-- 5. Semanas --}}
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-500 uppercase">
                {{ $prestamo->semanas }} / {{ $prestamo->semanas }} Pagadas
            </td>

            {{-- 6. Finalización --}}
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-gray-500 uppercase">
               FINALIZADO EL {{ $prestamo->updated_at->format('d/m/Y') }} →
            </td>
           
        </tr>
        @empty
        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No hay préstamos liquidados en el historial.</td></tr>
        @endforelse
    </tbody>
</table>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>