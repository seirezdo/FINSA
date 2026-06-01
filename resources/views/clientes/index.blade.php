<x-app-layout>
    <div class="py-12">
        {{-- Si quieres que sea TOTALMENTE ancha como tus otros reportes, cambia 'max-w-7xl' por 'max-w-full' --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500 pb-0"> 
                
                {{-- Sección de Encabezado con Padding (Igual que Liquidados) --}}
                <div class="p-6 bg-gray-50 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="font-bold text-gray-700 uppercase">
                            Gestión de Clientes
                        </h2>
                        
                        {{-- Buscador --}}
                        <div class="relative w-1/3">
                            <input type="text" id="search" placeholder="Buscar por nombre o documento..." 
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 pl-10 text-sm">
                        </div>

                        {{-- Solo Admin y Ejecutivo pueden registrar nuevos clientes [4, 5] --}}
    @if(in_array(auth()->user()->role, [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::EJECUTIVO]))
        <a href="{{ route('clientes.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-xs font-bold uppercase">
            + Nuevo Cliente
        </a>
    @endif
                    </div>
                </div>

                {{-- Contenedor de la Tabla (SIN P-6 para que toque los bordes) --}}
                <div id="table-container" class="overflow-x-auto">
                    @include('clientes.partials.table')
                </div>
            </div>
        </div>
    </div>

    {{-- 🔥 AQUÍ ESTÁ EL SCRIPT PROTEGIDO CONTRA SATURACIÓN 🔥 --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const tableContainer = document.getElementById('table-container');
            let timeout = null;

            searchInput.addEventListener('input', function (e) {
                clearTimeout(timeout);
                
                // Esperamos 300ms después de que deje de escribir para consultar la BD
                timeout = setTimeout(() => {
                    let query = e.target.value;
                    
                    // Usamos la ruta dinámica y codificamos el texto por si escriben símbolos
                    fetch(`{{ route('clientes.index') }}?search=${encodeURIComponent(query)}`, {
                        headers: { "X-Requested-With": "XMLHttpRequest" }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                    })
                    .catch(error => console.error('Error en la búsqueda:', error));
                }, 300); 
            });
        });
    </script>
</x-app-layout>