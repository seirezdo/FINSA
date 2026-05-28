<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($clienteSeleccionado)
                {{ __('Nuevo Préstamo para: ') }} {{ $clienteSeleccionado->nombre }}
            @else
                {{ __('Crear Nuevo Préstamo') }}
            @endif
        </h2>
    </x-slot>

    {{-- BOTÓN DINÁMICO: Regresa al expediente si hay cliente, o al listado si no lo hay --}}
    <x-slot name="backUrl">
        {{ $clienteSeleccionado ? route('clientes.show', $clienteSeleccionado->id) : route('clientes.index') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-800">Nuevo Préstamo Grupal</h2>

                <form action="{{ route('prestamos.store') }}" method="POST">
                    @csrf

                    <!-- GESTIÓN DINÁMICA DEL CLIENTE -->
                    <div class="mb-4">
                        @if($clienteSeleccionado)
                            {{-- Modo Renovación (Desde el Expediente) --}}
                            <p class="mb-2 text-gray-600">Cliente: <strong class="text-gray-900">{{ $clienteSeleccionado->nombre }}</strong></p>
                            <input type="hidden" name="cliente_id" value="{{ $clienteSeleccionado->id }}">
                        @else
                            {{-- Modo General (Desde el Menú Principal) --}}
                            <label class="block text-gray-700 text-sm font-bold mb-2">Seleccionar Cliente</label>
                            <select name="cliente_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                                <option value="">-- Seleccionar Cliente --</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->nombre }} ({{ $c->curp }})
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <!-- MONTO DEL PRÉSTAMO -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Monto del Préstamo ($)</label>
                        <input type="number" name="monto_prestado" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" placeholder="Ej. 10000" required>
                    </div>

                    <!-- AVISO DE CUOTAS -->
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <p class="text-sm text-blue-800 italic">
                            * Al confirmar, se generarán automáticamente <strong>12 cuotas semanales</strong> del <strong>12.5%</strong> cada una.
                        </p>
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex items-center justify-end">
                        
                        {{-- Botón de cancelar dinámico --}}
                        @if($clienteSeleccionado)
                            <a href="{{ route('clientes.show', $clienteSeleccionado->id) }}" class="mr-4 text-gray-600 hover:underline">Cancelar</a>
                        @else
                            <a href="{{ route('clientes.index') }}" class="mr-4 text-gray-600 hover:underline">Cancelar</a>
                        @endif
                        
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                            Autorizar Préstamo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>