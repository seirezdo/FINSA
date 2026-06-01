<table class="min-w-full bg-white border border-gray-200">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuota Esperada</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
            @php 
                $prestamo = $cliente->prestamos->first(); 
                
                // 🔥 MODIFICACIÓN 1: Incluimos 'falla_penalizada' para que no desaparezca de la tabla
                $cuotaActiva = $prestamo->calendarioPagos->whereIn('estado', ['pendiente', 'falla', 'falla_penalizada'])->first();

                // Si hay cuota activa, calculamos cuánto dinero ha entregado en la semana
                if($cuotaActiva) {
                    $totalAbonado = $cuotaActiva->pagos->sum('monto_pagado');
                    $restante = $cuotaActiva->monto_esperado - $totalAbonado;
                }
            @endphp
                
            @if($cuotaActiva)
                {{-- 🔥 MODIFICACIÓN 2: Pintamos la fila de rojo si tiene CUALQUIERA de las dos fallas --}}
                <tr class="border-t {{ in_array($cuotaActiva->estado, ['falla', 'falla_penalizada']) ? 'bg-red-50' : '' }}">
                    
                    {{-- 🔥 MODIFICACIÓN 3: Agregamos las 2 columnas que le faltaban a tu código para que cuadre la tabla --}}
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $cliente->nombre ?? 'Cliente' }}
                    </td>
                    
                    <td class="px-6 py-4 text-blue-600 font-bold">
                        ${{ number_format($restante, 2) }}
                    </td>

                    {{-- COLUMNA DE ACCIONES --}}
                    <td class="px-6 py-4 flex items-center space-x-3">

                        {{-- BOTÓN A: AGREGAR ABONO --}}
                        <form action="{{ route('pagos.registrar') }}" method="POST" class="flex items-center space-x-2 m-0">
                            @csrf
                            <input type="hidden" name="calendario_pago_id" value="{{ $cuotaActiva->id }}">
                            <input type="number" name="monto_pagado" step="0.01" min="1" max="{{ $restante }}" value="{{ $restante > 0 ? $restante : '' }}" class="border border-gray-300 rounded px-2 py-1 w-24" required>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded transition">
                                Agregar Pago
                            </button>
                        </form>

                        {{-- BOTÓN B: COLOCAR FALLA (Solo si está 'pendiente') --}}
                        @if($cuotaActiva->estado === 'pendiente')
                            <form action="{{ route('pagos.update', $cuotaActiva->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="accion" value="falla">
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded transition" onclick="return confirm('¿Confirmar falla anticipada por los ${{ number_format($restante, 2) }} restantes?')">
                                    Falla
                                </button>
                            </form>
                        @endif

                        {{-- BOTÓN C: RECUPERAR --}}
                        {{-- 🔥 MODIFICACIÓN 4: El botón recuperar ahora aparece tanto en 'falla' como en 'falla_penalizada' --}}
                        @if(in_array($cuotaActiva->estado, ['falla', 'falla_penalizada']))
                            <form action="{{ route('pagos.update', $cuotaActiva->id) }}" method="POST" class="m-0">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="accion" value="recuperado">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded transition">
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