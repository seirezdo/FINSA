<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use App\Models\Plaza;
use App\Models\Grupo;
use App\Models\Cliente; // Asegúrate de tener este modelo creado [1, 4]
use App\Enums\UserRole; 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- 1. CREACIÓN DE PERSONAS (Identidades Únicas) ---
        // Creamos las identidades físicas para cada rol del sistema [1, 5]
        
        $pAdmin = Persona::create(['nombre' => 'Admin', 'apellido_paterno' => 'Sistema', 'numero_documento' => '11111111', 'tipo_documento' => 'INE']);
        $pEjecutivo = Persona::create(['nombre' => 'Juan', 'apellido_paterno' => 'Pérez', 'numero_documento' => '22222222', 'tipo_documento' => 'INE']);
        
        // Supervisoras
        $pSuper1 = Persona::create(['nombre' => 'María', 'apellido_paterno' => 'López', 'numero_documento' => '33333333', 'tipo_documento' => 'INE']);
        $pSuper2 = Persona::create(['nombre' => 'Rosa', 'apellido_paterno' => 'Díaz', 'numero_documento' => '44444444', 'tipo_documento' => 'INE']);

        // Promotoras
        $pPromotora1 = Persona::create(['nombre' => 'Lucía', 'apellido_paterno' => 'Méndez', 'numero_documento' => '55555555', 'tipo_documento' => 'INE']);
        $pPromotora2 = Persona::create(['nombre' => 'Carmen', 'apellido_paterno' => 'Ortiz', 'numero_documento' => '66666666', 'tipo_documento' => 'INE']);

        // Clientes (Ejemplo de registro rápido con documentos nullable) [Turnos 20-22]
        $pCliente1 = Persona::create(['nombre' => 'Sergio', 'apellido_paterno' => 'García', 'numero_documento' => '77777777', 'tipo_documento' => 'INE']);
        $pCliente2 = Persona::create(['nombre' => 'Beatriz', 'apellido_paterno' => 'Luna', 'numero_documento' => '88888888', 'tipo_documento' => 'INE']);

        // --- 2. CREACIÓN DE USUARIOS (Cuentas de Acceso) ---
        // Usamos los Enums para garantizar la seguridad de tipos en los roles [1, 6, 7]
        
        $password = Hash::make('password'); // Encriptación profesional [1, 8]

        User::create(['persona_id' => $pAdmin->id, 'name' => 'Admin', 'email' => 'admin@prueba.com', 'password' => $password, 'role' => UserRole::ADMIN->value]);
        User::create(['persona_id' => $pEjecutivo->id, 'name' => 'Juan', 'email' => 'ejecutivo@prueba.com', 'password' => $password, 'role' => UserRole::EJECUTIVO->value]);
        
        // Cuentas para Supervisoras
        User::create(['persona_id' => $pSuper1->id, 'name' => 'María', 'email' => 'super1@prueba.com', 'password' => $password, 'role' => UserRole::SUPERVISORA->value]);
        User::create(['persona_id' => $pSuper2->id, 'name' => 'Rosa', 'email' => 'super2@prueba.com', 'password' => $password, 'role' => UserRole::SUPERVISORA->value]);

        // Cuentas para Promotoras
        User::create(['persona_id' => $pPromotora1->id, 'name' => 'Lucía', 'email' => 'promo1@prueba.com', 'password' => $password, 'role' => UserRole::PROMOTORA->value]);
        User::create(['persona_id' => $pPromotora2->id, 'name' => 'Carmen', 'email' => 'promo2@prueba.com', 'password' => $password, 'role' => UserRole::PROMOTORA->value]);

        // --- 3. ESTRUCTURA OPERATIVA (Plazas y Grupos) ---
        
        $plazaCentro = Plaza::create([
            'nombre' => 'Plaza Centro',
            'ejecutivo_id' => $pEjecutivo->id,
            'supervisora_id' => $pSuper1->id,
            'estado' => 'activo'
        ]);

        $grupoLealtad = Grupo::create([
            'plaza_id' => $plazaCentro->id,
            'nombre' => 'Grupo Lealtad',
            'dia_cobro' => 1, // Lunes
            'estado' => 'ACTIVO'
        ]);

        // --- 4. ASIGNACIÓN DE CLIENTES ---
        // Los clientes se vinculan a un grupo específico [1, 3]
        
        Cliente::create([
            'persona_id' => $pCliente1->id,
            'grupo_id' => $grupoLealtad->id,
            'curp' => 'GARS010101HDFRR01', // El CURP debe ser único para evitar fraude [1, 3]
            'estado' => 'activo'
        ]);

        Cliente::create([
            'persona_id' => $pCliente2->id,
            'grupo_id' => $grupoLealtad->id,
            'curp' => 'LUNB020202MDFRR02',
            'estado' => 'activo'
        ]);
    }
}