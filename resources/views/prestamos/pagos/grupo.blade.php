<div class="overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Cliente</th>
                <th class="px-6 py-3">Cuota Semanal (12.5%)</th>
                <th class="px-6 py-3">Monto a Cobrar</th>
                <th class="px-6 py-3">Acción</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
                @php $cuotaActual = $cliente->prestamoActivo->calendarioPagos->where('estado', 'pendiente')->first(); @endphp
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $cliente->persona->nombre }}</td>
                    <td class="px-6 py-4 text-blue-600 font-bold">${{ number_format($cuotaActual->monto_esperado, 2) }}</td>
                    <td class="px-6 py-4">
                        <form id="pago-form-{{ $cliente->id }}" action="{{ route('pagos.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="calendario_pago_id" value="{{ $cuotaActual->id }}">
                            <input type="number" name="monto_pagado" value="{{ $cuotaActual->monto_esperado }}" 
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </td>
                    <td class="px-6 py-4">
                            <button type="submit" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                Registrar Pago
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>