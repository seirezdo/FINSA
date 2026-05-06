<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre Completo</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($clientes as $cliente)
            <tr>
                <td class="px-6 py-4">{{ $cliente->persona->nombre }} {{ $cliente->persona->apellido_paterno }}</td>
                <td class="px-6 py-4">{{ $cliente->persona->numero_documento }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                        {{ $cliente->estado }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No se encontraron clientes.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="mt-4">
    {{ $clientes->links() }}
</div>