<table class="min-w-full bg-white border border-gray-200">
    <thead class="bg-gray-100">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuota Esperada</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clientes as $cliente)
            @php $prestamo = $cliente->prestamos->first(); @endphp
            <tr class="border-t">
                <td class="px-6 py-4">{{ $cliente->nombre }}</td>
                <td class="px-6 py-4 text-green-600 font-bold">
                    ${{ number_format($prestamo->calendarioPagos->where('estado', 'pendiente')->first()->monto_esperado, 2) }}
                </td>
                <td class="px-6 py-4">
                    <form action="{{ route('pagos.registrar') }}" method="POST">
                        @csrf
                        <input type="hidden" name="calendario_pago_id" value="{{ $prestamo->calendarioPagos->where('estado', 'pendiente')->first()->id }}">
                        <input type="number" name="monto_pagado" class="border rounded px-2 w-24" required>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">Cobrar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>