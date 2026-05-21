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

        // --- 1. PERSONAS Y USUARIOS (ADMIN Y EJECUTIVOS) ---
        $pAdmin = Persona::create(['nombre' => 'Admin', 'apellido_paterno' => 'Sistema', 'numero_documento' => '11111111', 'tipo_documento' => 'INE']);
        User::create(['persona_id' => $pAdmin->id, 'name' => 'Admin', 'email' => 'admin@prueba.com', 'password' => $password, 'role' => UserRole::ADMIN->value]);

        $ejecutivos = [];
        foreach(['Juan', 'Pedro', 'Luis'] as $nombre) {
            $p = Persona::create(['nombre' => $nombre, 'apellido_paterno' => 'Pérez', 'numero_documento' => rand(1000,9999), 'tipo_documento' => 'INE']);
            $ejecutivos[] = User::create(['persona_id' => $p->id, 'name' => $nombre, 'email' => strtolower($nombre).'@prueba.com', 'password' => $password, 'role' => UserRole::EJECUTIVO->value]);
        }

        // --- 2. SUPERVISORAS Y PLAZAS (3 registros) ---
        $plazas = [];
        foreach(['Centro', 'Norte', 'Sur'] as $idx => $zona) {
            $pSup = Persona::create(['nombre' => 'Super-'.$zona, 'apellido_paterno' => 'López', 'numero_documento' => rand(1000,9999), 'tipo_documento' => 'INE']);
            User::create(['persona_id' => $pSup->id, 'name' => 'Super '.$zona, 'email' => 'super'.$idx.'@prueba.com', 'password' => $password, 'role' => UserRole::SUPERVISORA->value]);

            $plazas[] = Plaza::create([
                'nombre' => 'Plaza '.$zona,
                'ejecutivo_id' => $ejecutivos[$idx]->persona_id,
                'supervisora_id' => $pSup->id,
                'estado' => 'activo'
            ]);
        }

        // --- 3. GRUPOS (3 registros) ---
        $grupos = [];
        foreach(['Lealtad', 'Progreso', 'Éxito'] as $idx => $nom) {
            $grupos[] = Grupo::create([
                'plaza_id' => $plazas[$idx]->id,
                'nombre' => 'Grupo '.$nom,
                'dia_cobro' => $idx + 1, // Lunes, Martes, Miércoles
                'estado' => 'ACTIVO'
            ]);
        }

        // --- 4. CLIENTES Y PRÉSTAMOS (3 registros con escenarios distintos) ---
       $clientesData = [
    ['nombre' => 'Sergio', 'monto' => 2000, 'pagado' => true, 'curp' => 'GARS800101HDFRRN01'],
    ['nombre' => 'Beatriz', 'monto' => 4000, 'pagado' => false, 'curp' => 'LUNB850202MDFXNB02'],
    ['nombre' => 'Ricardo', 'monto' => 3000, 'pagado' => false, 'curp' => 'GARR900303HDFRRN03'],
];

      
        foreach($clientesData as $idx => $data) {
    $pCli = Persona::create([
        'nombre' => $data['nombre'], 
        'apellido_paterno' => 'García', 
        'numero_documento' => rand(1000,9999), 
        'tipo_documento' => 'INE'
    ]);

    // Ahora enviamos el 'curp' requerido por tu migración [1]
    $cliente = Cliente::create([
        'persona_id' => $pCli->id, 
        'grupo_id' => $grupos[$idx]->id, 
        'curp' => $data['curp'], // <--- LÍNEA CORREGIDA
        'estado' => 'activo'
    ]);
            // Creamos el préstamo con los nombres de columna corregidos [Conversación previa]
            $montoTotal = $data['monto'] * 1.20; // 20% de interés de ejemplo
            $prestamo = Prestamo::create([
                'cliente_id' => $cliente->persona_id,
                'aval_id' => $pAdmin->id, // Admin como aval para el ejemplo
                'grupo_id' => $grupos[$idx]->id,
                'monto_prestado' => $data['monto'],
                'monto_total_pagar' => $montoTotal,
                'tasa_interes' => 20.00,
                'semanas' => 12,
                'fecha_inicio' => now()->subWeeks(12),
                'estado' => $data['pagado'] ? 'liquidado' : 'activo'
            ]);

            // Crear el Calendario (12 semanas)
            $cuotaSemanal = $montoTotal / 12;
            for ($i = 1; $i <= 12; $i++) {
                $cuota = CalendarioPago::create([
                    'prestamo_id' => $prestamo->id,
                    'numero_semana' => $i,
                    'monto_esperado' => $cuotaSemanal,
                    'fecha_vencimiento' => Carbon::parse($prestamo->fecha_inicio)->addWeeks($i),
                    'estado' => $data['pagado'] ? 'pagado' : 'pendiente'
                ]);

                // Si el cliente es "cumplido", le creamos pagos reales para que el Dashboard sume [1, 5]
                if ($data['pagado']) {
                    Pago::create([
                        'calendario_pago_id' => $cuota->id,
                        'monto_pagado' => $cuotaSemanal,
                        'fecha_pago' => $cuota->fecha_vencimiento,
                        'registrado_por' => $pAdmin->id
                    ]);
                }
            }
        }
    }
}