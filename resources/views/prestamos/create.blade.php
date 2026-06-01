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

                    <!-- NUEVO: FECHA DE ENTREGA (Para calcular el sábado anterior) -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Fecha de Entrega / Desembolso</label>
                        <input type="date" name="fecha_desembolso" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" required>
                        <p class="text-xs text-gray-500 mt-1">El sistema calculará automáticamente el <strong>sábado anterior</strong> a esta fecha como inicio de la semana 1.</p>
                    </div>

                    <!-- MONTO DEL PRÉSTAMO -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Monto del Préstamo ($)</label>
                        <input type="number" name="monto_prestado" step="100" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500" placeholder="Ej. 10000" required>
                    </div>
   <!-- 🔥 NUEVO: SELECCIÓN DE AVAL (RF-04) 🔥 -->
                    <div class="mb-6 bg-yellow-50 p-4 border border-yellow-200 rounded-md">
                        <label class="block text-gray-800 text-sm font-bold mb-2">Seleccionar Aval (Garantía Solidaria)</label>
                        <select name="aval_id" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500">
                            <option value="">-- Seleccionar Aval --</option>
                            @foreach($clientes as $aval)
                                {{-- Si estamos en renovación, ocultamos al mismo titular para que no se auto-seleccione --}}
                                @if(!$clienteSeleccionado || $clienteSeleccionado->id !== $aval->id)
                                    <option value="{{ $aval->id }}" {{ old('aval_id') == $aval->id ? 'selected' : '' }}>
                                        {{ $aval->nombre }} - CURP: {{ $aval->curp }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        @error('aval_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-yellow-700 mt-2">
                            * El sistema bloqueará la solicitud si el aval seleccionado ya tiene un crédito activo, o si ya es aval de otra persona.
                        </p>
                    </div>
                    <!-- AVISO DE CUOTAS -->
                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <p class="text-sm text-blue-800 italic">
                            * Al confirmar, se generarán automáticamente <strong>12 cuotas semanales</strong> del <strong>12.5%</strong> cada una.
                        </p>
                        {{-- Mandamos las semanas por detrás para que el controlador las reciba --}}
                        <input type="hidden" name="semanas" value="12">
                    </div>

                    <!-- BOTONES DE ACCIÓN -->
                    <div class="flex items-center justify-end">
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