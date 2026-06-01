<div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Cliente</th>
                <th class="px-6 py-3">Cuota Semanal</th>
                <th class="px-6 py-3">Acciones de Cobro</th> {{-- Fusionamos las dos columnas aquí --}}
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
                @php 
                    // 1. Buscamos la cuota que esté pendiente, en falla o penalizada
                    $cuotaActiva = $cliente->prestamoActivo->calendarioPagos->whereIn('estado', ['pendiente', 'falla', 'falla_penalizada'])->first(); 

                    // 2. Calculamos la deuda real
                    if($cuotaActiva) {
                        $totalAbonado = $cuotaActiva->pagos->sum('monto_pagado');
                        $restante = $cuotaActiva->monto_esperado - $totalAbonado;
                    }
                @endphp

                @if($cuotaActiva)
                    {{-- Pintamos la fila de rojo si tiene falla o falla_penalizada --}}
                    <tr class="border-b hover:bg-gray-50 {{ in_array($cuotaActiva->estado, ['falla', 'falla_penalizada']) ? 'bg-red-50' : 'bg-white' }}">
                        
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $cliente->nombre }}</td>
                        
                        {{-- Mostramos lo que realmente debe, no la cuota fija --}}
                        <td class="px-6 py-4 text-blue-600 font-bold">${{ number_format($restante, 2) }}</td>
                        
                        {{-- EN ESTE ÚNICO TD COLOCAMOS TODOS LOS BOTONES ALINEADOS --}}
                        <td class="px-6 py-4 flex items-center space-x-3">
                            
                            {{-- BOTÓN A: REGISTRAR PAGO --}}
                            <form action="{{ route('pagos.registrar') }}" method="POST" class="flex items-center space-x-2 m-0">
                                @csrf
                                <input type="hidden" name="calendario_pago_id" value="{{ $cuotaActiva->id }}">
                                <input type="number" name="monto_pagado" step="0.01" min="1" max="{{ $restante }}" value="{{ $restante > 0 ? $restante : '' }}" 
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-24 p-2.5" required>
                                
                                <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 text-center transition">
                                    Registrar Pago
                                </button>
                            </form>

                            {{-- BOTÓN B: COLOCAR FALLA (Solo si está pendiente) --}}
                            @if($cuotaActiva->estado === 'pendiente')
                                <form action="{{ route('pagos.update', $cuotaActiva->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="accion" value="falla">
                                    <button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-4 py-2 transition" onclick="return confirm('¿Confirmar falla anticipada por los ${{ number_format($restante, 2) }} restantes?')">
                                        Falla
                                    </button>
                                </form>
                            @endif

                            {{-- BOTÓN C: RECUPERAR (Si está multada o en falla) --}}
                            @if(in_array($cuotaActiva->estado, ['falla', 'falla_penalizada']))
                                <form action="{{ route('pagos.update', $cuotaActiva->id) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="accion" value="recuperado">
                                    <button type="submit" class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-4 py-2 transition">
                                        Recuperar
                                    </button>
                                </form>
                            @endif

                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>