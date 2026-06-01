<div class="overflow-x-auto border rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 text-center">
        <thead class="bg-gray-100 text-xs font-medium text-gray-500 uppercase">
            <tr>
                <th class="px-6 py-3 text-left">Nombre Completo</th>
                <th class="px-6 py-3">Documento</th>
                <th class="px-6 py-3">Grupo</th>
                <th class="px-6 py-3">Estado</th>
                
                {{-- 1. RESTRICCIÓN DE ROL: Ocultamos cabecera de Acciones a Promotoras y Supervisoras --}}
                @if(in_array(auth()->user()->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::EJECUTIVO]))
                    <th class="px-6 py-3 text-right">Acciones</th>
                @endif
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($clientes as $cliente)
                <tr onclick="window.location='{{ route('clientes.show', $cliente->id) }}'" 
                        class="hover:bg-gray-50 cursor-pointer transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $cliente->nombre }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $cliente->curp }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $cliente->grupo->nombre ?? 'Sin asignar' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cliente->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($cliente->estado) }}
                        </span>
                    </td>
                    
                    {{-- 2. RESTRICCIÓN DE ROL: Ocultamos los botones de acción a Promotoras y Supervisoras --}}
                    @if(in_array(auth()->user()->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::EJECUTIVO]))
                        <td class="px-6 py-4 text-right text-sm font-medium" onclick="event.stopPropagation();">
                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 font-bold">Editar</a>
                            
                            {{-- Lógica de Seguridad: Solo el Admin ve el botón de eliminar (Corregido sin ->value) --}}
                            @if(auth()->user()->role === \App\Enums\UserRole::ADMIN)
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('¿Eliminar cliente?')">
                                        Eliminar
                                    </button>
                                </form>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No se encontraron clientes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
    @if ($clientes->hasPages())
        {{ $clientes->appends(request()->query())->links() }}
    @else
        <div class="text-xs text-gray-500 italic">
            Mostrando todos los registros ({{ $clientes->count() }})
        </div>
    @endif
</div>