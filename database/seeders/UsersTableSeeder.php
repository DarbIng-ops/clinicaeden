<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'medico.general@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'medico_general',
            'numero_licencia' => 'MG001',
            'telefono' => '555-0101',
            'activo' => true,
        ]);

        User::create([
            'name' => 'Dr. María González',
            'email' => 'medico.especialista@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'medico_especialista',
            'especialidad' => 'Cardiología',
            'numero_licencia' => 'ME001',
            'telefono' => '555-0102',
            'activo' => true,
        ]);

        User::create([
            'name' => 'Recepción',
            'email' => 'recepcion@clinicaeden.com',
            'password' => Hash::make('password'),
            'role' => 'recepcionista',
        ]);
    }
}
