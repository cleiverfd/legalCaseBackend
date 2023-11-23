<?php

namespace Database\Seeders;

use App\Models\JudicialDistrict;

use Illuminate\Database\Seeder;

class JudicialDistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $distritosJudiciales = [
            // ['judis_nombre' => 'AMAZONAS'],
            // ['judis_nombre' => 'ANCASH'],
            // ['judis_nombre' => 'APURIMAC'],
            // ['judis_nombre' => 'AREQUIPA'],
            // ['judis_nombre' => 'AYACUCHO'],
            // ['judis_nombre' => 'CAJAMARCA'],
            // ['judis_nombre' => 'CALLAO'],
            // ['judis_nombre' => 'CAÑETE'],
            // ['judis_nombre' => 'CUSCO'],
            // ['judis_nombre' => 'DEL SANTA'],
            // ['judis_nombre' => 'HUANCAVELICA'],
            // ['judis_nombre' => 'HUANUCO'],
            // ['judis_nombre' => 'HUAURA'],
            // ['judis_nombre' => 'ICA'],
            // ['judis_nombre' => 'JUNIN'],
            // ['judis_nombre' => 'LA LIBERTAD'],
            // ['judis_nombre' => 'LAMBAYEQUE'],
            // ['judis_nombre' => 'LIMA'],
            // ['judis_nombre' => 'LIMA ESTE'],
            // ['judis_nombre' => 'LIMA NORTE'],
            // ['judis_nombre' => 'LIMA SUR'],
            // ['judis_nombre' => 'LORETO'],
            // ['judis_nombre' => 'MADRE DE DIOS'],
            // ['judis_nombre' => 'MOQUEGUA'],
            // ['judis_nombre' => 'PASCO'],
            // ['judis_nombre' => 'PIURA'],
            // ['judis_nombre' => 'PUNO'],
            // ['judis_nombre' => 'SAN MARTIN'],
            // ['judis_nombre' => 'SELVA CENTRAL'],
            // ['judis_nombre' => 'SULLANA'],
            // ['judis_nombre' => 'TACNA'],
            // ['judis_nombre' => 'TUMBES'],
            // ['judis_nombre' => 'UCAYALI'],
            // ['judis_nombre' => 'VENTANILLA - LIMA NOROESTE']
            ['judis_nombre' => 'CAJAMARCA'],
            ['judis_nombre' => 'LA LIBERTAD'],
            ['judis_nombre' => 'LAMBAYEQUE'],
            ['judis_nombre' => 'LIMA'],
            ['judis_nombre' => 'PIURA'],
            ['judis_nombre' => 'SAN MARTIN']
        ];

        foreach ($distritosJudiciales as $distritoJudicial) {
            JudicialDistrict::create([
                'judis_nombre' => $distritoJudicial['judis_nombre'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
