<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Registrar Nuevo Grupo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white  overflow-hidden shadow-sm sm:rounded-lg p-8">
                
                <!-- Mostrar errores de validación si existen -->
                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                        <strong>¡Hay errores en el formulario!</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('grupos.store') }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre del Grupo -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nombre del Grupo *</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>

                        <!-- Día de Reunión -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Día de Reunión *</label>
                            <select name="dia_reunion" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Seleccione un día</option>
                                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                    <option value="{{ $dia }}" {{ old('dia_reunion') == $dia ? 'selected' : '' }}>{{ $dia }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Asignación de Plaza -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Plaza Operativa *</label>
                            <select name="plaza_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Seleccione una plaza</option>
                                @foreach($plazas as $plaza)
                                    <option value="{{ $plaza->id }}" {{ old('plaza_id') == $plaza->id ? 'selected' : '' }}>
                                        {{ $plaza->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Asignación de Promotora -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Promotora a Cargo *</label>
                            <select name="promotora_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">Seleccione a la promotora</option>
                                @foreach($promotoras as $promotora)
                                    <option value="{{ $promotora->id }}" {{ old('promotora_id') == $promotora->id ? 'selected' : '' }}>
                                        {{ $promotora->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-200">
                        <a href="{{ route('grupos.index') }}" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-6 rounded-lg shadow-sm transition">
                            Cancelar
                        </a>
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">
                            Guardar Grupo
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
