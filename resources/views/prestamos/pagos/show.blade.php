<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- SECCIÓN 1: RESUMEN DEL PRÉSTAMO --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-600 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Préstamo #{{ str_pad($prestamo->id, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Cliente: <span class="font-semibold">{{ $prestamo->cliente->persona->nombre ?? 'N/A' }}</span>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 uppercase tracking-wider">Total a Pagar</p>
                    <p class="text-2xl font-black text-indigo-600">${{ number_format($prestamo->monto_total_pagar, 2) }}</p>
                    
                    {{-- Badge de Estado Dinámico --}}
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase mt-2 inline-block 
                        {{ $prestamo->estado === 'liquidado' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $prestamo->estado }}
                    </span>
                </div>
            </div>

            {{-- SECCIÓN 2: HISTORIAL DE PAGOS EN TAILWIND --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 uppercase tracking-wider">
                        <i class="bi bi-clock-history"></i> Historial de Pagos Realizados
                    </h3>
                    
                    <a href="{{ route('prestamos.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-medium">
                        &larr; Volver a Préstamos
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Folio / Fecha</th>
                                <th class="px-6 py-3 text-left">Referencia (Semana)</th>
                                <th class="px-6 py-3 text-left">Monto Abonado</th>
                                <th class="px-6 py-3 text-left">Método</th>
                                <th class="px-6 py-3 text-left bg-indigo-50 text-indigo-800">Registrado Por</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            
                            {{-- Iteramos sobre la nueva relación 'pagos' que agregamos al modelo --}}
                            @forelse($prestamo->pagos as $pago)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">
                                        #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }} <br>
                                        <span class="text-xs text-gray-500 font-normal">
                                            {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y h:i A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 font-medium">
                                        Semana {{ $pago->cuota->numero_semana ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 font-bold text-green-600">
                                        + ${{ number_format($pago->monto_pagado, 2) }}
                                    </td>
                                    <td class="px-6 py-4 uppercase text-xs text-gray-500 font-semibold">
                                        {{ $pago->metodo_pago }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-700 font-medium bg-indigo-50/30">
                                        {{ $pago->usuario->persona->nombre ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @empty
                                {{-- Estado vacío si no hay abonos registrados --}}
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <p>Aún no hay pagos registrados para este préstamo.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- SECCIÓN 3: TOTAL RECUPERADO --}}
                @if($prestamo->pagos->isNotEmpty())
                    <div class="p-4 border-t bg-green-50 text-right">
                        <span class="text-sm text-gray-600 font-medium mr-4">Total Recuperado:</span>
                        <span class="text-xl font-black text-green-700">
                            {{-- Laravel permite sumar propiedades al vuelo desde la colección --}}
                            ${{ number_format($prestamo->pagos->sum('monto_pagado'), 2) }}
                        </span>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>