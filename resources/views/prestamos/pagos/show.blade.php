<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- ========================================================== --}}
            {{-- SECCIÓN 1: RESUMEN DEL PRÉSTAMO                            --}}
            {{-- ========================================================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-indigo-600 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">
                        Préstamo #{{ str_pad($prestamo->id, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Cliente: <span class="font-semibold">{{ $prestamo->cliente->nombre ?? 'N/A' }}</span>
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

            {{-- ========================================================== --}}
            {{-- SECCIÓN 2: HISTORIAL DE CUOTAS (CALENDARIO DE PAGOS)       --}}
            {{-- ========================================================== --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-500 mb-6">
                <div class="p-6 bg-gray-50 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 uppercase tracking-wider">
                        <i class="bi bi-calendar3"></i> Historial de Cuotas
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 text-left">Fecha / Semana</th>
                                <th class="px-6 py-3 text-left">Esperado</th>
                                <th class="px-6 py-3 text-left">Recuperado</th>
                                <th class="px-6 py-3 text-left">Estado</th>
                                <th class="px-6 py-3 text-left">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            
                            @foreach($prestamo->calendarioPagos as $cuota)
                                @php
                                    $totalAbonado = $cuota->pagos->sum('monto_pagado');
                                    $restante = $cuota->monto_esperado - $totalAbonado;
                                @endphp
                                
                                {{-- Pintamos de rojo si la cuota está en falla o multada --}}
                                <tr class="hover:bg-gray-50 transition duration-150 {{ in_array($cuota->estado, ['falla', 'falla_penalizada']) ? 'bg-red-50' : '' }}">
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-bold">
                                        {{ \Carbon\Carbon::parse($cuota->fecha_vencimiento)->format('d/m/Y') }} <br>
                                        <span class="text-xs text-gray-500 font-normal">
                                            Semana {{ $cuota->numero_semana }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-gray-600 font-medium">
                                        ${{ number_format($cuota->monto_esperado, 2) }}
                                    </td>
                                    
                                    <td class="px-6 py-4 font-bold text-green-600">
                                        ${{ number_format($totalAbonado, 2) }}
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        {{-- Badge dinámico para el estado --}}
                                        <span class="px-2 py-1 rounded-full text-xs font-bold uppercase
                                            {{ in_array($cuota->estado, ['pagado', 'recuperado']) ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $cuota->estado === 'pendiente' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ in_array($cuota->estado, ['falla', 'falla_penalizada']) ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ str_replace('_', ' ', $cuota->estado) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        {{-- LA CLAVE: Mostramos la cajita SIEMPRE que haya deuda activa --}}
                                        @if(in_array($cuota->estado, ['pendiente', 'falla', 'falla_penalizada']) && $restante > 0)
                                            
                                            <form action="{{ route('pagos.registrar') }}" method="POST" class="flex flex-col space-y-2 m-0">
                                                @csrf
                                                <input type="hidden" name="calendario_pago_id" value="{{ $cuota->id }}">
                                                
                                                <div class="flex items-center space-x-2">
                                                    <input type="number" name="monto_pagado" step="0.01" min="1" max="{{ $restante }}" value="{{ $restante }}" 
                                                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-1.5 w-24" required>
                                                    
                                                    <button type="submit" class="text-white bg-green-700 hover:bg-green-800 font-medium rounded-lg text-sm px-3 py-1.5 transition">
                                                        Abonar
                                                    </button>
                                                </div>
                                            </form>

                                            {{-- BOTÓN ROJO DE FALLA: Solo si está 'pendiente' --}}
                                            @if($cuota->estado === 'pendiente')
                                                <form action="{{ route('pagos.update', $cuota->id) }}" method="POST" class="mt-2 m-0">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="accion" value="falla">
                                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 font-medium rounded-lg text-sm px-3 py-1 w-full text-center transition" onclick="return confirm('¿Marcar anticipadamente como falla?')">
                                                        Marcar Falla
                                                    </button>
                                                </form>
                                            @endif

                                        @else
                                            <span class="text-gray-500 font-bold uppercase text-xs">Cerrada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- SECCIÓN 3: HISTORIAL DE PAGOS EN TAILWIND (AUDITORÍA)      --}}
            {{-- ========================================================== --}}
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
                            
                            {{-- Iteramos sobre la nueva relación 'pagos' --}}
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
                                        {{ $pago->metodo_pago ?? 'Efectivo' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-indigo-700 font-medium bg-indigo-50/30">
                                        {{ $pago->usuario->name ?? 'Sistema' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <p>Aún no hay pagos registrados para este préstamo.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{-- ========================================================== --}}
                {{-- SECCIÓN 4: TOTAL RECUPERADO AL FINAL DE LA AUDITORÍA       --}}
                {{-- ========================================================== --}}
                @if($prestamo->pagos->isNotEmpty())
                    <div class="p-4 border-t bg-green-50 text-right">
                        <span class="text-sm text-gray-600 font-medium mr-4">Total Recuperado:</span>
                        <span class="text-xl font-black text-green-700">
                            ${{ number_format($prestamo->pagos->sum('monto_pagado'), 2) }}
                        </span>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>