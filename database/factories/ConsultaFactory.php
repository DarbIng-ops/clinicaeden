<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consulta>
 */
class ConsultaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'paciente_id' => \App\Models\Paciente::factory(),
            'medico_id' => \App\Models\User::factory()->create(['role' => 'medico_general'])->id,
            'fecha_consulta' => now(),
            'motivo' => $this->faker->sentence(),
            'motivo_consulta' => $this->faker->sentence(),
            'estado' => 'pendiente',
            'tipo_consulta' => 'general',
        ];
    }
}
