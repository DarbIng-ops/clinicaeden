<?php

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

        // Crear usuarios adicionales para enfermería
        $jefeEnfermeria1 = User::create([
            'name' => 'Lic. Ana Martínez',
            'email' => 'jefe.enfermeria1@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'jefe_enfermeria',
            'telefono' => '555-0201',
            'activo' => true,
        ]);

        $jefeEnfermeria2 = User::create([
            'name' => 'Lic. Carlos Rodríguez',
            'email' => 'jefe.enfermeria2@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'jefe_enfermeria',
            'telefono' => '555-0202',
            'activo' => true,
        ]);

        $jefeEnfermeria3 = User::create([
            'name' => 'Lic. Laura Fernández',
            'email' => 'jefe.enfermeria3@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'jefe_enfermeria',
            'telefono' => '555-0203',
            'activo' => true,
        ]);

        $jefeEnfermeria4 = User::create([
            'name' => 'Lic. Miguel Torres',
            'email' => 'jefe.enfermeria4@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'jefe_enfermeria',
            'telefono' => '555-0204',
            'activo' => true,
        ]);

        // Crear auxiliares de enfermería
        $auxiliares = [];
        for ($i = 1; $i <= 36; $i++) {
            $auxiliares[] = User::create([
                'name' => 'Aux. Enfermería ' . $i,
                'email' => 'auxiliar.enfermeria' . $i . '@clinicaeden.com',
                'password' => Hash::make('password'),
                'role' => 'auxiliar_enfermeria',
                'telefono' => '555-' . str_pad(300 + $i, 4, '0', STR_PAD_LEFT),
                'activo' => true,
            ]);
        }

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
            'jefe_enfermeria_id' => $jefeEnfermeria1->id,
            'activo' => true,
        ]);

        $modulo2Piso1 = ModuloEnfermeria::create([
            'piso_id' => $piso1->id,
            'nombre' => 'Módulo Enfermería B',
            'tipo' => 'general',
            'descripcion' => 'Módulo de enfermería general - Piso 1',
            'jefe_enfermeria_id' => $jefeEnfermeria2->id,
            'activo' => true,
        ]);

        // Asignar auxiliares a módulos del piso 1 (10 auxiliares por módulo)
        for ($i = 0; $i < 10; $i++) {
            $modulo1Piso1->auxiliares()->attach($auxiliares[$i]->id, ['activo' => true]);
            $modulo2Piso1->auxiliares()->attach($auxiliares[$i + 10]->id, ['activo' => true]);
        }

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
            'jefe_enfermeria_id' => $jefeEnfermeria3->id,
            'activo' => true,
        ]);

        // Asignar auxiliares al módulo de partos (8 auxiliares)
        for ($i = 20; $i < 28; $i++) {
            $modulo1Piso2->auxiliares()->attach($auxiliares[$i]->id, ['activo' => true]);
        }

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
            'jefe_enfermeria_id' => $jefeEnfermeria4->id,
            'activo' => true,
        ]);

        // Asignar auxiliares al módulo de hospitalización (8 auxiliares)
        for ($i = 28; $i < 36; $i++) {
            $modulo2Piso2->auxiliares()->attach($auxiliares[$i]->id, ['activo' => true]);
        }

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