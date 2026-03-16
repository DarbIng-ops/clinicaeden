<?php

/**
 * HabitacionesSeeder.php
 *
 * Puebla la base de datos con la estructura hospitalaria de prueba:
 * pisos, módulos de enfermería y habitaciones para desarrollo/testing.
 *
 * @package PulsoCore
 * @author  Alirio Portilla
 * @version 3.0.0
 */
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Piso;
use App\Models\ModuloEnfermeria;
use App\Models\Consultorio;
use App\Models\Habitacion;
use App\Models\SalaProcedimiento;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class HabitacionesSeeder extends Seeder
{
    public function run(): void
    {
        $jefeEnfermeria = User::where('email', 'jefe.enfermeria@pulsocore.com')->firstOrFail();

        // Crear Pisos
        $piso1 = Piso::create([
            'numero' => 1,
            'nombre' => 'Piso Principal',
            'descripcion' => 'Piso principal con consultorios y módulos de enfermería general',
            'activo' => true,
        ]);

        $piso2 = Piso::create([
            'numero' => 2,
            'nombre' => 'Piso Especializado',
            'descripcion' => 'Piso especializado en partos, neonatos y hospitalización general',
            'activo' => true,
        ]);

        // PISO 1 - 3 Consultorios
        for ($i = 1; $i <= 3; $i++) {
            Consultorio::create([
                'numero' => 'C' . $i,
                'piso_id' => $piso1->id,
                'nombre' => 'Consultorio ' . $i,
                'descripcion' => 'Consultorio para médicos generales',
                'disponible' => true,
            ]);
        }

        // PISO 1 - 2 Módulos de Enfermería
        $modulo1Piso1 = ModuloEnfermeria::create([
            'piso_id' => $piso1->id,
            'nombre' => 'Módulo Enfermería A',
            'tipo' => 'general',
            'descripcion' => 'Módulo de enfermería general - Piso 1',
            'jefe_enfermeria_id' => $jefeEnfermeria->id,
            'activo' => true,
        ]);

        $modulo2Piso1 = ModuloEnfermeria::create([
            'piso_id' => $piso1->id,
            'nombre' => 'Módulo Enfermería B',
            'tipo' => 'general',
            'descripcion' => 'Módulo de enfermería general - Piso 1',
            'jefe_enfermeria_id' => $jefeEnfermeria->id,
            'activo' => true,
        ]);

        // Habitaciones del módulo 1 piso 1 (5 habitaciones de 4 camas cada una)
        for ($i = 1; $i <= 5; $i++) {
            Habitacion::create([
                'numero' => '1A' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'modulo_id' => $modulo1Piso1->id,
                'capacidad' => 4,
                'tipo' => 'general',
                'descripcion' => 'Habitación general - 4 camas',
                'disponible' => true,
            ]);
        }

        // Habitaciones del módulo 2 piso 1 (5 habitaciones de 4 camas cada una)
        for ($i = 1; $i <= 5; $i++) {
            Habitacion::create([
                'numero' => '1B' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'modulo_id' => $modulo2Piso1->id,
                'capacidad' => 4,
                'tipo' => 'general',
                'descripcion' => 'Habitación general - 4 camas',
                'disponible' => true,
            ]);
        }

        // Salas de procedimientos para módulos del piso 1
        SalaProcedimiento::create([
            'modulo_id' => $modulo1Piso1->id,
            'numero' => 'SP1A',
            'nombre' => 'Sala de Procedimientos A',
            'descripcion' => 'Sala de procedimientos del módulo A',
            'disponible' => true,
        ]);

        SalaProcedimiento::create([
            'modulo_id' => $modulo2Piso1->id,
            'numero' => 'SP1B',
            'nombre' => 'Sala de Procedimientos B',
            'descripcion' => 'Sala de procedimientos del módulo B',
            'disponible' => true,
        ]);

        // PISO 2 - Módulo 1: Zona de partos y neonatos
        $modulo1Piso2 = ModuloEnfermeria::create([
            'piso_id' => $piso2->id,
            'nombre' => 'Módulo Partos y Neonatos',
            'tipo' => 'partos_neonatos',
            'descripcion' => 'Módulo especializado en partos y cuidados neonatales',
            'jefe_enfermeria_id' => $jefeEnfermeria->id,
            'activo' => true,
        ]);

        // Sala de partos (5 camas)
        Habitacion::create([
            'numero' => '2P01',
            'modulo_id' => $modulo1Piso2->id,
            'capacidad' => 5,
            'tipo' => 'partos',
            'descripcion' => 'Sala de partos - 5 camas',
            'disponible' => true,
        ]);

        // Habitación de bebés recién nacidos (10 cunas)
        Habitacion::create([
            'numero' => '2N01',
            'modulo_id' => $modulo1Piso2->id,
            'capacidad' => 10,
            'tipo' => 'neonatos',
            'descripcion' => 'Habitación de bebés recién nacidos - 10 cunas',
            'disponible' => true,
        ]);

        // Habitación de madres pre o post parto (5 camas)
        Habitacion::create([
            'numero' => '2M01',
            'modulo_id' => $modulo1Piso2->id,
            'capacidad' => 5,
            'tipo' => 'madres_pre_post_parto',
            'descripcion' => 'Habitación de madres pre o post parto - 5 camas',
            'disponible' => true,
        ]);

        // PISO 2 - Módulo 2: Hospitalización general
        $modulo2Piso2 = ModuloEnfermeria::create([
            'piso_id' => $piso2->id,
            'nombre' => 'Módulo Hospitalización General',
            'tipo' => 'hospitalizacion_general',
            'descripcion' => 'Módulo de hospitalización general',
            'jefe_enfermeria_id' => $jefeEnfermeria->id,
            'activo' => true,
        ]);

        // 3 habitaciones de 3 camas cada una
        for ($i = 1; $i <= 3; $i++) {
            Habitacion::create([
                'numero' => '2H' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'modulo_id' => $modulo2Piso2->id,
                'capacidad' => 3,
                'tipo' => 'general',
                'descripcion' => 'Habitación general - 3 camas',
                'disponible' => true,
            ]);
        }

        // 5 habitaciones de 2 camas cada una
        for ($i = 4; $i <= 8; $i++) {
            Habitacion::create([
                'numero' => '2H' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'modulo_id' => $modulo2Piso2->id,
                'capacidad' => 2,
                'tipo' => 'general',
                'descripcion' => 'Habitación general - 2 camas',
                'disponible' => true,
            ]);
        }

        // Sala de procedimientos para módulo de hospitalización
        SalaProcedimiento::create([
            'modulo_id' => $modulo2Piso2->id,
            'numero' => 'SP2H',
            'nombre' => 'Sala de Procedimientos Hospitalización',
            'descripcion' => 'Sala de procedimientos del módulo de hospitalización',
            'disponible' => true,
        ]);
    }
}
