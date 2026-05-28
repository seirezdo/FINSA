<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cartera Vigente (Clientes al Corriente)') }}
        </h2>
    </x-slot>
  {{-- BOTÓN DE VOLVER: Como no hay menú de reportes, regresamos al inicio --}}
    <x-slot name="backUrl">
        {{ route('dashboard') }}
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500">
                <div class="p-6 bg-gray-50 border-b font-bold text-gray-700">
                    Listado de Clientes con Pagos Puntuales
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-center">
                        <thead class="bg-gray-100 text-xs font-medium text-gray-500 uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Cliente</th>
                                <th class="px-6 py-3">Grupo</th>
                                <th class="px-6 py-3">Progreso</th>
                                <th class="px-6 py-3">Monto Recuperado</th>
                                <th class="px-6 py-3">Próximo Pago</th>
                                <th class="px-6 py-3">Acciones</th>
                            </tr>
                        </thead>
                     <tbody class="bg-white divide-y divide-gray-200">
    @forelse($carteraVigente as $prestamo)
    <tr onclick="window.location='{{ route('prestamos.show', $prestamo->id) }}'" 
        class="hover:bg-green-50 cursor-pointer transition group">
        
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
            {{ $prestamo->cliente->nombre }}
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold text-center">
            ${{ number_format($prestamo->monto_total_pagar, 2) }}
        </td>

        {{-- Monto recuperado hasta hoy --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold text-center">
            ${{ number_format($prestamo->calendarioPagos->where('estado', 'pagado')->sum('monto_esperado'), 2) }}
        </td>

        {{-- Progreso Semanal --}}
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800">
                Semana {{ $prestamo->calendarioPagos->where('estado', 'pagado')->count() }} / {{ $prestamo->semanas }}
            </span>
        </td>

        <td class="px-6 py-4 text-right text-xs font-bold text-green-500 opacity-0 group-hover:opacity-100 transition-opacity uppercase">
            Ver Expediente →
        </td>
    </tr>
    @empty
    <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No hay clientes al corriente actualmente.</td></tr>
    @endforelse
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>