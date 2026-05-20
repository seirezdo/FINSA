<x-app-layout>
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
                        <!-- Datos Personales -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido Paterno</label>
                            <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Apellido Materno</label>
                            <input type="text" name="apellido_materno" value="{{ old('apellido_materno') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                        </div>

                        <!-- Identificación y Ubicación Operativa -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">N° Documento (CURP/INE)</label>
                            <input type="text" name="numero_documento" value="{{ old('numero_documento') }}" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                            @error('numero_documento') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Asignar a Grupo</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                <option value="">Seleccione un grupo...</option>
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo">Bajo</option>
                                <option value="medio">Medio</option>
                                <option value="alto">Alto</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección Completa</label>
                            <textarea name="direccion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">{{ old('direccion') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
                            Guardar Expediente de Cliente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>