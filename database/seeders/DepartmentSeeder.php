<?php

namespace Database\Seeders;

use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currentTimestamp = Carbon::now();

        $departments = [
            ['dep_nombre' => 'Amazonas', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Áncash', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Apurímac', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Arequipa', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Ayacucho', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Cajamarca', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Callao', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Cusco', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Huancavelica', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Huánuco', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Ica', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Junín', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'La Libertad', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Lambayeque', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Lima', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Loreto', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Madre de Dios', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Moquegua', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Pasco', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Piura', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Puno', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'San Martín', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Tacna', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Tumbes', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
            ['dep_nombre' => 'Ucayali', 'created_at' => $currentTimestamp, 'updated_at' => $currentTimestamp],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
