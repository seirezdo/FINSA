<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Auditoría Global de Pagos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-600">
                
                <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 uppercase tracking-wider">
                        <i class="bi bi-cash-stack"></i> Flujo de Efectivo Reciente
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Folio / Fecha</th>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3 text-left">Monto</th>
                                <th class="px-6 py-3 text-left">Método</th>
                                <th class="px-6 py-3 text-left bg-indigo-50 text-indigo-800 font-bold border-l border-indigo-100">
                                    Registrado Por (Cajero)
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @forelse($pagos as $pago)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">
                                        #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }} <br>
                                        <span class="text-xs text-gray-500 font-normal">
                                            {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y h:i A') }}
                                        </span>
                                    </td>
                                    
                                    {{-- Navegamos por las relaciones para llegar al nombre del cliente --}}
                                    <td class="px-6 py-4 text-gray-700">
                                        {{ $pago->cuota->prestamo->cliente->persona->nombre ?? 'N/A' }}
                                        <br>
                                        <span class="text-xs text-gray-400">Préstamo #{{ $pago->cuota->prestamo_id ?? '' }}</span>
                                    </td>
                                    
                                    <td class="px-6 py-4 font-black text-green-600">
                                        + ${{ number_format($pago->monto_pagado, 2) }}
                                    </td>
                                    
                                    <td class="px-6 py-4 uppercase text-xs text-gray-500 font-semibold">
                                        {{ $pago->metodo_pago }}
                                    </td>
                                    
                                    {{-- Trazabilidad Financiera: Quién recibió el dinero --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-800 font-medium bg-indigo-50/30 border-l border-indigo-50">
                                        {{ $pago->usuario->persona->nombre ?? 'Sistema Automático' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        No hay pagos registrados en el sistema todavía.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- Paginación de Laravel compatible con Tailwind --}}
                <div class="p-4 border-t bg-gray-50">
                    {{ $pagos->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>