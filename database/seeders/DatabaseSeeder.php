<?php 
namespace Database\Seeders; 

use Illuminate\Database\Seeder; 
use App\Models\User; 
use App\Models\Plaza; 
use App\Models\Grupo; 
use App\Models\Cliente; 
use App\Models\Prestamo; 
use App\Models\CalendarioPago; 
use App\Models\Pago; 
use Illuminate\Support\Facades\Hash; 
use Carbon\Carbon; 

class DatabaseSeeder extends Seeder 
{ 
    public function run(): void 
    { 
        $password = Hash::make('password'); 

        // 1. ADMIN
        $admin = User::create([
            'name' => 'Admin Sistema', 
            'email' => 'admin@prueba.com', 
            'password' => $password, 
            'role' => \App\Enums\UserRole::ADMIN->value
        ]); 

        // 2. CREACIÓN DE 4 EJECUTIVOS
        $ejecutivos = []; 
        foreach(['Juan', 'Pedro', 'Luis', 'Carlos'] as $nombre) { 
            $ejecutivos[] = User::create([
                'name' => $nombre . ' Ejecutivo', 
                'email' => strtolower($nombre) . '.ejecutivo@prueba.com', 
                'password' => $password, 
                'role' => \App\Enums\UserRole::EJECUTIVO->value
            ]); 
        } 

        // 3. CREACIÓN DE 4 SUPERVISORAS Y PLAZAS
        $plazas = []; 
        $zonas = ['Centro', 'Norte', 'Sur', 'Occidente'];
        foreach($zonas as $idx => $zona) { 
            $supervisora = User::create([
                'name' => 'Super ' . $zona, 
                'email' => 'super'.strtolower($zona).'@prueba.com', 
                'password' => $password, 
                'role' => \App\Enums\UserRole::SUPERVISORA->value
            ]); 

            $plazas[] = Plaza::create([
                'nombre' => 'Plaza '.$zona, 
                'ejecutivo_id' => $ejecutivos[$idx]->id, 
                'supervisora_id' => $supervisora->id, 
                'estado' => 'activo'
            ]); 
        } 

        // 4. CREACIÓN DE 4 PROMOTORAS Y GRUPOS
        $grupos = []; 
        $nombresGrupos = ['Lealtad', 'Progreso', 'Éxito', 'Esperanza'];
        foreach($nombresGrupos as $idx => $nom) { 
            $promotora = User::create([
                'name' => 'Promotora ' . $nom, 
                'email' => 'promotora'.strtolower($nom).'@prueba.com', 
                'password' => $password, 
                'role' => \App\Enums\UserRole::PROMOTORA->value
            ]); 

            $grupos[] = Grupo::create([
                'plaza_id' => $plazas[$idx]->id, 
                'promotora_id' => $promotora->id, // Vinculamos la promotora al grupo
                'nombre' => 'Grupo '.$nom, 
                'dia_reunion' => 'Lunes', 
                'estado' => 'activo'
            ]); 
        } 

        // 5. CREACIÓN DE 10 PRÉSTAMOS (10 Titulares + 10 Avales = 20 Clientes)
        // Distribución: 3 Liquidados, 4 Activos sanos, 3 Morosos
        $escenarios = [
            'liquidado', 'liquidado', 'liquidado', 
            'activo_sano', 'activo_sano', 'activo_sano', 'activo_sano',
            'moroso', 'moroso', 'moroso'
        ];

        foreach($escenarios as $idx => $tipo) { 
            // Asignamos al cliente a uno de los 4 grupos equitativamente
            $grupoActual = $grupos[$idx % 4];

            // A) Crear el Aval
            $aval = Cliente::create([
                'grupo_id' => $grupoActual->id, 
                'nombre' => 'Aval ' . ($idx + 1) . ' Respaldo', 
                'curp' => 'AVAL000000HDF' . sprintf('%02d', $idx), 
                'telefono' => '555-' . rand(1000, 9999), 
                'direccion' => 'Domicilio Aval ' . ($idx + 1), 
                'fecha_registro' => now()->subMonths(3),
                'perfil_riesgo' => 'bajo',
                'estado' => 'activo'
            ]);

            // B) Crear el Titular
            $titular = Cliente::create([
                'grupo_id' => $grupoActual->id, 
                'nombre' => 'Titular ' . ($idx + 1) . ' Cliente', 
                'curp' => 'TITU000000HDF' . sprintf('%02d', $idx), 
                'telefono' => '555-' . rand(1000, 9999), 
                'direccion' => 'Domicilio Titular ' . ($idx + 1), 
                'fecha_registro' => now()->subMonths(3),
                'perfil_riesgo' => 'bajo',
                'estado' => 'activo'
            ]);

            // C) Configurar fechas y montos según el escenario
            $monto = rand(2, 5) * 1000; 
            $montoTotal = $monto * 1.20; 

            if ($tipo === 'liquidado') { 
                $fechaBase = now()->subWeeks(13)->previous(Carbon::SATURDAY); 
                $estadoPrestamo = 'liquidado'; 
            } elseif ($tipo === 'moroso') { 
                // Inició hace 5 semanas (para que deba las últimas 2)
                $fechaBase = now()->subWeeks(5)->previous(Carbon::SATURDAY); 
                $estadoPrestamo = 'activo'; 
            } else { 
                // Activo sano (Inició hace 3 semanas, todo pagado)
                $fechaBase = now()->subWeeks(3)->previous(Carbon::SATURDAY); 
                $estadoPrestamo = 'activo'; 
            } 

            $prestamo = Prestamo::create([
                'cliente_id' => $titular->id, 
                'aval_id' => $aval->id, 
                'grupo_id' => $grupoActual->id, 
                'monto_prestado' => $monto, 
                'monto_total_pagar' => $montoTotal, 
                'tasa_interes' => 20.00, 
                'semanas' => 12, 
                'fecha_inicio' => $fechaBase, 
                'estado' => $estadoPrestamo
            ]); 

            $cuotaSemanal = $montoTotal / 12; 

            // D) Generar Calendario de Pagos
            for ($i = 1; $i <= 12; $i++) { 
                $fechaVencimiento = $fechaBase->copy()->addWeeks($i); 
                $estadoCuota = 'pendiente'; 

                if ($tipo === 'liquidado') { 
                    $estadoCuota = 'pagado'; 
                } elseif ($tipo === 'activo_sano') { 
                    if ($i <= 3) $estadoCuota = 'pagado'; 
                } elseif ($tipo === 'moroso') { 
                    if ($i <= 2) $estadoCuota = 'pagado'; 
                    elseif ($i <= 4) $estadoCuota = 'falla'; // Falla en la cuota 3 y 4
                } 

                $cuota = CalendarioPago::create([
                    'prestamo_id' => $prestamo->id, 
                    'numero_semana' => $i, 
                    'monto_esperado' => $cuotaSemanal, 
                    'fecha_vencimiento' => $fechaVencimiento, 
                    'estado' => $estadoCuota
                ]); 

                // Si está pagado, le creamos su recibo de Pago
                if ($estadoCuota === 'pagado') { 
                    Pago::create([
                        'calendario_pago_id' => $cuota->id, 
                        'monto_pagado' => $cuotaSemanal, 
                        'fecha_pago' => $fechaVencimiento, 
                        'registrado_por' => $admin->id
                    ]); 
                } 
            } 
        } 
    } 
}