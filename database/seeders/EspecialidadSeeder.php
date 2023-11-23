<?php

namespace Database\Seeders;

use App\Models\Especialidad;

use Carbon\Carbon;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $especialidades = [
            ['esp_nombre' => 'CIVIL', 'ins_id' => 1],
            ['esp_nombre' => 'FAMILIA CIVIL', 'ins_id' => 1],
            ['esp_nombre' => 'FAMILIA TUTELAR', 'ins_id' => 1],
            ['esp_nombre' => 'LABORAL', 'ins_id' => 1],
            ['esp_nombre' => 'JUZGADO DE PAZ LETRADO', 'ins_id' =>1],
            ['esp_nombre' => 'CIVIL', 'ins_id' => 2],
            ['esp_nombre' => 'COMERCIAL', 'ins_id' => 2],
            ['esp_nombre' => 'FAMILIA CIVIL', 'ins_id' => 2],
            ['esp_nombre' => 'FAMILIA TUTELAR', 'ins_id' => 2],
            ['esp_nombre' => 'LABORAL', 'ins_id' => 2],
            ['esp_nombre' => 'CIVIL', 'ins_id' => 3],
            ['esp_nombre' => 'FAMILIA CIVIL', 'ins_id' => 3],
            ['esp_nombre' => 'FAMILIA TUTELAR', 'ins_id' => 3],
            ['esp_nombre' => 'LABORAL', 'ins_id' => 3],
            ['esp_nombre' => 'CIVIL', 'ins_id' => 4],
            ['esp_nombre' => 'COMERCIAL', 'ins_id' => 4],
            ['esp_nombre' => 'DERECHO CONSTITUCIONAL', 'ins_id' => 4],
            ['esp_nombre' => 'FAMILIA CIVIL', 'ins_id' => 4],
            ['esp_nombre' => 'FAMILIA TUTELAR', 'ins_id' => 4],
            ['esp_nombre' => 'LABORAL', 'ins_id' => 4],
        ];

        $currentTimestamp = Carbon::now();

        foreach ($especialidades as $especialidad) {
            $district['created_at'] = $currentTimestamp;
            $district['updated_at'] = $currentTimestamp;
        }

        DB::table('specialties')->insert($especialidades);
    }
}
