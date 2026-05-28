<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 pb-0"> 
                
                {{-- Sección de Encabezado --}}
                <div class="p-6 bg-gray-50 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-700 uppercase">
                            Gestión de Plazas
                        </h2>
                        
                        {{-- Buscador AJAX --}}
                        <div class="relative w-1/3">
                            <input type="text" id="search_plazas" placeholder="Buscar por nombre o zona..." 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 text-sm">
                        </div>

                        <a href="{{ route('plazas.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-bold uppercase">
                            + Nueva Plaza
                        </a>
                    </div>
                </div>

                {{-- Contenedor de la Tabla --}}
                <div id="table-container-plazas" class="overflow-x-auto">
                    @include('plazas.partials.table')
                </div>
            </div>
        </div>
    </div>

    {{-- Script AJAX idéntico al de Clientes --}}
    <script>
        document.getElementById('search_plazas').addEventListener('input', function(e) {
            let query = e.target.value;
            fetch(`/plazas?search=${query}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('table-container-plazas').innerHTML = html;
            });
        });
    </script>
</x-app-layout>