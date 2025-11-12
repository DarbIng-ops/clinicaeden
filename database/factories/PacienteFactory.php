<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'dni' => $this->faker->unique()->numerify('########'),
            'nombres' => $this->faker->firstName(),
            'apellidos' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->date('Y-m-d', '-18 years'),
            'sexo' => $this->faker->randomElement(['M', 'F']),
            'telefono' => $this->faker->numerify('300#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'direccion' => $this->faker->address(),
            'ciudad' => $this->faker->city(),
            'contacto_emergencia_nombre' => $this->faker->name(),
            'contacto_emergencia_telefono' => $this->faker->numerify('300#######'),
            'activo' => true,
        ];
    }
}
