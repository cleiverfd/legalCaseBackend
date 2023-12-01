<?php

namespace Database\Factories;

use App\Models\Proceeding;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProceedingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */

    protected $model = Proceeding::class;
    private static $index = 0;

    public function definition()
    {
        // Obtener IDs generados de PeopleNaturalFactory
        $peopleID = \App\Models\Person::orderBy('per_id')->pluck('per_id')->toArray();
        // Obtener el ID actual en el orden
        $currentID = $peopleID[self::$index];

        // Incrementar el índice para el próximo uso
        self::$index++;

        // Datos generales del expediente
        $expediente = [
            'exp_numero' => $this->faker->unique()->numerify('####-####-#-####-JM-LA-##'),
            'exp_fecha_inicio' => $this->faker->dateTimeBetween('2020-01-01', '2023-01-01'),
            'exp_pretencion' => $this->faker->numberBetween(1, 19),
            'exp_materia' => $this->faker->numberBetween(1, 9),
            'exp_dis_judicial' => $this->faker->numberBetween(1, 34),
            'exp_instancia' => $this->faker->numberBetween(1, 4),
            'exp_especialidad' => $this->faker->numberBetween(1, 6),
            'exp_monto_pretencion' => $this->faker->numberBetween(5000, 50000),
            'exp_juzgado' => $this->faker->numberBetween(1, 78),
            'exp_estado_proceso' =>$this->faker->randomElement(['EN TRAMITE', 'EN EJECUCION']),
            'exp_demandante' => $currentID,
            'exp_juzgado' => $this->faker->numberBetween(1,78),
            'abo_id' => $this->faker->numberBetween(1,6),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $expediente;
    }
}
