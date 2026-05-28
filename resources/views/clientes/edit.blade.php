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
            <!-- Encabezado con navegación de regreso -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Editar Expediente: {{ $cliente->nombre }}</h2>
                <a href="{{ route('clientes.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                    &larr; Volver al listado
                </a>
            </div>

            <div class="bg-white p-8 shadow-xl sm:rounded-xl border border-gray-100">
                <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                    @csrf
                    @method('PUT') <!-- VITAL: Indica a Laravel que es una actualización [3] -->

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Datos de Identidad del Cliente -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nombre completo</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre) }}" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">N° Documento (CURP/INE)</label>
                            <input type="text" name="numero_documento" value="{{ old('numero_documento', $cliente->curp) }}" 
                                   class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Este dato es único por cliente para evitar fraudes [4].</p>
                        </div>

                        <!-- Datos Operativos (Tabla Clientes) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grupo Asignado</label>
                            <select name="grupo_id" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500" required>
                                @foreach($grupos as $grupo)
                                    <option value="{{ $grupo->id }}" {{ $cliente->grupo_id == $grupo->id ? 'selected' : '' }}>
                                        {{ $grupo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Perfil de Riesgo</label>
                            <select name="perfil_riesgo" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="bajo" {{ $cliente->perfil_riesgo == 'bajo' ? 'selected' : '' }}>Bajo</option>
                                <option value="medio" {{ $cliente->perfil_riesgo == 'medio' ? 'selected' : '' }}>Medio</option>
                                <option value="alto" {{ $cliente->perfil_riesgo == 'alto' ? 'selected' : '' }}>Alto</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado del Cliente</label>
                            <select name="estado" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">
                                <option value="activo" {{ $cliente->estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ $cliente->estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                <option value="bloqueado" {{ $cliente->estado == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                            </select>
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Dirección</label>
                            <textarea name="direccion" rows="2" class="mt-1 w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500">{{ old('direccion', $cliente->direccion) }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-4">
                        <a href="{{ route('clientes.index') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-all transform hover:scale-105">
                            Actualizar Expediente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>