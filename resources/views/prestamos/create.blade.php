<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Nuevo Préstamo Grupal</h2>
                <p class="mb-6 text-gray-600">Cliente: <strong>{{ $cliente->persona->nombre }}</strong></p>

                <form action="{{ route('prestamos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Monto del Préstamo ($)</label>
                        <input type="number" name="monto_prestado" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" placeholder="Ej. 10000" required>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <p class="text-sm text-blue-800 italic">
                            * Al confirmar, se generarán automáticamente **12 cuotas semanales** del **12.5%** cada una.
                        </p>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('clientes.show', $cliente->id) }}" class="mr-4 text-gray-600 hover:underline">Cancelar</a>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Autorizar Préstamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>