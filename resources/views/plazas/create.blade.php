<x-app-layout>
@if(auth()->user()->role !== \App\Enums\UserRole::CLIENTE)
    <button class="bg-blue-600 text-white px-4 py-2 rounded">
        Gestión Administrativa
    </button>
@endif   
<x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva Plaza Operativa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <form action="{{ route('plazas.store') }}" method="POST" class="space-y-6">
                    @csrf {{-- Protección contra ataques CSRF obligatoria [3, 4] --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre de la Plaza -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre de la Plaza</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror">
                            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Zona -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Zona / Región</label>
                            <input type="text" name="zona" value="{{ old('zona') }}" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('zona') border-red-500 @enderror">
                            @error('zona') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Selector de Ejecutivo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asignar Ejecutivo</label>
                            <select name="ejecutivo_id" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Seleccionar Ejecutivo --</option>
                                @foreach($personas as $persona)
                                    <option value="{{ $persona->id }}" {{ old('ejecutivo_id') == $persona->id ? 'selected' : '' }}>
                                        {{ $persona->nombre }} {{ $persona->apellido_paterno }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ejecutivo_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado Inicial</label>
                            <select name="estado" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4 border-t pt-4">
                        <a href="{{ route('plazas.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow transition duration-150">
                            Guardar Plaza
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
