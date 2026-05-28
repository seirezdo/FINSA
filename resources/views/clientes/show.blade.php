<x-app-layout>
    {{-- 1. Aquí defines el título --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente del Cliente: {{ $cliente->nombre }}
        </h2>
    </x-slot>

    {{-- 2. AQUÍ ENCIENDES EL BOTÓN DE VOLVER Y LE DICES A DÓNDE IR --}}
    <x-slot name="backUrl">
        {{ route('clientes.index') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
         <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Información Personal</h3>
                
                {{-- Primera Fila: Lo que solicitaste --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                    <div>
                        <span class="font-bold text-gray-500">Nombre del Cliente:</span> <br> 
                        <span class="text-gray-900 font-semibold">{{ $cliente->nombre }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Grupo:</span> <br> 
                        <span class="text-gray-900">{{ $cliente->grupo->nombre ?? 'Sin grupo' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Promotora:</span> <br> 
                        {{-- Accedemos a la promotora a través del grupo --}}
                        <span class="text-gray-900">{{ $cliente->grupo->promotora->name ?? 'Sin asignar' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Fecha de Registro/Crédito:</span> <br> 
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}</span>
                    </div>
                </div>

                {{-- Segunda Fila: Datos de contacto secundarios --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm pt-4 border-t border-gray-100">
                    <div>
                        <span class="font-bold text-gray-500">Documento (CURP):</span> <br> 
                        <span class="text-gray-900">{{ $cliente->curp }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Teléfono:</span> <br> 
                        <span class="text-gray-900">{{ $cliente->telefono ?? 'No registrado' }}</span>
                    </div>
                </div>
            </div>
<div class="flex justify-between items-center border-b pb-2 mb-4">
    <h3 class="text-lg font-bold text-gray-700">Historial de Créditos</h3>
    
    {{-- Botón para renovar o dar un nuevo crédito a este cliente específico --}}
    <a href="{{ route('prestamos.create', ['cliente_id' => $cliente->id]) }}" 
       class="px-4 py-2 bg-green-600 text-white rounded-md text-xs font-bold uppercase hover:bg-green-700 transition">
        + Nuevo Préstamo
    </a>
</div>
            {{-- HISTORIAL DE CRÉDITOS --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Historial de Créditos</h3>
                
                @forelse($prestamos as $prestamo)
                    <div class="border rounded-lg p-4 mb-4 flex justify-between items-center {{ $prestamo->estado == 'liquidado' ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                        
                        {{-- Detalles del Monto --}}
                        <div>
                            <p class="font-bold text-gray-800 text-lg">Préstamo #${{ number_format($prestamo->monto_total_pagar, 2) }}</p>
                            <p class="text-sm text-gray-500">Recuperado: <span class="text-green-600 font-bold">${{ number_format($prestamo->monto_recuperado ?? 0, 2) }}</span></p>
                        </div>

                        {{-- Progreso de Semanas --}}
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase font-bold">Progreso</p>
                            <span class="px-3 py-1 text-sm font-bold rounded bg-indigo-100 text-indigo-800">
                                {{ $prestamo->semanas_pagadas }} / {{ $prestamo->semanas }} Semanas
                            </span>
                            @if($prestamo->en_prorroga && $prestamo->estado != 'liquidado')
                                <span class="block text-[10px] text-red-600 font-black uppercase mt-1">En Prórroga / Atraso</span>
                            @endif
                        </div>

                        {{-- Estatus Final --}}
                        <div class="text-right">
                            <p class="text-xs text-gray-500 uppercase font-bold">Estatus</p>
                            @if($prestamo->estado === 'liquidado')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-blue-100 text-blue-800">
                                    LIQUIDADO
                                </span>
                            @elseif($prestamo->estado === 'activo')
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-green-100 text-green-800">
                                    ACTIVO
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full bg-gray-100 text-gray-800">
                                    {{ strtoupper($prestamo->estado) }}
                                </span>
                            @endif
                            
                            {{-- Botón opcional para ir al detalle completo del préstamo --}}
                            <div class="mt-2">
                                <a href="{{ route('prestamos.show', $prestamo->id) }}" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold">Ver pagos &rarr;</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 italic text-center py-4">Este cliente aún no tiene historial de créditos.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>