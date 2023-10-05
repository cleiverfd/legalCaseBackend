<?php

namespace Database\Seeders;

use App\Models\District;
use Carbon\Carbon;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistricSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = [
            ['dis_nombre' => 'Chiclayo', 'pro_id' => '125'],
            ['dis_nombre' => 'Chongoyape', 'pro_id' => '125'],
            ['dis_nombre' => 'Eten', 'pro_id' => '125'],
            ['dis_nombre' => 'Eten Puerto', 'pro_id' => '125'],
            ['dis_nombre' => 'José Leonardo Ortiz', 'pro_id' => '125'],
            ['dis_nombre' => 'La Victoria', 'pro_id' => '125'],
            ['dis_nombre' => 'Lagunas', 'pro_id' => '125'],
            ['dis_nombre' => 'Monsefu', 'pro_id' => '125'],
            ['dis_nombre' => 'Nueva Arica', 'pro_id' => '125'],
            ['dis_nombre' => 'Oyotun', 'pro_id' => '125'],
            ['dis_nombre' => 'Picsi', 'pro_id' => '125'],
            ['dis_nombre' => 'Pimentel', 'pro_id' => '125'],
            ['dis_nombre' => 'Reque', 'pro_id' => '125'],
            ['dis_nombre' => 'Santa Rosa', 'pro_id' => '125'],
            ['dis_nombre' => 'Saña', 'pro_id' => '125'],
            ['dis_nombre' => 'Cayalti', 'pro_id' => '125'],
            ['dis_nombre' => 'Patapo', 'pro_id' => '125'],
            ['dis_nombre' => 'Pomalca', 'pro_id' => '125'],
            ['dis_nombre' => 'Pucala', 'pro_id' => '125'],
            ['dis_nombre' => 'Tuman', 'pro_id' => '125'],
            ['dis_nombre' => 'Ferreñafe', 'pro_id' => '125'],
            ['dis_nombre' => 'Cañaris', 'pro_id' => '125'],
            ['dis_nombre' => 'Incahuasi', 'pro_id' => '125'],
            ['dis_nombre' => 'Manuel Antonio Mesones Muro', 'pro_id' => '125'],
            ['dis_nombre' => 'Pitipo', 'pro_id' => '125'],
            ['dis_nombre' => 'Pueblo Nuevo', 'pro_id' => '125'],
            ['dis_nombre' => 'Lambayeque', 'pro_id' => '125'],
            ['dis_nombre' => 'Chochope', 'pro_id' => '125'],
            ['dis_nombre' => 'Illimo', 'pro_id' => '125'],
            ['dis_nombre' => 'Jayanca', 'pro_id' => '125'],
            ['dis_nombre' => 'Mochumi', 'pro_id' => '125'],
            ['dis_nombre' => 'Morrope', 'pro_id' => '125'],
            ['dis_nombre' => 'Motupe', 'pro_id' => '125'],
            ['dis_nombre' => 'Olmos', 'pro_id' => '125'],
            ['dis_nombre' => 'Pacora', 'pro_id' => '125'],
            ['dis_nombre' => 'Salas', 'pro_id' => '125'],
            ['dis_nombre' => 'San José', 'pro_id' => '125'],
            ['dis_nombre' => 'Tucume', 'pro_id' => '125']
        ];

        $currentTimestamp = Carbon::now();

        foreach ($districts as &$district) {
            $district['created_at'] = $currentTimestamp;
            $district['updated_at'] = $currentTimestamp;
        }

        DB::table('districts')->insert($districts);
    }
}
