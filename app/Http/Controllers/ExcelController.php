<?php

namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExcelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
public function index(Request $request)
{
    try {
        \DB::beginTransaction();

        $file = $request->file('excel');
        $data = Excel::toArray([], $file)[0];
        for ($i = 1; $i < count($data); $i++) {
             $row = $data[$i];
            //buscar la especialidad a travez del distrito judicial
             $nombredistrito=strtoupper(trim($row[3]));
             $distrito = \App\Models\JudicialDistrict::where('judis_nombre', $nombredistrito)->first();
             $nombreinstancia=strtoupper(trim($row[4]));
             $instancia = \App\Models\Instance::where('judis_id', $distrito->judis_id)
             ->where('ins_nombre',$nombreinstancia)->first();
             $nombreespecialidad=strtoupper(trim($row[5]));
             $especialidad = \App\Models\Specialty::where('ins_id', $instancia->ins_id)
             ->where('esp_nombre',$nombreespecialidad)->first();
            
            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($row[0])),
                'exp_fecha_inicio' => Carbon::parse($row[1])->format('Y-m-d'),
                'exp_materia' => strtoupper(trim($row[2])),
                'exp_pretencion' =>strtoupper(trim($row[6])),
                'exp_monto_pretencion' => strtoupper(trim($row[7])),
                'exp_estado_proceso' => 'EN TRAMITE',
                'exp_juzgado' => strtoupper(trim($row[8])),
                'exp_especialidad' =>$especialidad->esp_id,
            ]);
            $persona = null;
            $direccion = null;
            $per = null;

            // Verificar si la persona ya existe
            if ($row[10]!=null) {
                $persona = \App\Models\PeopleNatural::updateOrCreate(
                    ['nat_dni' =>trim($row[10])],
                    [
                        'nat_apellido_paterno' => strtoupper(trim($row[11])),
                        'nat_apellido_materno' => strtoupper(trim($row[12])),
                        'nat_nombres' => strtoupper(trim($row[13])),
                        'nat_telefono' => strtoupper(trim($row[16])),
                        'nat_correo' => trim($row[17])
                    ]
                );

                $per = \App\Models\Person::updateOrCreate(
                    ['nat_id' => $persona->nat_id],
                    []
                );
            } else {
                $persona = \App\Models\PeopleJuridic::updateOrCreate(
                    ['jur_ruc' => strtoupper(trim($row[14]))],
                    [
                        'jur_razon_social' => strtoupper(trim($row[15])),
                        'jur_telefono' => strtoupper(trim($row[16])),
                        'jur_correo' => trim($row[17]),
                        'jur_rep_legal' => '-',
                    ]
                );

                $per = \App\Models\Person::updateOrCreate(
                    ['jur_id' => $persona->jur_id],
                    []
                );
            }
            // Obtener el ID de la persona
            $perId = $per->per_id;
            // Dirección del demandante o demandado
            $nombredepartamento=trim($row[18]);
            $departamento= \App\Models\Department::where('dep_nombre', $nombredepartamento)->first();
            $nombreprovincia=trim($row[19]);
            $provincia= \App\Models\Province::where('dep_id', $departamento->dep_id)
            ->where('pro_nombre',$nombreprovincia )->first();
            $nombredistrito=trim($row[20]);
            $distrito= \App\Models\District::where('pro_id', $provincia->pro_id)
            ->where('dis_nombre',$nombredistrito)->first();

            $direccion = \App\Models\Address::updateOrCreate(
                ['per_id' => $perId],
                [
                    'dir_calle_av' => trim($row[21]),
                    'dis_id' =>$distrito->dis_id,
                    'pro_id' =>$provincia->pro_id,
                    'dep_id' =>$departamento->dep_id,
                ]
            );
              // traer  id de abogado
            $personaabodado= \App\Models\PeopleNatural::where('nat_dni',$row[22])->first();
            $aboId = \App\Models\Lawyer::where('nat_id', $personaabodado->nat_id)->first();
             /*Actulizar el expediente  asignando la persona y el abogado*/
             $EX = \App\Models\Proceeding::find($exp->exp_id);
             if('DEMANDANTE'==$row[9]){
             $EX->exp_demandante = strtoupper(trim($perId));}
             else{
             $EX->exp_demandado=strtoupper(trim($perId));
             }
             $EX->abo_id =  $aboId->abo_id;
             $EX->save();
             /*ACTULIZAR ESTADO DE ABOGADO */
             $abogado = \App\Models\Lawyer::find($aboId ->abo_id);
             $abogado->abo_disponibilidad = 'OCUPADO';
             $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
             $abogado->save();
        }
        \DB::commit();
        return response()->json(['state' => 0, 'data' => $data], 200);
    } catch (Exception $e) {
        \DB::rollback();
        return ['state' => '1', 'exception' => (string) $e];
    }
}

}
