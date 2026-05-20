<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre Completo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($clientes as $cliente)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $cliente->persona->nombre }} {{ $cliente->persona->apellido_paterno }} {{ $cliente->persona->apellido_materno }}
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $cliente->persona->numero_documento }}
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    {{ $cliente->grupo->nombre ?? 'Sin asignar' }}
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cliente->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($cliente->estado) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium">
                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Editar</a>
                    <!-- Agrega aquí el botón de eliminar si el rol es Admin -->
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No se encontraron clientes registrados.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="mt-4">
    {{ $clientes->links() }}
</div>