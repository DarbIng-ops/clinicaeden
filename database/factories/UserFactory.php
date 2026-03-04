<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'admin',
            'activo' => true,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'remember_token' => Str::random(10),
            'profile_photo_path' => null,
            'current_team_id' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin', 'activo' => true]);
    }

    public function recepcionista(): static
    {
        return $this->state(['role' => 'recepcionista', 'activo' => true]);
    }

    public function medicoGeneral(): static
    {
        return $this->state(['role' => 'medico_general', 'activo' => true]);
    }

    public function medicoEspecialista(): static
    {
        return $this->state(['role' => 'medico_especialista', 'activo' => true]);
    }

    public function jefeEnfermeria(): static
    {
        return $this->state(['role' => 'jefe_enfermeria', 'activo' => true]);
    }

    public function auxiliarEnfermeria(): static
    {
        return $this->state(['role' => 'auxiliar_enfermeria', 'activo' => true]);
    }

    public function caja(): static
    {
        return $this->state(['role' => 'caja', 'activo' => true]);
    }

    public function inactivo(): static
    {
        return $this->state(['activo' => false]);
    }

    /**
     * Indicate that the user should have a personal team.
     */
    public function withPersonalTeam(?callable $callback = null): static
    {
        if (! Features::hasTeamFeatures()) {
            return $this->state([]);
        }

        return $this->has(
            Team::factory()
                ->state(fn (array $attributes, User $user) => [
                    'name' => $user->name.'\'s Team',
                    'user_id' => $user->id,
                    'personal_team' => true,
                ])
                ->when(is_callable($callback), $callback),
            'ownedTeams'
        );
    }
}
