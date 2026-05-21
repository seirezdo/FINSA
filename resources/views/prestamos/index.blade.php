<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex justify-between mb-4">
                    <h2 class="text-2xl font-bold">Listado de Préstamos</h2>
                    <!-- El botón para crear ya lo tienes en el flujo de clientes -->
                </div>

                <table class="min-w-full table-auto border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border p-2">Cliente</th>
                            <th class="border p-2">Monto Prestado</th>
                            <th class="border p-2">Estado</th>
                            <th class="border p-2">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestamos as $prestamo)
                        <tr>
                            <td class="border p-2">{{ $prestamo->cliente->persona->nombre }}</td>
                            <td class="border p-2">${{ number_format($prestamo->monto_prestado, 2) }}</td>
                            <td class="border p-2">{{ ucfirst($prestamo->estado) }}</td>
                            <td class="border p-2">
                                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="text-blue-600 underline">Ver Calendario</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $prestamos->links() }} <!-- Paginación automática [6] -->
                </div>
            </div>
        </div>
    </div>
</x-app-layout>