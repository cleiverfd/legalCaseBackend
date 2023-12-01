<?php

namespace Database\Factories;

use App\Models\Alert;
use Illuminate\Database\Eloquent\Factories\Factory;

class AlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Alert::class;

    public function definition()
    {
        // Generar una fecha aleatoria en el futuro
        // $fechaVencimiento = $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d');
        // $fechaVencimiento = $this->faker->dateTimeBetween('+1 month', '+2 months')->format('Y-m-d');
        $fechaVencimiento = $this->faker->dateTimeBetween(now()->addMonth(), now()->addMonths(2))->format('Y-m-d');

        $descripcion = $this->faker->sentence;

        static $index = 0;
        $ID = \App\Models\Proceeding::pluck('exp_id')->toArray();
        
        // Obtener un ID de expediente asegurándose de no exceder el índice
        $expId = isset($ID[$index]) ? $ID[$index] : $this->faker->numberBetween(1, 100);

        // Incrementar el índice para el próximo uso
        $index++;

        $auFecha = strtoupper(trim($fechaVencimiento));
        $hoy = now();

        $diasFaltantes = (new \DateTime($auFecha))->diff(new \DateTime($hoy))->days;

        return [
            'ale_fecha_vencimiento' => $fechaVencimiento,
            'ale_descripcion' => $descripcion,
            'exp_id' => $expId,
            'ale_dias_faltantes' => $diasFaltantes,
        ];
    }
}
