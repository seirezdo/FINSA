<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Cliente') }}
        </h2>
    </x-slot>

    {{-- INYECTAS ESTO PARA VOLVER AL LISTADO --}}
    <x-slot name="backUrl">
        {{ route('clientes.index') }}
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Botón de Gestión Administrativa -->
            @if(auth()->user()->role !== \App\Enums\UserRole::CLIENTE)
                <div class="mb-6">
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition">
                        Gestión Administrativa
                    </button>
                </div>
            @endif

            <div class="bg-white p-8 shadow-xl sm:rounded-xl border border-gray-100">
                <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Registrar Nuevo Cliente</h2>
                
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre Completo (Unificado) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" placeholder="Ej. Sergio García" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- CURP (Antes numero_documento) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CURP</label>
                            <input type="text" name="curp" value="{{ old('curp') }}" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 uppercase" required>
                            @error('curp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            @error('telefono') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Asignar a Grupo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asignar a Grupo</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                <option value="">Seleccione un grupo...</option>
                                @foreach($grupos as $grupo)
                                    {{-- Agregamos la lógica old() por si falla la validación, mantenga el grupo seleccionado --}}
                                    <option value="{{ $grupo->id }}" {{ old('grupo_id') == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grupo_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <!-- Dirección (Opcional, si la requieres en la creación) -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}" 
                                class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            @error('direccion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                   

                       <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo" {{ old('perfil_riesgo') == 'bajo' ? 'selected' : '' }}>Bajo</option>
                                <option value="medio" {{ old('perfil_riesgo') == 'medio' ? 'selected' : '' }}>Medio</option>
                                <option value="alto" {{ old('perfil_riesgo') == 'alto' ? 'selected' : '' }}>Alto</option>
                            </select>
                            @error('perfil_riesgo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div> <!-- Cierre del grid -->

                    <!-- Botones de Acción -->
                    <div class="flex items-center justify-end mt-6 border-t pt-4">
                        <a href="{{ route('clientes.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 font-medium transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow-md transition duration-150">
                            Guardar Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>