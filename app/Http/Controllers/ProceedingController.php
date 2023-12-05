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
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('person.juridica', 'person.persona', 'pretension', 'materia')
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
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => ucwords(strtolower($proceeding->materia->mat_nombre)),
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

            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($request->exp['exp_numero'])),
                'exp_fecha_inicio' => $request->exp['exp_fecha_inicio'],
                'exp_pretencion' => strtoupper(trim($request->exp['exp_pretencion'])),
                'exp_materia' => strtoupper(trim($request->exp['exp_materia'])),
                'exp_dis_judicial' => strtoupper(trim($request->exp['exp_distrito_judicial'])),
                'exp_instancia' => strtoupper(trim($request->exp['exp_instancia'])),
                'exp_especialidad' => trim($request->exp['exp_especialidad']),
                'exp_monto_pretencion' => trim($request->exp['exp_monto_pretencion']),
                'exp_estado_proceso' => 'EN TRAMITE',
                'exp_juzgado' => strtoupper(trim($request->exp['exp_juzgado'])),
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
                        'nat_correo' => trim($request->pn['nat_correo'])
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
                        'jur_correo' => trim($request->pj['jur_correo']),
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
            if ($request->procesal == 'DEMANDANTE') {
                $EX->exp_demandante = strtoupper(trim($perId));
                $EX->exp_demandado = null;
            } else {
                $EX->exp_demandante = null;
                $EX->exp_demandado = strtoupper(trim($perId));
            }
            $EX->abo_id = $request->abo_id;
            $EX->save();

            /*ACTULIZAR ESTADO DE ABOGADO */
            $abogado = \App\Models\Lawyer::find($request->abo_id);
            $abogado->abo_disponibilidad = 'OCUPADO';
            $abogado->abo_carga_laboral = $abogado->abo_carga_laboral + 1;
            $abogado->save();
            DB::commit();

            return \response()->json(['state' => 0, 'data' => $EX, 'dir' => $request->dir], 200);
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
            $persona = null;
            $direccion = null;
            $per = null;
            if ($request->tipoper == 'natural') {
                $persona = \App\Models\PeopleNatural::find($request->pnat['nat_id']);
                $persona->nat_dni = strtoupper(trim($request->pnat['nat_dni']));
                $persona->nat_apellido_paterno = strtoupper(trim($request->pnat['nat_apellido_paterno']));
                $persona->nat_apellido_materno = strtoupper(trim($request->pnat['nat_apellido_materno']));
                $persona->nat_nombres = strtoupper(trim($request->pnat['nat_nombres']));
                $persona->nat_telefono = strtoupper(trim($request->pnat['nat_telefono']));
                $persona->nat_correo = strtoupper(trim($request->pnat['nat_correo']));
                $persona->save();
            } else {
                $persona = \App\Models\PeopleJuridic::find($request->pjuc['jur_id']);
                $persona->jur_ruc = strtoupper(trim($request->pjuc['jur_ruc']));
                $persona->jur_razon_social = strtoupper(trim($request->pjuc['jur_razon_social']));
                $persona->jur_telefono = strtoupper(trim($request->pjuc['jur_telefono']));
                $persona->jur_correo = strtoupper(trim($request->pjuc['jur_correo']));
                $persona->jur_rep_legal = strtoupper(trim($request->pjuc['jur_rep_legal']));
                $persona->save();
            }
            $direccion = \App\Models\Address::updateOrCreate(
                ['per_id' => $request->expediente['persona']],
                [
                    'dir_calle_av' => trim($request->direccion['dir_calle_av']),
                    'dis_id' => trim($request->direccion['dis_id']),
                    'pro_id' => trim($request->direccion['pro_id']),
                    'dep_id' => trim($request->direccion['dep_id']),
                ]
            );

            /*Actulizar el expediente  asignando laersona y el abogado*/
            $EX = \App\Models\Proceeding::find($exp->exp_id);
            if ($request->procesal == 'demandante') {
                $EX->exp_demandante = $request->expediente['persona'];
            } else {
                $EX->exp_demandado = $request->expediente['persona'];
            }
            $EX->save();
            DB::commit();
            return \response()->json(['state' => 0, 'data' => $EX], 200);
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
                'demandante.persona',
                'demandado.persona'
            )
            ->find($id);

        if (!$proceeding) {
            return response()->json(['error' => 'Expediente no encontrado'], 404);
        }

        $procesal = null;
        $person = null;

        if ($proceeding->exp_demandante !== null) {
            $person = $proceeding->demandante;
            $procesal = 'demandante';
        } elseif ($proceeding->exp_demandado !== null) {
            $person = $proceeding->demandado;
            $procesal = 'demandado';
        }

        $personData = null;
        $tipo_persona = null;

        // Información de distrito judicial
        $districtName = optional($proceeding->distritoJudicial)->judis_nombre;

        // Información de instancia
        $instanceName = optional($proceeding->instancia)->ins_nombre;

        // Información de especialidad
        $specialtyName = optional($proceeding->specialty)->esp_nombre;

        $judicial = [
            'distrito_judicial' => $districtName,
            'instancia' => $instanceName,
            'especialidad' => $specialtyName,
        ];

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
        $data['per_id'] = optional($person)->per_id;

        // Traer archivos
        $eje = \App\Models\LegalDocument::where('exp_id', $id)->where('doc_tipo', 'EJE')
            ->orderBy('created_at', 'DESC')->get();
        $escritos = \App\Models\LegalDocument::where('exp_id', $id)->where('doc_tipo', 'ESCRITO')
            ->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'data' => $data,
            'eje' => $eje,
            'escritos' => $escritos,
            'procesal' => $procesal,
            'judicial' => $judicial
        ], 200);
    }

    protected function showupdate($id)
    {
        $person = null;
        $procesal = null;
        $personData = null;
        $tipo_persona = null;

        $proceeding = \App\Models\Proceeding::with('specialty', 'instancia', 'distritoJudicial')
            ->with('abogado.persona')
            ->find($id);

        if (!$proceeding) {
            return response()->json(['error' => 'Expediente no encontrado'], 404);
        }

        if ($proceeding->exp_demandante !== null) {
            $person = \App\Models\Person::with('address.district.province.departament')
                ->where('per_id', $proceeding->exp_demandante)
                ->first();
            $procesal = 'demandante';
        } elseif ($proceeding->exp_demandado !== null) {
            $person = \App\Models\Person::with('address.district.province.departament')
                ->where('per_id', $proceeding->exp_demandado)
                ->first();
            $procesal = 'demandado';
        }

        if ($person) {
            if ($person->nat_id !== null) {
                $personData = $this->getNaturalPersonData($person);
                $tipo_persona = 'natural';
            } elseif ($person->jur_id !== null) {
                $personData = $person->juridica;
                $tipo_persona = 'juridica';
            }
        }

        $costos = \App\Models\ExecutionAmount::where('exp_id', $proceeding->exp_id)
            ->first();

        return response()->json([
            'proceeding' => $proceeding,
            'person' => $person,
            'tipo_persona' => $tipo_persona,
            'personData' => $personData,
            'procesal' => $procesal,
            'costos' => $costos,
        ], 200);
    }

    protected function take()
    {
        $proceedings = \App\Models\Proceeding::latest('created_at')
            ->with('person.juridica', 'person.persona', 'pretension', 'materia')
            ->take(5)
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
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => ucwords(strtolower($proceeding->materia->mat_nombre)),
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


    private function getNaturalPersonData($person)
    {
        $nat_nombres = $person->persona->nat_nombres;
        $nombre_array = explode(' ', $nat_nombres);

        $capitalized_names = array_map(function ($name) {
            return ucwords(strtolower($name));
        }, $nombre_array);

        return [
            // 'nat_id' => $person->persona->nat_id,
            'nat_dni' => $person->persona->nat_dni,
            'nat_apellido_paterno' => ucwords(strtolower($person->persona->nat_apellido_paterno)),
            'nat_apellido_materno' => ucwords(strtolower($person->persona->nat_apellido_materno)),
            'nat_nombres' => implode(' ', $capitalized_names),
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
                'exp_juzgado',
            ]),
            // $proceeding->specialty->instance->judicialdistrict->only(['judis_nombre']),
            // ['esp_nombre' => $proceeding->specialty->esp_nombre],
            // ['ins_nombre' => $proceeding->specialty->instance->ins_nombre],
            ['nombre_materia' => $proceeding->materia->mat_nombre],
            ['nombre_juzgado' => $proceeding->juzgado->co_nombre],
            ['tipo_persona' => $tipo_persona],
            $personData // Aquí agregamos los datos de la persona
        );
    }

   
}
