<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\User;
use App\Models\Plaza;
use App\Models\Grupo;
use App\Models\Cliente;
use App\Models\Prestamo;
use App\Models\CalendarioPago;
use App\Models\Pago;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

       User::create([
        'name'     => 'Admin Sistema', 
        'email'    => 'admin@prueba.com', 
        'password' => $password, 
        'role'     => \App\Enums\UserRole::ADMIN->value
    ]);
        $ejecutivos = [];
    foreach(['Juan', 'Pedro', 'Luis'] as $nombre) {
        $ejecutivos[] = User::create([
            'name'     => $nombre . ' Pérez', 
            'email'    => strtolower($nombre) . '@prueba.com', 
            'password' => $password, 
            'role'     => \App\Enums\UserRole::EJECUTIVO->value
        ]);
    }


        // --- 2. SUPERVISORAS Y PLAZAS (3 registros) ---
             $plazas = [];
        foreach(['Centro', 'Norte', 'Sur'] as $idx => $zona) {
            
            // 1. CORREGIDO: Creamos a la Supervisora directamente en la tabla Users
            $supervisora = User::create([
                'name'     => 'Super '.$zona.' López', 
                'email'    => 'super'.$idx.'@prueba.com', 
                'password' => $password, 
                'role'     => \App\Enums\UserRole::SUPERVISORA->value
            ]);

            // 2. CORREGIDO: Creamos la Plaza apuntando a los IDs directos de los usuarios
            $plazas[] = Plaza::create([
                'nombre'         => 'Plaza '.$zona,
                'ejecutivo_id'   => $ejecutivos[$idx]->id, // Antes decía persona_id
                'supervisora_id' => $supervisora->id,      // Antes apuntaba a $pSup->id
                'estado'         => 'activo'    
            ]);       
        }

        // --- 3. GRUPOS (3 registros) ---
      $grupos = [];
        foreach(['Lealtad', 'Progreso', 'Éxito'] as $idx => $nom) {
            $grupos[] = Grupo::create([
                'plaza_id'    => $plazas[$idx]->id,
                'nombre'      => 'Grupo '.$nom,
                // Nota: En tu migración / controlador le pusimos 'dia_reunion', ajusta si usaste 'dia_cobro'
                'dia_reunion' => 'Lunes', 
                'estado'      => 'activo'
            ]);
        }

        // OBTENEMOS AL ADMIN: Lo buscamos en la tabla users para que pueda "registrar" los pagos
        $admin = User::where('role', \App\Enums\UserRole::ADMIN->value)->first();

        $clientesData = [
            ['nombre' => 'Sergio', 'monto' => 2000, 'pagado' => true, 'curp' => 'GARS800101HDFRRN01'],
            ['nombre' => 'Beatriz', 'monto' => 4000, 'pagado' => false, 'curp' => 'LUNB850202MDFXNB02'],
            ['nombre' => 'Ricardo', 'monto' => 3000, 'pagado' => false, 'curp' => 'GARR900303HDFRRN03'],
        ];

        foreach($clientesData as $idx => $data) {
            
            // 1. CREACIÓN UNIFICADA DEL CLIENTE (Ya no existe Persona)
            $cliente = Cliente::create([
                'grupo_id'  => $grupos[$idx]->id, 
                'nombre'    => $data['nombre'] . ' García', // Juntamos el nombre y apellido directamente aquí
                'curp'      => $data['curp'], 
                'telefono'  => '555-' . rand(1000, 9999),
                'direccion' => 'Dirección de prueba',
                'estado'    => 'activo'
            ]);

            // 2. CREACIÓN DEL PRÉSTAMO
            $montoTotal = $data['monto'] * 1.20; // 20% de interés de ejemplo
            $prestamo = Prestamo::create([
                'cliente_id'        => $cliente->id, // CORREGIDO: Apunta directo al id del cliente
                'aval_id'           => null,         // CORREGIDO: Lo dejamos nulo porque el admin ya no puede ser aval
                'grupo_id'          => $grupos[$idx]->id,
                'monto_prestado'    => $data['monto'],
                'monto_total_pagar' => $montoTotal,
                'tasa_interes'      => 20.00,
                'semanas'           => 12,
                'fecha_inicio'      => now()->subWeeks(12),
                'estado'            => $data['pagado'] ? 'liquidado' : 'activo'   
            ]);
     
            // 3. CALENDARIO DE PAGOS
            $cuotaSemanal = $montoTotal / 12;
            for ($i = 1; $i <= 12; $i++) {
                $cuota = CalendarioPago::create([
                    'prestamo_id'       => $prestamo->id,
                    'numero_semana'     => $i,
                    'monto_esperado'    => $cuotaSemanal,
                    'fecha_vencimiento' => Carbon::parse($prestamo->fecha_inicio)->addWeeks($i),
                    'estado'            => $data['pagado'] ? 'pagado' : 'pendiente'
                ]);

                // Si el cliente es "cumplido", le creamos pagos reales para que el Dashboard sume
                if ($data['pagado']) {
                    Pago::create([
                        'calendario_pago_id' => $cuota->id,
                        'monto_pagado'       => $cuotaSemanal,
                        'fecha_pago'         => $cuota->fecha_vencimiento,
                        'registrado_por'     => $admin->id // CORREGIDO: Usamos el ID del usuario Admin
                    ]);
                }
            }
        }
    } // <-- Cierre de la función run()
} // <-- Cierre de la clase DatabaseSeeder