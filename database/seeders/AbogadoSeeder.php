<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AbogadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datosAbogados = [
            [
                'nat_dni' => '16797179',
                'nat_apellido_paterno' => 'Coronado',
                'nat_apellido_materno' => 'Rioja',
                'nat_nombres' => 'Antonio Lenín',
                'nat_correo' => 'antoniocoronadorioja@gmail.con',
                'nat_telefono' => '957945569',
                'nat_direccion' => 'Av. Santa Rosa 400 - Condominio Los Rosales',
            ],
            [
                'nat_dni' => '47345175',
                'nat_apellido_paterno' => 'Guevara',
                'nat_apellido_materno' => 'Acosta',
                'nat_nombres' => 'Diana Carolina',
                'nat_correo' => 'carolinaguevara91@outlook.com',
                'nat_telefono' => '975943495',
                'nat_direccion' => 'Calle Libertad 841',
            ],
            [
                'nat_dni' => '16794061',
                'nat_apellido_paterno' => 'Rios',
                'nat_apellido_materno' => 'Lora',
                'nat_nombres' => 'Christian Miguel',
                'nat_correo' => 'rioslora.asesor.consultor@gmail.com',
                'nat_telefono' => '991526970',
                'nat_direccion' => 'MZ E LOTE 16 - Urbanización Villas del Norte',
            ],
            [
                'nat_dni' => '16725844',
                'nat_apellido_paterno' => 'Cubas',
                'nat_apellido_materno' => 'Mor',
                'nat_nombres' => 'Manuel Fernando',
                'nat_correo' => 'manuelfernandocubasmori@gmail.com',
                'nat_telefono' => '948417991',
                'nat_direccion' => 'Las Moras 360, 2do Piso, Dpto 201, Urb Los Parques, Chiclayo',
            ],
            [
                'nat_dni' => '45616071',
                'nat_apellido_paterno' => 'Santamaría',
                'nat_apellido_materno' => 'Chapoñan',
                'nat_nombres' => 'Isvin',
                'nat_correo' => null,
                'nat_telefono' => null,
                'nat_direccion' => 'Calle 25 de Noviembre N 200 PJ San Miguel',
            ],
            [
                'nat_dni' => '40640979',
                'nat_apellido_paterno' => 'Fernandez',
                'nat_apellido_materno' => 'Acha',
                'nat_nombres' => 'Omar Simón',
                'nat_correo' => 'omar_082679@hotmail.com',
                'nat_telefono' => '948499363',
                'nat_direccion' => 'Calle Carabaya 1093, PJ Villa El Sol',
            ],
            [
                'nat_dni' => '16799993',
                'nat_apellido_paterno' => 'Torres',
                'nat_apellido_materno' => 'Oballe',
                'nat_nombres' => 'Juan Carlos',
                'nat_correo' => 'juancarlostorresoballe@gmail.com',
                'nat_telefono' => '948569277',
                'nat_direccion' => 'Calle Alfonso Ugarate 721 Int. 401',
            ],
        ];

        foreach ($datosAbogados as $datosAbogado) {
            $datosAbogado['created_at'] = now();
            $datosAbogado['updated_at'] = now();

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
