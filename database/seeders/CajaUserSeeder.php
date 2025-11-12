<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CajaUserSeeder extends Seeder
{
    /**
     * Crear usuario de caja faltante
     */
    public function run(): void
    {
        // Verificar si ya existe un usuario de caja
        $cajaExists = User::where('role', 'caja')->exists();
        
        if (!$cajaExists) {
            User::create([
                'name' => 'Caja',
                'apellido' => 'Sistema',
                'email' => 'caja@clinicaeden.com',
                'password' => Hash::make('password'),
                'role' => 'caja',
                'telefono' => '555-0300',
                'activo' => true,
            ]);
            
            $this->command->info('Usuario de caja creado exitosamente.');
        } else {
            $this->command->info('Usuario de caja ya existe.');
        }
    }
}