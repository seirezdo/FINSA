<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Gestión de Clientes</h2>
                    <input type="text" id="search" placeholder="Buscar por nombre o documento..." 
                           class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 w-1/3">
           <a href="{{ route('clientes.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
    Nuevo Cliente
</a>

                        </div>

                <div id="table-container">
                    @include('clientes.partials.table')
                </div>
            </div>
        </div>
    </div>

    <script>
        // Lógica de búsqueda en tiempo real
        document.getElementById('search').addEventListener('input', function(e) {
            let query = e.target.value;
            fetch(`/clientes?search=${query}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('table-container').innerHTML = html;
            });
        });
    </script>
</x-app-layout>