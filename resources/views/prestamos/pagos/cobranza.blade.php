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
                
                // Buscamos la cuota que está corriendo (pendiente o en semana de gracia/falla)
                $cuotaActiva = $prestamo->calendarioPagos->whereIn('estado', ['pendiente', 'falla'])->first();

                // Si hay cuota activa, calculamos cuánto dinero ha entregado en la semana
                if($cuotaActiva) {
                    $totalAbonado = $cuotaActiva->pagos->sum('monto_pagado');
                    $restante = $cuotaActiva->monto_esperado - $totalAbonado;
                }
            @endphp
            
            @if($cuotaActiva)
                <tr class="border-t {{ $cuotaActiva->estado === 'falla' ? 'bg-red-50' : '' }}">
                    
                    {{-- 1. CLIENTE --}}
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

                        {{-- BOTÓN B: COLOCAR FALLA (Ahora siempre disponible si está pendiente) --}}
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

                        {{-- BOTÓN C: RECUPERAR (Solo si está en falla) --}}
                        @if($cuotaActiva->estado === 'falla')
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