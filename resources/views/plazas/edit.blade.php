<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Plaza: {{ $plaza->nombre }}
        </h2>
    </x-slot>

    {{-- INYECTAS ESTO PARA VOLVER A LA TABLA --}}
    <x-slot name="backUrl">
        {{ route('plazas.index') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                {{-- OBLIGATORIO: Apuntar al método update y usar @method('PUT') --}}
                <form action="{{ route('plazas.update', $plaza->id) }}" method="POST" class="space-y-6">
                    @csrf 
                    @method('PUT') 

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre de la Plaza</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $plaza->nombre) }}" required
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Zona -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zona / Región</label>
                            <input type="text" name="zona" value="{{ old('zona', $plaza->zona) }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Ejecutivo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ejecutivo Asignado</label>
                            <select name="ejecutivo_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                @foreach($ejecutivos as $ejecutivo)
                                    <option value="{{ $ejecutivo->id }}" {{ (old('ejecutivo_id', $plaza->ejecutivo_id) == $ejecutivo->id) ? 'selected' : '' }}>
                                        {{ $ejecutivo->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Supervisora -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Supervisora Asignada</label>
                            <select name="supervisora_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                @foreach($supervisoras as $supervisora)
                                    <option value="{{ $supervisora->id }}" {{ (old('supervisora_id', $plaza->supervisora_id) == $supervisora->id) ? 'selected' : '' }}>
                                        {{ $supervisora->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="activo" {{ $plaza->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ $plaza->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 border-t pt-4">
                        <a href="{{ route('plazas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow">
                            Actualizar Plaza
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>