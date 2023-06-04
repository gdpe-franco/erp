<?php

namespace Database\Factories\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContabilidadFinanzas\Transaccion>
 */
class TransaccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'detalles' => $this->faker->sentence(4),
            'fecha' => $this->faker->date(),
        ];
    }
}
