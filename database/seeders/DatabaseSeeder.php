<?php

namespace Database\Seeders;

use App\Models\JudicialDistrict;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UsersPersonSeeder::class);
        $this->call(AbogadoSeeder::class);
        $this->call(DepartmentSeeder::class);
        $this->call(ProvincesSeeder::class);
        $this->call(DistricSeeder::class);
        $this->call(JudicialDistrictSeeder::class);
        $this->call(InstanceSeeder::class);
        $this->call(EspecialidadSeeder::class);
    }
}
