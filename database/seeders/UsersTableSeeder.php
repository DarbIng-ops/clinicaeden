<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Un usuario de desarrollo por cada rol del sistema.
     * Usa updateOrCreate → seguro de ejecutar múltiples veces.
     */
    public function run(): void
    {
        $users = [
            [
                'email'           => 'admin@clinicaeden.com',
                'name'            => 'Administrador',
                'role'            => 'admin',
                'activo'          => true,
            ],
            [
                'email'           => 'medico.general@clinicaeden.com',
                'name'            => 'Dr. Juan Pérez',
                'role'            => 'medico_general',
                'numero_licencia' => 'MG001',
                'telefono'        => '555-0101',
                'especialidad'    => 'Medicina General',
                'activo'          => true,
            ],
            [
                'email'           => 'medico.especialista@clinicaeden.com',
                'name'            => 'Dr. María González',
                'role'            => 'medico_especialista',
                'numero_licencia' => 'ME001',
                'telefono'        => '555-0102',
                'especialidad'    => 'Cardiología',
                'activo'          => true,
            ],
            [
                'email'           => 'recepcion@clinicaeden.com',
                'name'            => 'Recepción',
                'role'            => 'recepcionista',
                'telefono'        => '555-0200',
                'activo'          => true,
            ],
            [
                'email'           => 'jefe.enfermeria@clinicaeden.com',
                'name'            => 'Jefe Enfermería',
                'role'            => 'jefe_enfermeria',
                'telefono'        => '555-0400',
                'activo'          => true,
            ],
            [
                'email'           => 'auxiliar@clinicaeden.com',
                'name'            => 'Auxiliar Enfermería',
                'role'            => 'auxiliar_enfermeria',
                'telefono'        => '555-0500',
                'activo'          => true,
            ],
            [
                'email'           => 'caja@clinicaeden.com',
                'name'            => 'Caja',
                'apellido'        => 'Sistema',
                'role'            => 'caja',
                'telefono'        => '555-0300',
                'activo'          => true,
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, ['password' => Hash::make('password')])
            );
        }

        $this->command->info('✓ 7 usuarios de desarrollo creados/actualizados (1 por rol).');
    }
}
