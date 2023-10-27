<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;

class ProceedingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index()
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.juridica', 'person.persona')
            ->get();
    
        $data = $proceedings->map(function ($proceeding) {
            $person = $proceeding->person;
            $tipo_persona = null;
    
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $proceeding->exp_fecha_inicio,
                'pretencion' => ucwords(strtolower($proceeding->exp_pretencion)),
                'materia' => ucwords(strtolower($proceeding->exp_materia)),
                'especialidad' => ucwords(strtolower($proceeding->exp_especialidad)),
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' =>ucwords(strtolower( $proceeding->exp_estado_proceso)),
            ];
            
    
            if ($person) {
                if ($person->nat_id !== null) {
                    $personData = $person->persona;
                    $tipo_persona = 'natural';
                } elseif ($person->jur_id !== null) {
                    $personData = $person->juridica;
                    $tipo_persona = 'juridica';
                }
            }
    
            if ($tipo_persona === 'natural') {
                $personDataArray = [
                    'dni' => $personData->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($personData->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($personData->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($personData->nat_nombres)),
                    'telefono' => $personData->nat_telefono,
                    'correo' => strtolower($personData->nat_correo),
                ];
            } elseif ($tipo_persona === 'juridica') {
                $personDataArray = [
                    'ruc' => ucwords(strtolower($personData->jur_ruc)),
                    'razon_social' => ucwords(strtolower($personData->jur_razon_social)),
                    'telefono' => $personData->jur_telefono,
                    'correo' => strtolower($personData->jur_correo),
                ];
            } else {
                $personDataArray = [];
            }
    
            return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
        });
    
        return response()->json(['data' => $data], 200);
    }
    

    protected function registrarcaso(Request $request)
    {
        try {
            \DB::beginTransaction();

            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($request->exp['exp_numero'])),
                'exp_fecha_inicio' => $request->exp['exp_fecha_inicio'],
                'exp_pretencion' => strtoupper(trim($request->exp['exp_pretencion'])),
                'exp_materia' => strtoupper(trim($request->exp['exp_materia'])),
                'exp_especialidad' => trim($request->exp['exp_especialidad']),
                'exp_monto_pretencion' => trim($request->exp['exp_monto_pretencion']),
                'exp_estado_proceso' => 'EN TRAMITE',
            ]);
            // Inicializar variables
            $persona = null;
            $direccion = null;
            $per = null;

            // Verificar si la persona ya existe
            if ($request->tipopersona == 'NATURAL') {
                $persona = \App\Models\PeopleNatural::updateOrCreate(
                    ['nat_dni' => strtoupper(trim($request->pn['nat_dni']))],
                    [
                        'nat_apellido_paterno' => strtoupper(trim($request->pn['nat_apellido_paterno'])),
                        'nat_apellido_materno' => strtoupper(trim($request->pn['nat_apellido_materno'])),
                        'nat_nombres' => strtoupper(trim($request->pn['nat_nombres'])),
                        'nat_telefono' => strtoupper(trim($request->pn['nat_telefono'])),
                        'nat_correo' => strtoupper(trim($request->pn['nat_correo']))
                    ]
                );

                $per = \App\Models\Person::updateOrCreate(
                    ['nat_id' => $persona->nat_id],
                    []
                );
            } else {
                $persona = \App\Models\PeopleJuridic::updateOrCreate(
                    ['jur_ruc' => strtoupper(trim($request->pj['jur_ruc']))],
                    [
                        'jur_razon_social' => strtoupper(trim($request->pj['jur_razon_social'])),
                        'jur_telefono' => strtoupper(trim($request->pj['jur_telefono'])),
                        'jur_correo' => strtoupper(trim($request->pj['jur_correo'])),
                        'jur_rep_legal' => strtoupper(trim($request->pj['jur_rep_legal'])),
                    ]
                );

                $per = \App\Models\Person::updateOrCreate(
                    ['jur_id' => $persona->jur_id],
                    []
                );
            }
            // Obtener el ID de la persona
            $perId = $per->per_id;

            // Insertar o actualizar dirección
            $direccion = \App\Models\Address::updateOrCreate(
                ['per_id' => $perId],
                [
                    'dir_calle_av' => trim($request->dir['dir_calle_av']),
                    'dis_id' => trim($request->dir['dis_id']),
                    'pro_id' => trim($request->dir['pro_id']),
                    'dep_id' => trim($request->dir['dep_id']),
                ]
            );

            /*Actulizar el expediente  asignando laersona y el abogado*/
            $EX = \App\Models\Proceeding::find($exp->exp_id);
            $EX->exp_demandante = strtoupper(trim($perId));
            $EX->abo_id = $request->abo_id;
            $EX->save();
            /*ACTULIZAR ESTADO DE ABOGADO */
            $abogado = \App\Models\Lawyer::find($request->abo_id);
            $abogado->abo_disponibilidad = 'OCUPADO';
            $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
            $abogado->save();
            \DB::commit();

            return \response()->json(['state' => 0, 'data' => $EX], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function show($id)
    {
        $proceeding = \App\Models\Proceeding::with('person')
            ->with('specialty.instance.judicialdistrict')
            ->find($id);
        
        if (!$proceeding) {
            return response()->json(['error' => 'Expediente no encontrado'], 404);
        }
    
        $person = $proceeding->person;
        $personData = null;
        $tipo_persona = null;
    
        if ($person) {
            if ($person->nat_id !== null) {
                $personData = $this->getNaturalPersonData($person);
                $tipo_persona = 'Natural';
            } elseif ($person->jur_id !== null) {
                $personData = $this->getJuridicalPersonData($person);
                $tipo_persona = 'Juridica';
            }
        }
    
        
        $data = $this->transformProceedingData($proceeding, $personData, $tipo_persona);
        $data['per_id'] = $person ? $person->per_id : null;
      //traer archivos
         $eje=\App\Models\LegalDocument::where('exp_id',$id)
         ->where('doc_tipo','EJE')
         ->get()
         ;
         $escritos=\App\Models\LegalDocument::where('exp_id',$id)
         ->where('doc_tipo','ESCRITO')
         ->get()
         ;
        return response()->json(['data' => $data,'eje'=>$eje,'escritos'=>$escritos], 200);
    }
    
    private function getNaturalPersonData($person)
    {
        return [
            // 'nat_id' => $person->persona->nat_id,
            'nat_dni' => $person->persona->nat_dni,
            'nat_apellido_paterno' => $person->persona->nat_apellido_paterno,
            'nat_apellido_materno' => $person->persona->nat_apellido_materno,
            'nat_nombres' => $person->persona->nat_nombres,
            'nat_telefono' => $person->persona->nat_telefono,
            'nat_correo' => $person->persona->nat_correo,
        ];
    }
    
    private function getJuridicalPersonData($person)
    {
        return [
            // 'jur_id' => $person->juridica->jur_id,
            'jur_ruc' => $person->juridica->jur_ruc,
            'jur_razon_social' => $person->juridica->jur_razon_social,
            'jur_telefono' => $person->juridica->jur_telefono,
            'jur_correo' => $person->juridica->jur_correo,
        ];
    }
    
    private function transformProceedingData($proceeding, $personData, $tipo_persona)
    {
        return array_merge(
            $proceeding->only([
                'exp_id',
                'exp_numero',
                'exp_fecha_inicio',
                'exp_pretencion',
                'exp_materia',
                'exp_especialidad',
                'exp_monto_pretencion',
                'exp_estado_proceso',
            ]),
            $proceeding->specialty->instance->judicialdistrict->only(['judis_nombre']),
            ['esp_nombre' => $proceeding->specialty->esp_nombre],
            ['ins_nombre' => $proceeding->specialty->instance->ins_nombre],
            ['tipo_persona' => $tipo_persona],
            $personData // Aquí agregamos los datos de la persona
        );
    }
    
}
