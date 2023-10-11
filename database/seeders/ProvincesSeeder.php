<?php

namespace Database\Seeders;

use App\Models\Province;
use Carbon\Carbon;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $provinces = [
            ['pro_nombre' => 'Chachapoyas', 'dep_id' => '1'],
            ['pro_nombre' => 'Bagua', 'dep_id' => '1'],
            ['pro_nombre' => 'Bongará', 'dep_id' => '1'],
            ['pro_nombre' => 'Condorcanqui', 'dep_id' => '1'],
            ['pro_nombre' => 'Luya', 'dep_id' => '1'],
            ['pro_nombre' => 'Rodríguez de Mendoza', 'dep_id' => '1'],
            ['pro_nombre' => 'Utcubamba', 'dep_id' => '1'],
            ['pro_nombre' => 'Huaraz', 'dep_id' => '2'],
            ['pro_nombre' => 'Aija', 'dep_id' => '2'],
            ['pro_nombre' => 'Antonio Raymondi', 'dep_id' => '2'],
            ['pro_nombre' => 'Asunción', 'dep_id' => '2'],
            ['pro_nombre' => 'Bolognesi', 'dep_id' => '2'],
            ['pro_nombre' => 'Carhuaz', 'dep_id' => '2'],
            ['pro_nombre' => 'Carlos Fermín Fitzcarrald', 'dep_id' => '2'],
            ['pro_nombre' => 'Casma', 'dep_id' => '2'],
            ['pro_nombre' => 'Corongo', 'dep_id' => '2'],
            ['pro_nombre' => 'Huari', 'dep_id' => '2'],
            ['pro_nombre' => 'Huarmey', 'dep_id' => '2'],
            ['pro_nombre' => 'Huaylas', 'dep_id' => '2'],
            ['pro_nombre' => 'Mariscal Luzuriaga', 'dep_id' => '2'],
            ['pro_nombre' => 'Ocros', 'dep_id' => '2'],
            ['pro_nombre' => 'Pallasca', 'dep_id' => '2'],
            ['pro_nombre' => 'Pomabamba', 'dep_id' => '2'],
            ['pro_nombre' => 'Recuay', 'dep_id' => '2'],
            ['pro_nombre' => 'Santa', 'dep_id' => '2'],
            ['pro_nombre' => 'Sihuas', 'dep_id' => '2'],
            ['pro_nombre' => 'Yungay', 'dep_id' => '2'],
            ['pro_nombre' => 'Abancay', 'dep_id' => '3'],
            ['pro_nombre' => 'Andahuaylas', 'dep_id' => '3'],
            ['pro_nombre' => 'Antabamba', 'dep_id' => '3'],
            ['pro_nombre' => 'Aymaraes', 'dep_id' => '3'],
            ['pro_nombre' => 'Cotabambas', 'dep_id' => '3'],
            ['pro_nombre' => 'Chincheros', 'dep_id' => '3'],
            ['pro_nombre' => 'Grau', 'dep_id' => '3'],
            ['pro_nombre' => 'Arequipa', 'dep_id' => '4'],
            ['pro_nombre' => 'Camaná', 'dep_id' => '4'],
            ['pro_nombre' => 'Caravelí', 'dep_id' => '4'],
            ['pro_nombre' => 'Castilla', 'dep_id' => '4'],
            ['pro_nombre' => 'Caylloma', 'dep_id' => '4'],
            ['pro_nombre' => 'Condesuyos', 'dep_id' => '4'],
            ['pro_nombre' => 'Islay', 'dep_id' => '4'],
            ['pro_nombre' => 'La Unión', 'dep_id' => '4'],
            ['pro_nombre' => 'Huamanga', 'dep_id' => '5'],
            ['pro_nombre' => 'Cangallo', 'dep_id' => '5'],
            ['pro_nombre' => 'Huanca Sancos', 'dep_id' => '5'],
            ['pro_nombre' => 'Huanta', 'dep_id' => '5'],
            ['pro_nombre' => 'La Mar', 'dep_id' => '5'],
            ['pro_nombre' => 'Lucanas', 'dep_id' => '5'],
            ['pro_nombre' => 'Parinacochas', 'dep_id' => '5'],
            ['pro_nombre' => 'Pàucar del Sara Sara', 'dep_id' => '5'],
            ['pro_nombre' => 'Sucre', 'dep_id' => '5'],
            ['pro_nombre' => 'Víctor Fajardo', 'dep_id' => '5'],
            ['pro_nombre' => 'Vilcas Huamán', 'dep_id' => '5'],
            ['pro_nombre' => 'Cajamarca', 'dep_id' => '6'],
            ['pro_nombre' => 'Cajabamba', 'dep_id' => '6'],
            ['pro_nombre' => 'Celendín', 'dep_id' => '6'],
            ['pro_nombre' => 'Chota', 'dep_id' => '6'],
            ['pro_nombre' => 'Contumazá', 'dep_id' => '6'],
            ['pro_nombre' => 'Cutervo', 'dep_id' => '6'],
            ['pro_nombre' => 'Hualgayoc', 'dep_id' => '6'],
            ['pro_nombre' => 'Jaén', 'dep_id' => '6'],
            ['pro_nombre' => 'San Ignacio', 'dep_id' => '6'],
            ['pro_nombre' => 'San Marcos', 'dep_id' => '6'],
            ['pro_nombre' => 'San Miguel', 'dep_id' => '6'],
            ['pro_nombre' => 'San Pablo', 'dep_id' => '6'],
            ['pro_nombre' => 'Santa Cruz', 'dep_id' => '6'],
            ['pro_nombre' => 'Prov. Const. del Callao', 'dep_id' => '7'],
            ['pro_nombre' => 'Cusco', 'dep_id' => '8'],
            ['pro_nombre' => 'Acomayo', 'dep_id' => '8'],
            ['pro_nombre' => 'Anta', 'dep_id' => '8'],
            ['pro_nombre' => 'Calca', 'dep_id' => '8'],
            ['pro_nombre' => 'Canas', 'dep_id' => '8'],
            ['pro_nombre' => 'Canchis', 'dep_id' => '8'],
            ['pro_nombre' => 'Chumbivilcas', 'dep_id' => '8'],
            ['pro_nombre' => 'Espinar', 'dep_id' => '8'],
            ['pro_nombre' => 'La Convención', 'dep_id' => '8'],
            ['pro_nombre' => 'Paruro', 'dep_id' => '8'],
            ['pro_nombre' => 'Paucartambo', 'dep_id' => '8'],
            ['pro_nombre' => 'Quispicanchi', 'dep_id' => '8'],
            ['pro_nombre' => 'Urubamba', 'dep_id' => '8'],
            ['pro_nombre' => 'Huancavelica', 'dep_id' => '9'],
            ['pro_nombre' => 'Acobamba', 'dep_id' => '9'],
            ['pro_nombre' => 'Angaraes', 'dep_id' => '9'],
            ['pro_nombre' => 'Castrovirreyna', 'dep_id' => '9'],
            ['pro_nombre' => 'Churcampa', 'dep_id' => '9'],
            ['pro_nombre' => 'Huaytará', 'dep_id' => '9'],
            ['pro_nombre' => 'Tayacaja', 'dep_id' => '9'],
            ['pro_nombre' => 'Huánuco', 'dep_id' => '10'],
            ['pro_nombre' => 'Ambo', 'dep_id' => '10'],
            ['pro_nombre' => 'Dos de Mayo', 'dep_id' => '10'],
            ['pro_nombre' => 'Huacaybamba', 'dep_id' => '10'],
            ['pro_nombre' => 'Huamalíes', 'dep_id' => '10'],
            ['pro_nombre' => 'Leoncio Prado', 'dep_id' => '10'],
            ['pro_nombre' => 'Marañón', 'dep_id' => '10'],
            ['pro_nombre' => 'Pachitea', 'dep_id' => '10'],
            ['pro_nombre' => 'Puerto Inca', 'dep_id' => '10'],
            ['pro_nombre' => 'Lauricocha', 'dep_id' => '10'],
            ['pro_nombre' => 'Yarowilca', 'dep_id' => '10'],
            ['pro_nombre' => 'Ica', 'dep_id' => '11'],
            ['pro_nombre' => 'Chincha', 'dep_id' => '11'],
            ['pro_nombre' => 'Nasca', 'dep_id' => '11'],
            ['pro_nombre' => 'Palpa', 'dep_id' => '11'],
            ['pro_nombre' => 'Pisco', 'dep_id' => '11'],
            ['pro_nombre' => 'Huancayo', 'dep_id' => '12'],
            ['pro_nombre' => 'Concepción', 'dep_id' => '12'],
            ['pro_nombre' => 'Chanchamayo', 'dep_id' => '12'],
            ['pro_nombre' => 'Jauja', 'dep_id' => '12'],
            ['pro_nombre' => 'Junín', 'dep_id' => '12'],
            ['pro_nombre' => 'Satipo', 'dep_id' => '12'],
            ['pro_nombre' => 'Tarma', 'dep_id' => '12'],
            ['pro_nombre' => 'Yauli', 'dep_id' => '12'],
            ['pro_nombre' => 'Chupaca', 'dep_id' => '12'],
            ['pro_nombre' => 'Trujillo', 'dep_id' => '13'],
            ['pro_nombre' => 'Ascope', 'dep_id' => '13'],
            ['pro_nombre' => 'Bolívar', 'dep_id' => '13'],
            ['pro_nombre' => 'Chepén', 'dep_id' => '13'],
            ['pro_nombre' => 'Julcán', 'dep_id' => '13'],
            ['pro_nombre' => 'Otuzco', 'dep_id' => '13'],
            ['pro_nombre' => 'Pacasmayo', 'dep_id' => '13'],
            ['pro_nombre' => 'Pataz', 'dep_id' => '13'],
            ['pro_nombre' => 'Sánchez Carrión', 'dep_id' => '13'],
            ['pro_nombre' => 'Santiago de Chuco', 'dep_id' => '13'],
            ['pro_nombre' => 'Gran Chimú', 'dep_id' => '13'],
            ['pro_nombre' => 'Virú', 'dep_id' => '13'],
            ['pro_nombre' => 'Chiclayo', 'dep_id' => '14'],
            ['pro_nombre' => 'Ferreñafe', 'dep_id' => '14'],
            ['pro_nombre' => 'Lambayeque', 'dep_id' => '14'],
            ['pro_nombre' => 'Lima', 'dep_id' => '15'],
            ['pro_nombre' => 'Barranca', 'dep_id' => '15'],
            ['pro_nombre' => 'Cajatambo', 'dep_id' => '15'],
            ['pro_nombre' => 'Canta', 'dep_id' => '15'],
            ['pro_nombre' => 'Cañete', 'dep_id' => '15'],
            ['pro_nombre' => 'Huaral', 'dep_id' => '15'],
            ['pro_nombre' => 'Huarochirí', 'dep_id' => '15'],
            ['pro_nombre' => 'Huaura', 'dep_id' => '15'],
            ['pro_nombre' => 'Oyón', 'dep_id' => '15'],
            ['pro_nombre' => 'Yauyos', 'dep_id' => '15'],
            ['pro_nombre' => 'Maynas', 'dep_id' => '16'],
            ['pro_nombre' => 'Alto Amazonas', 'dep_id' => '16'],
            ['pro_nombre' => 'Loreto', 'dep_id' => '16'],
            ['pro_nombre' => 'Mariscal Ramón Castilla', 'dep_id' => '16'],
            ['pro_nombre' => 'Requena', 'dep_id' => '16'],
            ['pro_nombre' => 'Ucayali', 'dep_id' => '16'],
            ['pro_nombre' => 'Datem del Marañón', 'dep_id' => '16'],
            ['pro_nombre' => 'Tambopata', 'dep_id' => '17'],
            ['pro_nombre' => 'Manu', 'dep_id' => '17'],
            ['pro_nombre' => 'Tahuamanu', 'dep_id' => '17'],
            ['pro_nombre' => 'Mariscal Nieto', 'dep_id' => '18'],
            ['pro_nombre' => 'General Sánchez Cerro', 'dep_id' => '18'],
            ['pro_nombre' => 'Ilo', 'dep_id' => '18'],
            ['pro_nombre' => 'Pasco', 'dep_id' => '19'],
            ['pro_nombre' => 'Daniel Alcides Carrión', 'dep_id' => '19'],
            ['pro_nombre' => 'Oxapampa', 'dep_id' => '19'],
            ['pro_nombre' => 'Piura', 'dep_id' => '20'],
            ['pro_nombre' => 'Ayabaca', 'dep_id' => '20'],
            ['pro_nombre' => 'Huancabamba', 'dep_id' => '20'],
            ['pro_nombre' => 'Morropón', 'dep_id' => '20'],
            ['pro_nombre' => 'Paita', 'dep_id' => '20'],
            ['pro_nombre' => 'Sullana', 'dep_id' => '20'],
            ['pro_nombre' => 'Talara', 'dep_id' => '20'],
            ['pro_nombre' => 'Sechura', 'dep_id' => '20'],
            ['pro_nombre' => 'Puno', 'dep_id' => '21'],
            ['pro_nombre' => 'Azángaro', 'dep_id' => '21'],
            ['pro_nombre' => 'Carabaya', 'dep_id' => '21'],
            ['pro_nombre' => 'Chucuito', 'dep_id' => '21'],
            ['pro_nombre' => 'El Collao', 'dep_id' => '21'],
            ['pro_nombre' => 'Huancané', 'dep_id' => '21'],
            ['pro_nombre' => 'Lampa', 'dep_id' => '21'],
            ['pro_nombre' => 'Melgar', 'dep_id' => '21'],
            ['pro_nombre' => 'Moho', 'dep_id' => '21'],
            ['pro_nombre' => 'San Antonio de Putina', 'dep_id' => '21'],
            ['pro_nombre' => 'San Román', 'dep_id' => '21'],
            ['pro_nombre' => 'Sandia', 'dep_id' => '21'],
            ['pro_nombre' => 'Yunguyo', 'dep_id' => '21'],
            ['pro_nombre' => 'Moyobamba', 'dep_id' => '22'],
            ['pro_nombre' => 'Bellavista', 'dep_id' => '22'],
            ['pro_nombre' => 'El Dorado', 'dep_id' => '22'],
            ['pro_nombre' => 'Huallaga', 'dep_id' => '22'],
            ['pro_nombre' => 'Lamas', 'dep_id' => '22'],
            ['pro_nombre' => 'Mariscal Cáceres', 'dep_id' => '22'],
            ['pro_nombre' => 'Picota', 'dep_id' => '22'],
            ['pro_nombre' => 'Rioja', 'dep_id' => '22'],
            ['pro_nombre' => 'San Martín', 'dep_id' => '22'],
            ['pro_nombre' => 'Tocache', 'dep_id' => '22'],
            ['pro_nombre' => 'Tacna', 'dep_id' => '23'],
            ['pro_nombre' => 'Candarave', 'dep_id' => '23'],
            ['pro_nombre' => 'Jorge Basadre', 'dep_id' => '23'],
            ['pro_nombre' => 'Tarata', 'dep_id' => '23'],
            ['pro_nombre' => 'Tumbes', 'dep_id' => '24'],
            ['pro_nombre' => 'Contralmirante Villar', 'dep_id' => '24'],
            ['pro_nombre' => 'Zarumilla', 'dep_id' => '24'],
            ['pro_nombre' => 'Coronel Portillo', 'dep_id' => '25'],
            ['pro_nombre' => 'Atalaya', 'dep_id' => '25'],
            ['pro_nombre' => 'Padre Abad', 'dep_id' => '25'],
            ['pro_nombre' => 'Purús', 'dep_id' => '25'],
        ];

        $currentTimestamp = Carbon::now();

        foreach ($provinces as &$province) {
            $province['created_at'] = $currentTimestamp;
            $province['updated_at'] = $currentTimestamp;
        }

        DB::table('provinces')->insert($provinces);
    }
}