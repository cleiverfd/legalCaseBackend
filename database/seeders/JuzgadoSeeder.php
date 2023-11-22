<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JuzgadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pretensiones = [
            ["co_nombre" => "1º SALA CIVIL"],
            ["co_nombre" => "2º SALA CIVIL"],
            ["co_nombre" => "1° SALA LABORAL"],
            ["co_nombre" => "2° SALA LABORAL"],
            ["co_nombre" => "3º SALA LABORAL"],
            ["co_nombre" => "SALA MIXTA"],
            ["co_nombre" => "1º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "1º Juzgado Civil de Jaén"],
            ["co_nombre" => "2º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "2º Juzgado Civil de Jaen"],
            ["co_nombre" => "3° Juzgado Civil - Comercial"],
            ["co_nombre" => "4º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "5º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "6º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "7º Juzgado Civil de Chiclayo"],
            ["co_nombre" => "8º Juzgado Civil – Comercial"],
            ["co_nombre" => "Juzgado Civil de Cutervo"],
            ["co_nombre" => "Juzgado Mixto de Lambayeque"],
            ["co_nombre" => "Juzgado Civil de JLO"],
            ["co_nombre" => "Juzgado Mixto de Ferreñafe"],
            ["co_nombre" => "Juzgado Mixto de Motupe"],
            ["co_nombre" => "Juzgado Mixto de San Ignacio"],
            ["co_nombre" => "1° Juzgado de Familia de Jaén"],
            ["co_nombre" => "10° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "11° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "12° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "13° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "14° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "1º Juzgado de Familia de Chiclayo"],
            ["co_nombre" => "2° Juzgado de Familia de Chiclayo"],
            ["co_nombre" => "2° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "3º Juzgado de Familia de Chiclayo"],
            ["co_nombre" => "8° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar  de Chiclayo"],
            ["co_nombre" => "9° Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar"],
            ["co_nombre" => "Juzgado de Familia de Lambayeque"],
            ["co_nombre" => "Juzgado de Familia de JLO"],
            ["co_nombre" => "Juzgado de Familia Subespecializado en Violencia contra la Mujer e Integrantes del Grupo Familiar de Cutervo"],
            ["co_nombre" => "10° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "11° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "1º Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "2º Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "3º Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "4º Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "5° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "6° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "7° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "8° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "9° Juzgado de Trabajo  de Chiclayo"],
            ["co_nombre" => "Juzgado de Trabajo de Lambayeque"],
            ["co_nombre" => "Juzgado Mixto de Oyotun"],
            ["co_nombre" => "1º Juzgado de Paz Letrado de Ferreñafe"],
            ["co_nombre" => "1º Juzgado de Paz Letrado de JLO"],
            ["co_nombre" => "1º Juzgado de Paz Letrado Civil  de Chiclayo"],
            ["co_nombre" => "2º Juzgado de Paz Letrado Civil  de Chiclayo"],
            ["co_nombre" => "1º Juzgado de Paz Letrado de Familia  de Chiclayo"],
            ["co_nombre" => "2º Juzgado de Paz Letrado de Familia  de Chiclayo"],
            ["co_nombre" => "3º Juzgado de Paz Letrado de Familia  de Chiclayo"],
            ["co_nombre" => "1º Juzgado de Paz Letrado Laboral  de Chiclayo"],
            ["co_nombre" => "2º Juzgado de Paz Letrado Laboral  de Chiclayo"],
            ["co_nombre" => "1º Juzgado de Paz Letrado de la Victoria"],
            ["co_nombre" => "1º Juzgado de Paz Letrado de Jaén"],
            ["co_nombre" => "2º Juzgado de Paz Letrado de Jaén"],
            ["co_nombre" => "2º Juzgado de Paz Letrado - Comisaría de JLO"],
            ["co_nombre" => "3° Juzgado de Paz Letrado de Jaén"],
            ["co_nombre" => "Juzgado de Paz Letrado de Monsefú"],
            ["co_nombre" => "Juzgado de Paz Letrado de Tuman"],
            ["co_nombre" => "Juzgado de Paz Letrado de Lambayeque"],
            ["co_nombre" => "Juzgado de Paz Letrado de Motupe"],
            ["co_nombre" => "Juzgado de Paz Letrado de Cutervo"],
            ["co_nombre" => "Juzgado de Paz Letrado de San Ignacio"],
            ["co_nombre" => "Juzgado de Paz Letrado de Oyotun"],
            ["co_nombre" => "Juzgado de Paz Letrado de Pucará"],
            ["co_nombre" => "Juzgado de Paz Letrado de la Coipa"],
            ["co_nombre" => "Juzgado de Paz Letrado de Huarango"],
            ["co_nombre" => "Juzgado de Paz Letrado de Chongoyape"],
            ["co_nombre" => "Juzgado de Paz Letrado Mixto de Illimo"],
            ["co_nombre" => "2º Juzgado de Paz Letrado Comisaría de Ferreñafe"],
            ["co_nombre" => "Juzgado Transitorio Laboral de Chiclayo"],
            ["co_nombre" => "9º Juzgado de Paz Letrado  de Chiclayo"]
        ];

        $currentTimestamp = Carbon::now();
        foreach ($pretensiones as &$pretension) {
            $pretension['co_nombre'] = mb_strtoupper($pretension['co_nombre']);
            $pretension['created_at'] = $currentTimestamp;
            $pretension['updated_at'] = $currentTimestamp;
        }

        DB::table('courts')->insert($pretensiones);
    }
}
