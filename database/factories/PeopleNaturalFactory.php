<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PeopleNatural;

class PeopleNaturalFactory extends Factory
{
    protected $model = PeopleNatural::class;
    
    

    public function definition()
    {
        return [
            'nat_dni' => $this->faker->unique()->numerify('########'),
            'nat_apellido_paterno' => $this->faker->lastName,
            'nat_apellido_materno' => $this->faker->lastName,
            'nat_nombres' => $this->faker->firstName,
            'nat_telefono' => '+0519' . $this->faker->numerify('########'),
            'nat_correo' => strtolower($this->faker->lastName) .$this->faker->unique()->numerify('########'). '@gmail.com',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
