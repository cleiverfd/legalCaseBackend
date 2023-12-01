<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Audience;
use App\Models\Proceeding;
use App\Models\Court;
use App\Models\Person;

class AudienceFactory extends Factory
{
    protected $model = Audience::class;

    private function generateGoogleMeetLink()
    {
        // Puedes personalizar esta parte del enlace según tus necesidades
        return 'https://meet.google.com/' . $this->faker->unique()->slug;
    }

    public function definition()
    {
        $au_fecha = $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d');
        $au_link = $this->faker->optional(0.2, null, 'https://meet.google.com/' . $this->faker->unique()->slug)->url;

        $au_hora = $this->faker->time();
        $au_detalles = $this->faker->paragraph;
        $dias_faltantes = $this->faker->numberBetween(1, 30);

        static $index = 0;

        $expedientes = \App\Models\Proceeding::pluck('exp_id')->toArray();
        $expediente_ID = $expedientes[$index];
        $juzgado_ID = Proceeding::where('exp_id', $expediente_ID)->value('exp_juzgado');
        $persona_ID = Proceeding::where('exp_id', $expediente_ID)->value('exp_demandante');
        $juzgado_nombre = Court::where('co_id', $juzgado_ID)->value('co_nombre');
    
        $index++;

        
        return [
            'per_id' => $persona_ID, 
            'exp_id' => $expediente_ID, 
            'au_fecha' => $au_fecha,
            'au_link' => $au_link,
            'au_hora' => $au_hora,
            'au_lugar' => $juzgado_nombre,
            'au_detalles' => $au_detalles,
            'au_dias_faltantes' => $dias_faltantes,
        ];
    }
}
