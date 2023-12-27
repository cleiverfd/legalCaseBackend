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
public function convertirMinusculasTildadasAMayusculas($cadena) {
        $minusculas = ['á', 'é', 'í', 'ó', 'ú', 'ü'];
        $mayusculas = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü'];
        return str_replace($minusculas, $mayusculas, mb_strtoupper($cadena, 'UTF-8'));
}
public function index(Request $request)
{
    try {
        \DB::beginTransaction();

        $file = $request->file('excel');
        $data = Excel::toArray([], $file)[0];
        for ($i = 1; $i < count($data); $i++) {
             $row = $data[$i];
             $nombredistrito=trim($row[5]);
             $distrito = \App\Models\JudicialDistrict::where('judis_nombre', $nombredistrito)->first();

             $nombreinstancia=(trim($row[6]));
             $instancia = \App\Models\Instance::

             where('ins_nombre',$nombreinstancia)->first();
             $nombreespecialidad=trim($row[7]);
             $especialidad = \App\Models\Specialty::
             where('esp_nombre',$nombreespecialidad)->first();

             $nombrejuzgado=trim($row[8]);
             $juzgado= \App\Models\Court::where('judis_id',$distrito->judis_id)
             ->where('co_nombre',$nombrejuzgado)->first();

             $nombrepre=trim($row[2]);
             $pretension= \App\Models\Claim::where('pre_nombre',$nombrepre)
             ->first();

             $nombremateria=trim($row[4]);
             $materia= \App\Models\Subject::
             where('mat_nombre',$nombremateria)->first();
             
            $nombreabogado=trim($row[23]);
            $user=\App\Models\User::
            where('name',$nombreabogado)->first();
            $abogado=\App\Models\Lawyer::
            where('per_id',$user->per_id)->first();

            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($row[0])),
                'exp_fecha_inicio' => Carbon::parse($row[1])->format('Y-m-d'),
                'exp_materia' =>$materia->mat_id,
                'exp_pretencion' =>$pretension->pre_id,
                'exp_monto_pretencion' => strtoupper(trim($row[3])),
                'exp_estado_proceso' =>strtoupper(trim($row[24])),
                'exp_especialidad' =>$especialidad->esp_id,
                'exp_dis_judicial' => $distrito->judis_id,
                'exp_instancia' => $instancia->ins_id,
                'exp_juzgado' =>$juzgado->co_id,
                'multiple' =>0,
                'abo_id' => $abogado->abo_id
            ]);
            $persona = null;
            $direccion = null;
            $procesal = null;
            $tipo=null;

            // Verificar si la persona ya existe
            if ($row[11]!=null) {
                $persona = \App\Models\Person::updateOrCreate(
                    ['nat_dni' =>trim($row[11])],
                    [
                        'nat_apellido_paterno' =>  strtoupper(trim($row[12])),
                        'nat_apellido_materno' =>  strtoupper(trim($row[13])),
                        'nat_nombres' =>  strtoupper(trim($row[14])),
                        'nat_telefono' =>  strtoupper(trim($row[17])),
                        'nat_correo' => trim($row[18])==null ? trim($row[11]):trim($row[18]),
                        'per_condicion'=>strtoupper(trim($row[10]))
                    ]
                );
                 $tipo='NATURAL';
            } else {
                $persona = \App\Models\Person::updateOrCreate(
                    ['jur_ruc' => strtoupper(trim($row[15]))],
                    [
                        'jur_razon_social' => strtoupper(trim($row[16])),
                        'jur_telefono' =>  strtoupper(trim($row[17])),
                        'jur_correo' =>  trim($row[18])==null ? trim($row[15]):trim($row[18]),
                        'jur_rep_legal' => '-',
                        'per_condicion'=>strtoupper(trim($row[10]))
                    ]
                );
                $tipo='JURIDICA';
            }
            
            $procesal = \App\Models\Procesal::Create(
                [
                    'per_id'=> $persona->per_id,
                    'exp_id'=>$exp->exp_id,
                    'tipo_procesal'=> strtoupper(trim($row[9])),
                    'tipo_persona'=>$tipo
                ]
            );

            $nombredepartamento = trim($row[19]);
            if ($nombredepartamento) {
                $departamento = \App\Models\Department::where('dep_nombre', $nombredepartamento)->first();
                $nombreprovincia = trim($row[20]);
                $provincia = null;
                $distrito = null;

                if ($departamento) {
                    $provincia = \App\Models\Province::where('dep_id', $departamento->dep_id)
                        ->where('pro_nombre', $nombreprovincia)->first();
                    if ($provincia) {
                        $nombredistrito = trim($row[21]);
                        $distrito = \App\Models\District::where('pro_id', $provincia->pro_id)
                            ->where('dis_nombre', $nombredistrito)->first();
                    }
                }
            }

            $direccion = \App\Models\Address::updateOrCreate(
                ['proc_id' => $procesal->proc_id],
                [
                    'dir_calle_av' => trim($row[22]),
                    'dis_id' => $distrito ? $distrito->dis_id : 250402,
                    'pro_id' => $provincia ? $provincia->pro_id : 2505,
                    'dep_id' => $departamento ? $departamento->dep_id : 26,
                ]
            );

             $abogado = \App\Models\Lawyer::find($abogado ->abo_id);
             $abogado->abo_disponibilidad = 'OCUPADO';
             $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
             $abogado->save();
        }
        \DB::commit();
        return response()->json(['state' => 0, 'data' => $data], 200);
    } catch (Exception $e) {
        \DB::rollback();
          return response()->json(['state' => 1, 'exception' => (string) $e]);
    }
}

}
