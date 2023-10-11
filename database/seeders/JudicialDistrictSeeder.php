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
        // Nombres de los distritos
        $distritosJudiciales = [
            'AMAZONAS',
            'ANCASH',
            'APURIMAC',
            'AREQUIPA',
            'AYACUCHO',
            'CAJAMARCA',
            'CALLAO',
            'CAÑETE',
            'CUSCO',
            'DEL SANTA',
            'HUANCAVELICA',
            'HUANUCO',
            'HUAURA',
            'ICA',
            'JUNIN',
            'LA LIBERTAD',
            'LAMBAYEQUE',
            'LIMA',
            'LIMA ESTE',
            'LIMA NORTE',
            'LIMA SUR',
            'LORETO',
            'MADRE DE DIOS',
            'MOQUEGUA',
            'PASCO',
            'PIURA',
            'PUNO',
            'SAN MARTIN',
            'SELVA CENTRAL',
            'SULLANA',
            'TACNA',
            'TUMBES',
            'UCAYALI',
            'VENTANILLA - LIMA NOROESTE'
        ];

        foreach ($distritosJudiciales as $distritoJudicial) {
            JudicialDistrict::create([
                'judis_nombre' => $distritoJudicial,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
