<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AbogadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 10; $i++) {
            // Datos ficticios para el abogado
            $apellidoPaterno = $faker->lastName;
            $datosAbogado = [
                'nat_dni' => '1' . str_pad($faker->unique()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
                'nat_apellido_paterno' => $apellidoPaterno,
                'nat_apellido_materno' => $faker->lastName,
                'nat_nombres' => $faker->firstName,
                'nat_correo' => strtolower($apellidoPaterno) . '@gmail.com',
                'nat_telefono' => '+51 ' . $faker->numberBetween(900000000, 999999999),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Inserta los datos en la tabla persona_natural
            $idPersonaNatural = DB::table('people_naturals')->insertGetId($datosAbogado);

            // Datos para el usuario
            $datosUsuario = [
                'name' => explode(' ', $datosAbogado['nat_nombres'])[0], // Tomamos el primer nombre
                'email' => $datosAbogado['nat_correo'],
                'usu_rol' => 'ABOGADO',
                'per_id' => $idPersonaNatural,
                'email_verified_at' => now(),
                'password' => Hash::make($datosAbogado['nat_dni']),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Inserta los datos en la tabla users
            DB::table('users')->insert($datosUsuario);

            // Datos para la tabla abogados
            $datosAbogadoTabla = [
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'nat_id' => $idPersonaNatural,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Inserta los datos en la tabla abogados
            DB::table('lawyers')->insert($datosAbogadoTabla);
        }
    }
}
