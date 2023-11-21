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
            ['co_id' => '01', 'co_nombre' => '8 JUZGADO'],
            ['co_id' => '02', 'co_nombre' => '9 JUZGADO'],
            ['co_id' => '03', 'co_nombre' => '10 JUZGADO'],
           
        ];

        $currentTimestamp = Carbon::now();
        foreach($pretensiones as &$pretension){
            $pretension['co_nombre'] = mb_strtoupper($pretension['co_nombre']);
            $pretension['created_at'] = $currentTimestamp;
            $pretension['updated_at'] = $currentTimestamp;
        }
        
        DB::table('courts')->insert($pretensiones);
    }
}
