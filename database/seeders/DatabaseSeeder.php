<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,    // 7 usuarios, uno por rol
            HabitacionesSeeder::class,  // pisos, módulos y habitaciones
            TarifasSeeder::class,       // 22 tarifas en COP por categoría
        ]);
    }
}
