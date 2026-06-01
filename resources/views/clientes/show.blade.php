<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Expediente del Cliente: {{ $cliente->nombre }}
        </h2>
    </x-slot>

    <x-slot name="backUrl">
        {{ route('clientes.index') }}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. INFORMACIÓN PERSONAL --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-bold text-gray-700 border-b pb-2 mb-4">Información Personal</h3>
                
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
                        <span class="text-gray-900">{{ $cliente->grupo->promotora->name ?? 'Sin asignar' }}</span>
                    </div>
                    <div>
                        <span class="font-bold text-gray-500">Fecha de Registro:</span> <br>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($cliente->fecha_registro)->format('d/m/Y') }}</span>
                    </div>
                </div>

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

                @php 
                    $prestamoActivo = $cliente->prestamos->where('estado', 'activo')->first(); 
                @endphp
                
                @if($prestamoActivo && $prestamoActivo->aval)
                    <div class="mt-6 pt-4 border-t border-gray-200 bg-gray-50 p-4 rounded-lg shadow-inner">
                        <h4 class="text-xs font-extrabold text-indigo-600 uppercase tracking-wider mb-3">
                            <i class="bi bi-shield-lock-fill mr-1"></i> Respaldo del Crédito Activo (Aval)
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-bold text-gray-500">Nombre del Aval:</span> <br>
                                <span class="text-gray-900 font-bold">{{ $prestamoActivo->aval->nombre }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-gray-500">Documento (CURP):</span> <br>
                                <span class="text-gray-900">{{ $prestamoActivo->aval->curp ?? 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-gray-500">Teléfono:</span> <br>
                                <span class="text-gray-900">{{ $prestamoActivo->aval->telefono ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                @elseif($prestamoActivo && !$prestamoActivo->aval)
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <span class="text-xs font-semibold text-red-500 uppercase">
                            <i class="bi bi-exclamation-triangle"></i> El crédito activo no tiene un aval registrado.
                        </span>
                    </div>
                @endif
            </div>

            {{-- 2. ENCABEZADO HISTORIAL DE CRÉDITOS --}}
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-bold text-gray-700">Historial de Créditos</h3>
                <a href="{{ route('prestamos.create', ['cliente_id' => $cliente->id]) }}" class="px-4 py-2 bg-green-600 text-white rounded-md text-xs font-bold uppercase hover:bg-green-700 transition">
                    + Nuevo Préstamo
                </a>
            </div>

            {{-- 3. TARJETAS DE CRÉDITOS COMO TITULAR --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                @forelse($prestamos as $prestamo)
                    @php
                        $cuotas = $prestamo->calendarioPagos ?? collect();
                        $semanasPagadasCalculadas = $cuotas->where('estado', 'pagado')->count();
                        $recuperadoCalculado = $cuotas->sum(function($cuota) {
                            return $cuota->pagos->sum('monto_pagado');
                        });
                    @endphp

                    <div class="border rounded-lg p-4 mb-4 flex justify-between items-center {{ $prestamo->estado == 'liquidado' ? 'bg-blue-50 border-blue-200' : 'bg-gray-50' }}">
                        
                        <div>
                            <p class="font-bold text-gray-800 text-lg">Préstamo #${{ number_format($prestamo->monto_total_pagar, 2) }}</p>
                            <p class="text-sm text-gray-500">Recuperado: 
                                <span class="text-green-600 font-bold">${{ number_format($recuperadoCalculado, 2) }}</span>
                            </p>
                        </div>
                        
                        <div class="text-center">
                            <p class="text-xs text-gray-500 uppercase font-bold">Progreso</p>
                            <span class="px-3 py-1 text-sm font-bold rounded bg-indigo-100 text-indigo-800">
                                {{ $semanasPagadasCalculadas }} / {{ $prestamo->semanas }} Semanas
                            </span>
                            @if($prestamo->en_prorroga && $prestamo->estado != 'liquidado')
                                <span class="block text-[10px] text-red-600 font-black uppercase mt-1">En Prórroga / Atraso</span>
                            @endif
                        </div>
                        
                        <div class="text-right">
                            <a href="{{ route('prestamos.show', $prestamo->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-bold flex items-center">
                                Ver pagos &rarr;
                            </a>
                        </div>
                        
                    </div>
                @empty
                    <p class="text-gray-500 italic">Este cliente aún no tiene historial de créditos como titular.</p>
                @endforelse
            </div>

            {{-- 4. CRÉDITOS RESPALDADOS COMO AVAL (SOLUCIÓN AL ERROR 4) --}}
            <div class="bg-white shadow-sm sm:rounded-lg border-l-4 border-yellow-500 p-6">
                <h3 class="text-lg font-bold text-gray-700 uppercase mb-4">
                    Créditos Respaldados como AVAL
                </h3>
                
                @if(isset($prestamosComoAval) && $prestamosComoAval->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($prestamosComoAval as $aval)
                            <div class="border rounded-lg p-4 bg-yellow-50 shadow-sm">
                                <p class="font-bold text-gray-800">Préstamo #${{ number_format($aval->monto_total_pagar, 2) }}</p>
                                <p class="text-sm text-gray-600 mt-2">
                                    <span class="font-semibold">Titular del Crédito:</span> {{ $aval->cliente->nombre ?? 'N/A' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <span class="font-semibold">Semanas:</span> {{ $aval->semanas }} semanas
                                </p>
                                <div class="mt-3">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $aval->estado === 'activo' ? 'bg-blue-100 text-blue-800' : ($aval->estado === 'liquidado' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($aval->estado) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic">Este cliente no funge como aval en ningún crédito actualmente.</p>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>