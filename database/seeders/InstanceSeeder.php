<?php

namespace Database\Seeders;

use App\Models\Instance;

use Illuminate\Database\Seeder;

class InstanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $instancias = [
            'JUZGADO DE PAZ LETRADO',
            'JUZGADO ESPECIALIZADO',
            'JUZGADO MIXTO',
            'SALA SUPERIOR',
        ];

        // Itera sobre el array y crea registros en la tabla 'instances'
        foreach ($instancias as $instancia) {
            Instance::create([
                'ins_nombre' => $instancia,
                'judis_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
