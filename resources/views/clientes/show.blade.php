<!-- Dentro de tu tabla de semanas en show.blade.php -->
<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    @if($cuota->estado !== 'pagado')
        <form action="{{ route('pagos.store') }}" method="POST" class="flex items-center justify-end space-x-2">
            @csrf
            <input type="hidden" name="calendario_pago_id" value="{{ $cuota->id }}">
            
            <!-- Selector de Método -->
            <select name="metodo_pago" class="text-xs border-gray-300 rounded-md focus:ring-indigo-500">
                <option value="efectivo">Efe</option>
                <option value="transferencia">Tra</option>
            </select>

            <!-- Input de Monto -->
            <input type="number" name="monto_pagado" value="{{ $cuota->monto_esperado }}" 
                   class="w-20 text-xs border-gray-300 rounded-md focus:ring-indigo-500">

            <!-- Botón Cobrar -->
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 rounded-md text-xs font-bold uppercase transition">
                Cobrar
            </button>
        </form>
    @else
        <span class="text-green-600 font-bold flex items-center justify-end">
            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
            Pagado
        </span>
    @endif
</td>