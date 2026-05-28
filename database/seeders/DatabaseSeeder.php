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
            'name'     => 'Admin Sistema', 
            'email'    => 'admin@prueba.com', 
            'password' => $password, 
            'role'     => \App\Enums\UserRole::ADMIN->value
        ]);

        // 2. EJECUTIVOS
        $ejecutivos = [];
        foreach(['Juan', 'Pedro', 'Luis'] as $nombre) {
            $ejecutivos[] = User::create([
                'name'     => $nombre . ' Pérez', 
                'email'    => strtolower($nombre) . '@prueba.com', 
                'password' => $password, 
                'role'     => \App\Enums\UserRole::EJECUTIVO->value
            ]);
        }

        // 3. SUPERVISORAS Y PLAZAS
        $plazas = [];
        foreach(['Centro', 'Norte', 'Sur'] as $idx => $zona) {
            $supervisora = User::create([
                'name'     => 'Super '.$zona.' López', 
                'email'    => 'super'.strtolower($zona).'@prueba.com', 
                'password' => $password, 
                'role'     => \App\Enums\UserRole::SUPERVISORA->value
            ]);

            $plazas[] = Plaza::create([
                'nombre'         => 'Plaza '.$zona,
                'ejecutivo_id'   => $ejecutivos[$idx]->id,
                'supervisora_id' => $supervisora->id,
                'estado'         => 'activo'    
            ]);       
        }

        // 4. GRUPOS
        $grupos = [];
        foreach(['Lealtad', 'Progreso', 'Éxito'] as $idx => $nom) {
            $grupos[] = Grupo::create([
                'plaza_id'    => $plazas[$idx]->id,
                'nombre'      => 'Grupo '.$nom,
                'dia_reunion' => 'Lunes', 
                'estado'      => 'activo'
            ]);
        }

        // 5. CLIENTES (CONFIGURADOS COMO ESCENARIOS DE PRUEBA)
        $clientesData = [
            ['nombre' => 'Sergio',  'monto' => 2000, 'tipo' => 'historico', 'curp' => 'GARS800101HDFRRN01'],
            ['nombre' => 'Beatriz', 'monto' => 4000, 'tipo' => 'moroso',    'curp' => 'LUNB850202MDFXNB02'],
            ['nombre' => 'Ricardo', 'monto' => 3000, 'tipo' => 'nuevo',     'curp' => 'GARR900303HDFRRN03'],
        ];

        foreach($clientesData as $idx => $data) {
            
            $cliente = Cliente::create([
                'grupo_id'  => $grupos[$idx]->id, 
                'nombre'    => $data['nombre'] . ' García',
                'curp'      => $data['curp'], 
                'telefono'  => '555-' . rand(1000, 9999),
                'direccion' => 'Dirección de prueba',
                'estado'    => 'activo'
            ]);

            // LÓGICA DE VIAJE EN EL TIEMPO Y SÁBADOS
            if ($data['tipo'] === 'historico') {
                $fechaBase = now()->subWeeks(13)->previous(Carbon::SATURDAY); // Empezó hace meses
                $estadoPrestamo = 'liquidado';
            } elseif ($data['tipo'] === 'moroso') {
                $fechaBase = now()->subWeeks(3)->previous(Carbon::SATURDAY);  // Beatriz: Empezó hace 3 semanas
                $estadoPrestamo = 'activo';
            } else {
                $fechaBase = now()->previous(Carbon::SATURDAY);               // Ricardo: Empieza recién
                $estadoPrestamo = 'activo';
            }

            $montoTotal = $data['monto'] * 1.20; 
            $prestamo = Prestamo::create([
                'cliente_id'        => $cliente->id,
                'aval_id'           => null,        
                'grupo_id'          => $grupos[$idx]->id,
                'monto_prestado'    => $data['monto'],
                'monto_total_pagar' => $montoTotal,
                'tasa_interes'      => 20.00,
                'semanas'           => 12,
                'fecha_inicio'      => $fechaBase,
                'estado'            => $estadoPrestamo   
            ]);
     
            $cuotaSemanal = $montoTotal / 12;
            
            // GENERAR CALENDARIO DE PAGOS
            for ($i = 1; $i <= 12; $i++) {
                $fechaVencimiento = $fechaBase->copy()->addWeeks($i);
                $estadoCuota = 'pendiente'; // Por defecto todas nacen pendientes

                // Personalizar estado de cuota según el cliente
                if ($data['tipo'] === 'historico') {
                    $estadoCuota = 'pagado';
                } elseif ($data['tipo'] === 'moroso') {
                    if ($i === 1) $estadoCuota = 'pagado'; // Beatriz pagó la 1
                    if ($i === 2) $estadoCuota = 'falla';  // Beatriz falló la 2
                }

                $cuota = CalendarioPago::create([
                    'prestamo_id'       => $prestamo->id,
                    'numero_semana'     => $i,
                    'monto_esperado'    => $cuotaSemanal,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'estado'            => $estadoCuota
                ]);

                // Crear pago real SOLO si el estatus final de la cuota fue 'pagado'
                if ($estadoCuota === 'pagado') {
                    Pago::create([
                        'calendario_pago_id' => $cuota->id,
                        'monto_pagado'       => $cuotaSemanal,
                        'fecha_pago'         => $fechaVencimiento,
                        'registrado_por'     => $admin->id
                    ]);
                }
            }
        }
    } 
}