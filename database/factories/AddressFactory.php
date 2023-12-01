<?php

namespace Database\Factories;

use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Address::class;

    public function definition()
    {
        static $index = 0;

        // Obtén todos los IDs de la tabla person
        $peopleID = \App\Models\Person::pluck('per_id')->toArray();

        // Obtén el próximo ID a tomar según el índice
        $perId = $peopleID[$index];

        // Incrementa el índice para el próximo uso
        $index++;

        return [
            'dir_calle_av' => $this->faker->address,
            'dis_id' => 140101,
            'pro_id' => 1401,
            'dep_id' => 14,
            'per_id' => $perId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
