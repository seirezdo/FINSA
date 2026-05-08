<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Persona;
use App\Models\Plaza;
use App\Models\Grupo;
use App\Enums\UserRole; // Importante para la seguridad de tipos [2]
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. CREACIÓN DE PERSONAS (Identidades Físicas) ---
        // Primero creamos a los seres humanos que operarán el sistema [1]
        
        $personaAdmin = Persona::create([
            'nombre' => 'Admin', 
            'apellido_paterno' => 'Sistema', 
            'apellido_materno' => 'Principal',
            'numero_documento' => '11111111', 
            'tipo_documento' => 'INE'
        ]);

        $personaEjecutivo = Persona::create([
            'nombre' => 'Juan', 
            'apellido_paterno' => 'Pérez', 
            'apellido_materno' => 'García',
            'numero_documento' => '22222222', 
            'tipo_documento' => 'INE'
        ]);

        $personaSupervisora1 = Persona::create([
            'nombre' => 'María', 
            'apellido_paterno' => 'López', 
            'apellido_materno' => 'Sánchez',
            'numero_documento' => '33333333', 
            'tipo_documento' => 'INE'
        ]);

        $personaSupervisora2 = Persona::create([
            'nombre' => 'Rosa', 
            'apellido_paterno' => 'Díaz', 
            'apellido_materno' => 'Ruiz',
            'numero_documento' => '44444444', 
            'tipo_documento' => 'INE'
        ]);

        // --- 2. CREACIÓN DE USUARIOS (Cuentas de Acceso) ---
        // Vinculamos las identidades con credenciales, usando Enums para los roles [3]
        
        User::create([
            'persona_id' => $personaAdmin->id,
            'name' => 'Admin Principal',
            'email' => 'admin@prueba.com',
            'password' => Hash::make('password'), // Siempre encriptada por seguridad [4]
            'role' => UserRole::ADMIN->value, 
            'estado' => 'activo'
        ]);

        User::create([
            'persona_id' => $personaEjecutivo->id,
            'name' => 'Juan Ejecutivo',
            'email' => 'ejecutivo@prueba.com',
            'password' => Hash::make('password'),
            'role' => UserRole::EJECUTIVO->value, 
            'estado' => 'activo'
        ]);

        // --- 3. CREACIÓN DE PLAZAS (Jerarquía Operativa) ---
        // Ahora que tenemos al Ejecutivo y Supervisoras, creamos las Plazas [1]
        
        $plazaCentro = Plaza::create([
            'nombre' => 'Plaza Centro',
            'zona' => 'Centro Histórico',
            'ejecutivo_id' => $personaEjecutivo->id,
            'supervisora_id' => $personaSupervisora1->id,
            'estado' => 'activo'
        ]);

        $plazaNorte = Plaza::create([
            'nombre' => 'Plaza Norte',
            'zona' => 'Valle del Norte',
            'ejecutivo_id' => $personaEjecutivo->id,
            'supervisora_id' => $personaSupervisora2->id,
            'estado' => 'activo'
        ]);

        // --- 4. CREACIÓN DE GRUPOS (Unidades de Cobranza) ---
        // Finalmente creamos los grupos, ahora que las Plazas ya existen en variables [5, 6]
        
        Grupo::create([
            'plaza_id' => $plazaCentro->id,
            'nombre' => 'Grupo Lealtad',
            'dia_cobro' => 1, // Lunes
            'fecha_creacion' => now(),
            'estado' => 'ACTIVO'
        ]);

        Grupo::create([
            'plaza_id' => $plazaNorte->id,
            'nombre' => 'Grupo Esfuerzo',
            'dia_cobro' => 3, // Miércoles
            'fecha_creacion' => now(),
            'estado' => 'ACTIVO'
        ]);
    }
    
}