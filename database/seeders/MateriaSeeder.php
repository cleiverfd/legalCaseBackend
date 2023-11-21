<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $pretensiones = [
            ['mat_id' => '01', 'mat_nombre' => 'Proceso de Amparo'],
            ['mat_id' => '02', 'mat_nombre' => 'Proceso de Cumplimiento'],
            ['mat_id' => '03', 'mat_nombre' => 'Proceso de Habeas Data'],
            ['mat_id' => '04', 'mat_nombre' => 'Civil'],
            ['mat_id' => '05', 'mat_nombre' => 'Laboral'],
            ['mat_id' => '06', 'mat_nombre' => 'Laboral Contencioso Administrativo'],
            ['mat_id' => '07', 'mat_nombre' => 'Penal'],
            ['mat_id' => '08', 'mat_nombre' => 'Arbitraje'],
            ['mat_id' => '09', 'mat_nombre' => 'Otros'],
        ];

        $currentTimestamp = Carbon::now();
        foreach($pretensiones as &$pretension){
            $pretension['mat_nombre'] = mb_strtoupper($pretension['mat_nombre']);
            $pretension['created_at'] = $currentTimestamp;
            $pretension['updated_at'] = $currentTimestamp;
        }
        
        DB::table('subjects')->insert($pretensiones);
    }
}
