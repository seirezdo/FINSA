<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expediente Detallado: ') }} {{ $prestamo->cliente->persona->nombre }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-6">
                
                <!-- Panel de Resumen del Crédito -->
                <div class="w-full md:w-1/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 p-6">
                        <h3 class="text-lg font-bold mb-4 border-b pb-2 uppercase text-gray-600">Estado de Cuenta</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Total a Recuperar</span>
                                <span class="block text-2xl font-bold text-indigo-700">${{ number_format($prestamo->monto_total_pagar, 2) }}</span>
                            </div>
                            
                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Monto Recuperado</span>
                                <span class="block text-xl font-bold text-green-600">
                                    ${{ number_format($prestamo->calendarioPagos->flatMap->pagos->sum('monto_pagado'), 2) }}
                                </span>
                            </div>

                            <div>
                                <span class="text-sm font-semibold text-gray-500 uppercase">Estatus Global</span>
                                <span class="mt-1 px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                    {{ $prestamo->estado === 'liquidado' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ strtoupper($prestamo->estado) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Comportamiento Semanal -->
                <div class="w-full md:w-2/3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg shadow-lg">
                        <div class="p-4 bg-gray-800 text-white flex justify-between items-center">
                            <span class="font-bold">Historial de Cuotas</span>
                            <span class="text-xs uppercase text-gray-400">Auditoría de Pagos Reales</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-center">
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Semana</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Esperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider text-green-700">Recuperado</th>
                                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200 text-center">
                                    @foreach($prestamo->calendarioPagos as $cuota)
                                    <tr class="{{ $cuota->numero_semana > 12 ? 'bg-red-50' : 'hover:bg-gray-50' }} transition">
                                        
                                        <!-- Identificador de Semana -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">Semana {{ $cuota->numero_semana }}</div>
                                            @if($cuota->numero_semana > 12)
                                                <span class="text-[10px] font-black text-red-600 uppercase animate-pulse">Extension por Mora</span>
                                            @endif
                                        </td>

                                        <!-- Monto Esperado -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            ${{ number_format($cuota->monto_esperado, 2) }}
                                        </td>

                                        <!-- Comportamiento de Pago (Suma de abonos) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                            ${{ number_format($cuota->pagos->sum('monto_pagado'), 2) }}
                                            @if($cuota->pagos->count() > 1)
                                                <span class="block text-[9px] text-gray-400 italic">({{ $cuota->pagos->count() }} abonos)</span>
                                            @endif
                                        </td>

                                        <!-- Estatus con Badges dinámicos -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full shadow-sm
                                                {{ $cuota->estado === 'pagado' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                                   ($cuota->estado === 'parcial' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                                   'bg-gray-100 text-gray-400') }}">
                                                {{ strtoupper($cuota->estado) }}
                                            </span>
                                        </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>