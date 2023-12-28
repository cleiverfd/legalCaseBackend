<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Uuid;

class ProceedingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index()
    {
        $procedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('procesal.persona', 'pretension', 'materia')
            ->get();

         $formattedData = [];
        foreach ($procedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' =>$proceeding->exp_fecha_inicio,
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => $proceeding->materia->mat_nombre,
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }

        return response()->json(['data' => $formattedData], 200);
    }

    protected function listarestado(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', $request->exp_estado_proceso)
            ->with('person.juridica', 'person.persona')
            ->get();
        $data = $proceedings->map(function ($proceeding) {
            $procesal = null;
            $tipo_persona = null;
            if ($proceeding) {
                if ($proceeding->exp_demandante !== null) {
                    $person = $proceeding->demandante;
                    $procesal = 'demandante';
                } elseif ($proceeding->exp_demandado !== null) {
                    $person = $proceeding->demandado;
                    $procesal = 'demandado';
                }
            }
            $fecha_inicio = $proceeding->exp_fecha_inicio;
            $fecha_formateada = date('d-m-Y', strtotime($fecha_inicio));
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $fecha_formateada,
                'pretencion' => ucwords(strtolower($proceeding->exp_pretencion)),
                'materia' => ucwords(strtolower($proceeding->exp_materia)),
                'especialidad' => ucwords(strtolower($proceeding->exp_especialidad)),
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'procesal' => $procesal
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
            DB::beginTransaction();
            $multiple = null;
            if ($request->multiple == "0") {
                $multiple = 0;
            } else {
                $multiple = 1;
            }

            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($request->exp['exp_numero'])),
                'exp_fecha_inicio' => $request->exp['exp_fecha_inicio'],
                'exp_pretencion' => strtoupper(trim($request->exp['exp_pretencion'])),
                'exp_materia' => strtoupper(trim($request->exp['exp_materia'])),
                'exp_dis_judicial' => strtoupper(trim($request->exp['exp_distrito_judicial'])),
                'exp_instancia' => strtoupper(trim($request->exp['exp_instancia'])),
                'exp_especialidad' => trim($request->exp['exp_especialidad']),
                'exp_monto_pretencion' => trim($request->exp['exp_monto_pretencion']),
                'exp_estado_proceso' => trim($request->exp['exp_estado_proceso']),
                'exp_juzgado' => strtoupper(trim($request->exp['exp_juzgado'])),
                'multiple' => $multiple,
                'abo_id' => $request->abo_id
            ]);
            // actualizar o crear costos
            if (
                $request->exp['exp_estado_proceso'] == 'EN EJECUCION' ||
                $request->exp['exp_estado_proceso'] == 'ARCHIVADO'
            ) {
                $costo = \App\Models\ExecutionAmount::updateOrCreate(
                    ['exp_id' => strtoupper(trim($exp->exp_id))],
                    [
                        'ex_ejecucion_1' => $request->exp['exp_monto_ejecucion1'] != '' ? strtoupper(trim($request->exp['exp_monto_ejecucion1'])) : null,
                        'ex_ejecucion_2' => $request->exp['exp_monto_ejecucion2'] != '' ? strtoupper(trim($request->exp['exp_monto_ejecucion2'])) : null,
                        'ex_interes_1'   => $request->exp['exp_interes1'] != '' ? strtoupper(trim($request->exp['exp_interes1'])) : null,
                        'ex_interes_2'   => $request->exp['exp_interes2'] != '' ? strtoupper(trim($request->exp['exp_interes2'])) : null,
                        'ex_costos'      => $request->exp['exp_costos'] != '' ? strtoupper(trim($request->exp['exp_costos'])) : null,
                    ]
                );
            }
            $persona = null;
            if ($request->multiple == "0") {
                if ($request->tipopersona == 'NATURAL') {
                    $persona = \App\Models\Person::updateOrCreate(
                        ['nat_dni' => strtoupper(trim($request->pn['nat_dni']))],
                        [
                            'nat_apellido_paterno' => strtoupper(trim($request->pn['nat_apellido_paterno'])),
                            'nat_apellido_materno' => strtoupper(trim($request->pn['nat_apellido_materno'])),
                            'nat_nombres' => strtoupper(trim($request->pn['nat_nombres'])),
                            'nat_telefono' => strtoupper(trim($request->pn['nat_telefono'])),
                            'nat_correo' => trim($request->pn['nat_correo']),
                            'tipo_procesal' => $request->procesal,
                            'per_condicion' => $request->condicion
                        ]
                    );
                } else {
                    $persona = \App\Models\Person::updateOrCreate(
                        ['jur_ruc' => strtoupper(trim($request->pj['jur_ruc']))],
                        [
                            'jur_razon_social' => strtoupper(trim($request->pj['jur_razon_social'])),
                            'jur_telefono' => strtoupper(trim($request->pj['jur_telefono'])),
                            'jur_correo' => trim($request->pj['jur_correo']),
                            'jur_rep_legal' => strtoupper(trim($request->pj['jur_rep_legal'])),
                            'tipo_procesal' => $request->procesal,
                            'per_condicion' => $request->condicion
                        ]
                    );
                }

                $procesal = null;
                $procesal = \App\Models\Procesal::Create(

                    [
                        'tipo_procesal' => trim($request->procesal),
                        'tipo_persona' => trim($request->tipopersona),
                        'per_id' => $persona->per_id,
                        'exp_id' => $exp->exp_id,
                    ]
                );
                $direccion = null;
                $direccion = \App\Models\Address::updateOrCreate(
                    ['per_id' => $procesal->per_id],
                    [
                        'dir_calle_av' => trim($request->dir['dir_calle_av']),
                        'dis_id' => trim($request->dir['dis_id']),
                        'pro_id' => trim($request->dir['pro_id']),
                        'dep_id' => trim($request->dir['dep_id']),
                    ]
                );
            } else {
                $personas = $request->Personas;

                foreach ($personas as $persona) {
                    $person = null;
                    if ($persona['tipo'] == 'NATURAL') {
                        // Crear registro para persona natural
                        $person = \App\Models\Person::updateOrcreate(
                            ['nat_dni' => strtoupper(trim($persona['nat_dni']))],
                            [
                                'nat_apellido_paterno' => strtoupper(trim($persona['nat_apellido_paterno'])),
                                'nat_apellido_materno' => strtoupper(trim($persona['nat_apellido_materno'])),
                                'nat_nombres' => strtoupper(trim($persona['nat_nombres'])),
                                'nat_telefono' => strtoupper(trim($persona['nat_telefono'])),
                                'nat_correo' => trim($persona['nat_correo']),
                                'per_condicion' => $persona['condicion'],
                                'tipo_procesal' => $persona['procesal']
                            ]
                        );
                    } else {
                        $person = \App\Models\Person::updateOrCreate(
                            ['jur_ruc' => strtoupper(trim($persona['jur_ruc']))],
                            [
                                'jur_razon_social' => strtoupper(trim($persona['jur_razon_social'])),
                                'jur_telefono' => strtoupper(trim($persona['jur_telefono'])),
                                'jur_correo' => trim($persona['jur_correo']),
                                'jur_rep_legal' => strtoupper(trim($persona['jur_rep_legal'])),
                                'per_condicion' => $persona['condicion'],
                                'tipo_procesal' => $persona['procesal']
                            ]
                        );
                    }
                    $procesal = null;
                    $procesal = \App\Models\Procesal::Create(
                        [
                            'tipo_procesal' => $persona['procesal'],
                            'tipo_persona' => $persona['tipo'],
                            'per_id' => $person->per_id,
                            'exp_id' => $exp->exp_id,
                        ]
                    );
                    $direccion = null;
                    $direccion = \App\Models\Address::updateOrCreate(
                        ['per_id' => $procesal->per_id],
                        [
                            'dir_calle_av' => trim($persona['dir_calle_av']),
                            'dis_id' => trim($persona['dis_id']),
                            'pro_id' => trim($persona['pro_id']),
                            'dep_id' => trim($persona['dep_id']),
                        ]
                    );
                }
            }
            $abogado = \App\Models\Lawyer::find($request->abo_id);
            $abogado->abo_disponibilidad = 'OCUPADO';
            $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
            $abogado->save();
            DB::commit();
            return \response()->json(['state' => 0, 'data' => $exp], 200);
        } catch (Exception $e) {
            DB::rollback();
            return  \response()->json(['state' => '1', 'exception' => (string) $e]);
        }
    }

    protected function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $exp = \App\Models\Proceeding::find($request->expediente['exp_id']);
            $exp->exp_numero = strtoupper(trim($request->expediente['exp_numero']));
            $exp->exp_fecha_inicio = $request->expediente['exp_fecha_inicio'];
            $exp->exp_pretencion = strtoupper(trim($request->expediente['exp_pretencion']));
            $exp->exp_materia = strtoupper(trim($request->expediente['exp_materia']));
            $exp->exp_especialidad = trim($request->expediente['exp_especialidad']);
            $exp->exp_monto_pretencion = trim($request->expediente['exp_monto_pretencion']);
            $exp->exp_juzgado = trim($request->expediente['exp_juzgado']);
            $exp->exp_estado_proceso = trim($request->expediente['exp_estado_proceso']);
            $exp->save();

            // actualizar o crear costos
            if (
                $request->expediente['exp_estado_proceso'] == 'EN EJECUCION' ||
                $request->expediente['exp_estado_proceso'] == 'ARCHIVADO'
            ) {
                $costo = \App\Models\ExecutionAmount::updateOrCreate(
                    ['exp_id' => strtoupper(trim($request->expediente['exp_id']))],
                    [
                        'ex_ejecucion_1' => $request->expediente['exp_monto_ejecucion1'] != '' ? strtoupper(trim($request->expediente['exp_monto_ejecucion1'])) : null,
                        'ex_ejecucion_2' => $request->expediente['exp_monto_ejecucion2'] != '' ? strtoupper(trim($request->expediente['exp_monto_ejecucion2'])) : null,
                        'ex_interes_1'   => $request->expediente['exp_interes1'] != '' ? strtoupper(trim($request->expediente['exp_interes1'])) : null,
                        'ex_interes_2'   => $request->expediente['exp_interes2'] != '' ? strtoupper(trim($request->expediente['exp_interes2'])) : null,
                        'ex_costos'      => $request->expediente['exp_costos'] != '' ? strtoupper(trim($request->expediente['exp_costos'])) : null,
                    ]
                );
            }
            $personas = $request->Personas;

            foreach ($personas as $persona) {
                $person = null;
                if ($persona['tipo_persona'] == 'NATURAL') {
                    // Crear registro para persona natural
                    $person = \App\Models\Person::updateOrcreate(
                        ['per_id' => strtoupper(trim($persona['per_id']))],
                        [   'nat_dni' => strtoupper(trim($persona['nat_dni'])),
                            'nat_apellido_paterno' => strtoupper(trim($persona['nat_apellido_paterno'])),
                            'nat_apellido_materno' => strtoupper(trim($persona['nat_apellido_materno'])),
                            'nat_nombres' => strtoupper(trim($persona['nat_nombres'])),
                            'nat_telefono' => strtoupper(trim($persona['nat_telefono'])),
                            'nat_correo' => trim($persona['nat_correo']),
                            'per_condicion' => $persona['per_condicion'],
                            'tipo_procesal' => $persona['tipo_procesal']
                        ]
                    );
                } else {
                    $person = \App\Models\Person::updateOrCreate(
                        ['per_id' => strtoupper(trim($persona['per_id']))],
                        [   
                            'jur_ruc' => strtoupper(trim($persona['jur_ruc'])),
                            'jur_razon_social' => strtoupper(trim($persona['jur_razon_social'])),
                            'jur_telefono' => strtoupper(trim($persona['jur_telefono'])),
                            'jur_correo' => trim($persona['jur_correo']),
                            'jur_rep_legal' => strtoupper(trim($persona['jur_rep_legal'])),
                            'per_condicion' => $persona['per_condicion'],
                            'tipo_procesal' => $persona['tipo_procesal']
                        ]
                    );
                }
                $direccion = null;
                $direccion = \App\Models\Address::updateOrCreate(
                    ['per_id' => $persona['per_id']],
                    [
                        'dir_calle_av' => trim($persona['dir_calle_av']),
                        'dis_id' => trim($persona['dis_id']),
                        'pro_id' => trim($persona['pro_id']),
                        'dep_id' => trim($persona['dep_id']),
                    ]
                );
            }
            DB::commit();
            return \response()->json(['state' => 0, 'data' => 'OK'], 200);
        } catch (Exception $e) {
            DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function show($id)
    {
        $proceeding = \App\Models\Proceeding::with(
            'specialty',
            'juzgado',
            'instancia',
            'distritoJudicial',
            'materia',
            'pretension',
            'pretension',
            'procesal.persona',
        )
            ->find($id);

        if (!$proceeding) {
            return response()->json(['error' => 'Expediente no encontrado'], 404);
        }

        $dataGeneral = null;
        $dataProcesal = null;
        $dataEje = null;
        $dataEscritos = null;

        $dataGeneral = [
            'exp_id' => $proceeding->exp_id,
            'exp_numero' => $proceeding->exp_numero,
            'exp_juzgado' => $proceeding->juzgado->co_nombre,
            'exp_distrito_judicial' => $proceeding->distritoJudicial->judis_nombre,
            'exp_fecha_inicio' => $proceeding->exp_fecha_inicio,
            'exp_especialidad' => $proceeding->specialty->esp_nombre,
            'exp_materia' => $proceeding->materia->mat_nombre,
            'exp_pretension' =>$proceeding->pretension->pre_nombre,
            'exp_monto_pretension'=>$proceeding->exp_monto_pretencion,
            'exp_estado' => $proceeding->exp_estado_proceso
        ];

        $dataProcesal = $this->formatProcesalData($proceeding->procesal);

        // Traer archivos
        $dataEje = \App\Models\LegalDocument::where('exp_id', $id)->where('doc_tipo', 'EJE')
            ->orderBy('created_at', 'DESC')->get();
        $dataEscritos = \App\Models\LegalDocument::where('exp_id', $id)->where('doc_tipo', 'ESCRITO')
            ->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'expediente' => $dataGeneral,
            'procesales' => $dataProcesal,
            'eje' => $dataEje,
            'escritos' => $dataEscritos,
        ], 200);
    }

    protected function showupdate($id)
    {
        $proceeding = \App\Models\Proceeding::
        with('abogado.persona')->
        find($id);
        $proceeding1 = \App\Models\Proceeding::with('procesal.persona.address')->find($id);
        $processedProcesalData = $proceeding1->procesal->map(function ($proc) {
            return [
                'proc_id' => $proc->proc_id,
                "tipo_procesal"=> $proc->tipo_procesal,
                "tipo_persona"=> $proc->tipo_persona,
                "per_id"=> $proc->per_id,
                "exp_id"=> $proc->exp_id,
                'nat_dni' => $proc->persona->nat_dni,
                'nat_apellido_paterno' => ucwords(strtolower($proc->persona->nat_apellido_paterno)),
                "nat_apellido_materno" => ucwords(strtolower($proc->persona->nat_apellido_materno)),
                "nat_nombres" => ucwords(strtolower($proc->persona->nat_nombres)),
                "nat_telefono" => $proc->persona->nat_telefono,
                "nat_correo" => $proc->persona->nat_correo,
                "jur_ruc" => $proc->persona->jur_ruc,
                "jur_razon_social" => ucwords(strtolower($proc->persona->jur_razon_social)),
                "jur_telefono" => $proc->persona->jur_telefono,
                "jur_correo" => $proc->persona->jur_correo,
                "jur_rep_legal" => $proc->persona->jur_rep_legal,
                "per_condicion" => $proc->persona->per_condicion,
                'dir_id' => $proc->persona->address[0]->dir_id,
                'dir_calle_av' => $proc->persona->address[0]->dir_calle_av,
                "dis_id"=> $proc->persona->address[0]->dis_id,
                "pro_id"=> $proc->persona->address[0]->pro_id,
                "dep_id"=> $proc->persona->address[0]->dep_id,
            ];
        });
        $costos = \App\Models\ExecutionAmount::where('exp_id', $proceeding->exp_id)
            ->first();

        return response()->json([
            'proceeding' => $proceeding,
            'personData' =>$processedProcesalData,
            'costos' => $costos,
        ], 200);
    }
    protected function take()
    {
        $proceedings = \App\Models\Proceeding::latest('created_at')
            ->with('procesal.persona', 'pretension', 'materia')
            ->take(5)
            ->get();

        $formattedData = [];
        foreach ($proceedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => date('d-m-Y', strtotime($proceeding->exp_fecha_inicio)),
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => $proceeding->materia->mat_nombre,
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }

        return response()->json(['data' => $formattedData], 200);
    }

    protected function buscarPorId(Request $request)
    {
        $expId = $request->exp_id;
        $proceeding = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('procesal.persona', 'juzgado')
            ->find($expId);

        $eje = \App\Models\LegalDocument::where('exp_id', $expId)->where('doc_tipo', 'EJE')
            ->orderBy('created_at', 'DESC')->get();

        if ($proceeding !== null) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'juzgado' => $proceeding->juzgado ? $proceeding->juzgado->co_nombre : null,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];

            return response()->json(['data' => $commonData, 'eje' => $eje], 200);
        } else {
            // Manejar el caso cuando no se encuentra el registro
            return response()->json(['error' => 'Expediente no encontrado'], 404);
        }
    }

    //formatear los procesales
    protected function formatProcesalData($procesal)
    {
        $processedProcesals = [];

        foreach ($procesal as $procesalItem) {
            $data = [
                'proc_id' => $procesalItem->proc_id,
                'per_id' => $procesalItem->per_id,
                'tipo_procesal' => $procesalItem->tipo_procesal,
                'tipo_persona' => $procesalItem->tipo_persona,
            ];

            if ($procesalItem->tipo_persona === 'NATURAL') {
                $data = array_merge($data, [
                    'dni' => $procesalItem->persona->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($procesalItem->persona->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($procesalItem->persona->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($procesalItem->persona->nat_nombres)),
                    'telefono' => $procesalItem->persona->nat_telefono,
                    'correo' => strtolower($procesalItem->persona->nat_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            } else {
                $data = array_merge($data, [
                    'ruc' => $procesalItem->persona->jur_ruc,
                    'razon_social' => ucwords(strtolower($procesalItem->persona->jur_razon_social)),
                    'telefono' => $procesalItem->persona->jur_telefono,
                    'correo' => strtolower($procesalItem->persona->jur_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            }

            $processedProcesals[] = $data;
        }

        return $processedProcesals;
    }
    public function filterprocesal(Request $request){
           $persona=null;
            $documento = trim($request->doc);
            if($request->tipo=='NATURAL'){
            $persona =\App\Models\Person::
            where('nat_dni',$documento)->first();
            }else{
            $persona = \App\Models\Person::
             where('jur_ruc',$documento)->first();
            }
            if($persona){
                $per = \App\Models\Procesal::
                where('per_id',$persona->per_id)->first();
                 if(!$per){
                    return \response()->json(['state' => 1, 'data' =>'Persona no Encontrada'], 200);
                 }
                $dir=\App\Models\Address::
                where('per_id',$persona->per_id)->first();
                
                return \response()->json(['state' => 0, 'data' => $persona,
                'dir'=>$dir], 200);  
            }
            return \response()->json(['state' => 1, 'data' =>'Persona no Encontrada'], 200);      
            
         
    }  
}
