<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ejecutivo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisora</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200">
        @forelse($plazas as $plaza)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-6 py-4">{{ $plaza->nombre }}</td>
            <td class="px-6 py-4">{{ $plaza->zona ?? 'N/A' }}</td>
            
            {{-- Mostrando los responsables desde la relación a la tabla users unificada --}}
            <td class="px-6 py-4 text-sm">{{ $plaza->ejecutivo->name ?? 'Sin asignar' }}</td>
            <td class="px-6 py-4 text-sm">{{ $plaza->supervisora->name ?? 'Sin asignar' }}</td>

            <td class="px-6 py-4">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $plaza->estado == 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($plaza->estado) }}
                </span>
            </td>
            <td class="px-6 py-4 text-right">
                <a href="{{ route('plazas.edit', $plaza) }}" class="text-indigo-600 hover:text-indigo-900 font-bold mr-3">Editar</a>
                
                <form action="{{ route('plazas.destroy', $plaza) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('¿Seguro que deseas eliminar esta plaza?')">
                        Eliminar
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">No se encontraron plazas.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Paginación AJAX (Opcional, pero recomendada) --}}
<div class="mt-4 px-4 pb-4">
    {{ $plazas->appends(request()->query())->links() }}
</div>
