<?php

namespace Database\Factories\ContabilidadFinanzas;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContabilidadFinanzas\DetalleTransaccion>
 */
class DetalleTransaccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cuenta_id' => $this->faker->numberBetween(1, 94),
            'transaccion_id' => $this->faker->numberBetween(1, 10),
            'monto' => $this->faker->randomFloat(1, 1000, 999999)
        ];
    }
}
