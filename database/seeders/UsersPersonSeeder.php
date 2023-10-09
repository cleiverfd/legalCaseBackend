<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UsersPersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inserta la primera persona natural manualmente
        DB::table('people_naturals')->insert([
            'nat_dni' => 77176517,
            'nat_nombres' => 'Cleiver',
            'nat_apellido_paterno' => 'Fernandez',
            'nat_apellido_materno' => 'Delgado',
            'nat_telefono' => '+51 980404074', // Asegúrate de incluir el prefijo internacional para Perú
            'nat_correo' => 'cleiverfd@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta un registro en la tabla users relacionado con la primera persona natural
        DB::table('users')->insert([
            'name' => 'Cleiver',
            'email' => 'cleiverfd@gmail.com',
            'usu_rol' => 'ADMIN', // Cambia esto si el rol es diferente
            'per_id' => 1, // El ID de la primera persona natural manualmente ingresada
            'password' => Hash::make('77176517'), // Usando el DNI como contraseña
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta la segunda persona natural manualmente
        DB::table('people_naturals')->insert([
            'nat_dni' => 65432190,
            'nat_nombres' => 'Jonatan',
            'nat_apellido_paterno' => 'Mayanga',
            'nat_apellido_materno' => 'Muñoz',
            'nat_telefono' => '+51 956342789', // Asegúrate de incluir el prefijo internacional para Perú
            'nat_correo' => 'jonatanm@gmail.com',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Inserta un registro en la tabla users relacionado con la segunda persona natural
        DB::table('users')->insert([
            'name' => 'Jonatan',
            'email' => 'jonatanm@gmail.com',
            'usu_rol' => 'ADMIN', // Cambia esto si el rol es diferente
            'per_id' => 2, // El ID de la segunda persona natural manualmente ingresada
            'password' => Hash::make('65432190'), // Usando el DNI como contraseña
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // // Genera registros faker para usuarios adicionales
        // $faker = \Faker\Factory::create();
        // foreach (range(1, 5) as $index) {
        //     $dni = $faker->unique()->numberBetween(10000000, 99999999);
        //     $nombres = $faker->firstName;
        //     $apPaterno = $faker->lastName;
        //     $apMaterno = $faker->lastName;

        //     // Genera un correo electrónico con el formato [apellido_paterno]@gmail.com
        //     $correo = strtolower($apPaterno) . '@gmail.com';

        //     // Genera un número de teléfono válido de Perú
        //     $telefono = '+51 ' . $faker->numberBetween(900000000, 999999999);

        //     $password = Hash::make($dni);

        //     // Inserta un registro en la tabla people_naturals
        //     DB::table('people_naturals')->insert([
        //         'nat_dni' => $dni,
        //         'nat_nombres' => $nombres,
        //         'nat_apellido_paterno' => $apPaterno,
        //         'nat_apellido_materno' => $apMaterno,
        //         'nat_telefono' => $telefono,
        //         'nat_correo' => $correo,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ]);

        //     // Inserta un registro en la tabla users relacionado con la people_naturals
        //     DB::table('users')->insert([
        //         'name' => $nombres,
        //         'email' => $correo,
        //         'usu_rol' => 'ABOGADO', // Cambia esto si el rol es diferente
        //         'per_id' => $index + 2, // Suponiendo que per_id es autoincremental y hemos agregado dos registros manualmente
        //         'password' => $password,
        //         'created_at' => Carbon::now(),
        //         'updated_at' => Carbon::now(),
        //     ]);
        // }
    }
}
