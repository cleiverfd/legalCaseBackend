<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\PeopleNatural;
use App\Models\Person;

class PersonFactory extends Factory
{
    protected $model = Person::class;
    
 

    public function definition()
    {
        static $index = 0;
        $peopleNaturalIds = PeopleNatural::where('nat_id', '>', 10)->pluck('nat_id')->toArray();

        // Verificamos si hay al menos 10 elementos antes de seleccionar el subconjunto
        // if (count($peopleNaturalIds) < 10) {
        //     throw new \Exception("No hay suficientes elementos en PeopleNatural para seleccionar un subconjunto.");
        // }

        // Seleccionamos un subconjunto de 10 elementos a partir del índice actual
        // $selectedIds = array_slice($peopleNaturalIds, self::$index, 10);
        $selectedIds = $peopleNaturalIds[$index];
        $index++;

        // // Incrementamos el índice para la próxima llamada
        // self::$index += 10;

        // // Seleccionamos el ID actual del subconjunto
        // $ID = $selectedIds[0];

        return [
            'nat_id' => $selectedIds,
            'jur_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
