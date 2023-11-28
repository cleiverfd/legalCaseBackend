<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;
use PDF;
use App\Http\Resources\{
    LawyerResource
};

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function inicio(Request $request)
    {
        $expTotal = \App\Models\Proceeding::count();
        $expTotalEnTramite = \App\Models\Proceeding::where(
            'exp_estado_proceso',
            'EN TRAMITE'
        )->count();
        $expTotalEnEjecucion = \App\Models\Proceeding::where(
            'exp_estado_proceso',
            'EN EJECUCION'
        )->count();
        $demandantes = \App\Models\Person::count();

        return \response()->json([
            'state' => 200, 'exptotal' => $expTotal, 'exptramite' => $expTotalEnTramite,
            'demandante' => $demandantes, 'expejecucion' => $expTotalEnEjecucion
        ], 200);
    }

    protected function exprecientes(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('person.juridica', 'person.persona')
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

    public function pdfabogados(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE ABOGADO/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $abogados = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();
        $pdf = PDF::loadView('vista_pdf_abo', ['data' => $abogados]);
        return $pdf->download('archivo.pdf');
    }

    protected function pdfexptramite(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE EN TRAMITE/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
        ->where('exp_estado_proceso', 'EN TRAMITE')
        ->with('person.address')
        ->with('specialty.instance.judicialdistrict')
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
                'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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

        $pdf = PDF::loadView('pdfExpedienteTramite', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfexpejecucion(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE EN EJECUCION/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
        ->where('exp_estado_proceso', 'EN EJECUCION')
        ->with('person.address')
        ->with('specialty.instance.judicialdistrict')
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
                'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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
        $pdf = PDF::loadView('vista_pdf_exp_ejc', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfexps(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTES /AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
        ->with('person.address')
        ->with('specialty.instance.judicialdistrict')
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
                'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfdemandantes(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE DEMANDANTES/AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->get();

        $data = $proceedings->map(function ($proceeding) {
            $person = $proceeding->person;
            $personData = null;
            $type = null;
            if ($person) {
                if ($person->nat_id !== null) {
                    $personData = $person->persona;
                    $type = 'natural';
                } elseif ($person->jur_id !== null) {
                    $personData = $person->juridica;
                    $type = 'juridica';
                }
            }
            return array_merge($proceeding->toArray(), [
                'person_data' => $personData ? $personData->toArray() : null,
                'type' => $type,
            ]);
        });
        $pdf = PDF::loadView('vista_pdf_de', ['data' => $data]);
        //return $pdf->stream();
        return $pdf->download('archivo.pdf');
        // return response()->json(['data' => $data], 200);

    }
    protected function pdffechaaño(Request $request)

    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  MES Y AÑO /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $mes = $request->mes;
        $año = $request->año;
        $mes = intval($mes);
        if ($mes >= 1 && $mes <= 9) {
            $mesFormateado = '0' . $mes;
        } else {
            $mesFormateado = (string) $mes;
        }
        $fechaBuscada = $año . '-' . $mesFormateado;
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->where('exp_fecha_inicio', 'LIKE', $fechaBuscada . '%') // Filtra por el mes y año
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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
        
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfmateria(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  MATERIA /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->where('exp_materia', $request->exp_materia)
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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

       
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfexpsabogado(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  ABOGADO /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->where('abo_id', $request->abo_id)
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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

       
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdfpretenciones(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  PRETENCIONES /AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->with('montos')
            ->get();
            $data = $proceedings->map(function ($proceeding) {
                $procesal = null;
                $tipo_persona = null;
                $montos=null;
                if ($proceeding) {
                    $montos=$proceeding->montos;
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
                    'monto_pretencion' => $proceeding->exp_monto_pretencion,
                    'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                    'procesal' => $procesal,
                     ];
                if($montos){
                    $commonData+=[
                    '$monto_ejecucion1' => $montos->ex_ejecucion_1 != null ? $proceeding->montos->ex_ejecucion_1 : '',
                    '$monto_ejecucion2' => $proceeding->montos->ex_ejecucion_2 != null ? $proceeding->montos->ex_ejecucion_2 : '',
                    '$interes1' => $proceeding->montos->ex_interes_1 != null ? $proceeding->montos->ex_interes_1 : '',
                    '$interes2' => $proceeding->montos->ex_interes_2 != null ? $proceeding->montos->ex_interes_2 : '',
                    '$costos' =>$proceeding->montos->ex_costos != null ? $proceeding->montos->ex_costos : '',
                    ];
                }
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

           // return \response()->json(['state' => 0, 'data' => $data], 200);
         $pdf = PDF::loadView('pdfpretensiones', ['data' => $data]);
         return $pdf->download('archivo.pdf');
    }
    protected function pdfejecuciones(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  EJECUCIONES /AUTOMATIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->where('exp_estado_proceso',  'EN EJECUCION')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->with('montos')
            ->get();
            $data = $proceedings->map(function ($proceeding) {
                $procesal = null;
                $tipo_persona = null;
                $montos=null;
                if ($proceeding) {
                    $montos=$proceeding->montos;
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
                    'monto_pretencion' => $proceeding->exp_monto_pretencion,
                    'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                    'procesal' => $procesal,
                     ];
                if($montos){
                    $commonData+=[
                    'monto_ejecucion1' => $montos->ex_ejecucion_1 != null ? $proceeding->montos->ex_ejecucion_1 : '',
                    'monto_ejecucion2' => $proceeding->montos->ex_ejecucion_2 != null ? $proceeding->montos->ex_ejecucion_2 : '',
                    'interes1' => $proceeding->montos->ex_interes_1 != null ? $proceeding->montos->ex_interes_1 : '',
                    'interes2' => $proceeding->montos->ex_interes_2 != null ? $proceeding->montos->ex_interes_2 : '',
                    'costos' =>$proceeding->montos->ex_costos != null ? $proceeding->montos->ex_costos : '',
                    ];
                }
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

            //return \response()->json(['state' => 0, 'data' => $data], 200);
         $pdf = PDF::loadView('pdfejecuciones', ['data' => $data]);
         return $pdf->download('archivo.pdf');
    }
    protected function pdfpretension(Request $request)
    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  PRETENSION /PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->where('exp_pretencion', $request->exp_pretension)
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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

       
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }
    protected function pdffechas(Request $request)

    {
        $report = \App\Models\Report::create([
            'rep_fecha_generacion' => now()->setTimezone('America/Lima'),
            'rep_tipo' => 'REPORTE EXPEDIENTE  DESDE-HASTA/PERSONALIZADO',
            'usu_id' => $request->usu_id,
        ]);
        $mes = $request->mes;
        $año = $request->año;
        $mes = intval($mes);
        if ($mes >= 1 && $mes <= 9) {
            $mesFormateado = '0' . $mes;
        } else {
            $mesFormateado = (string) $mes;
        }
        $fechaBuscada = $año . '-' . $mesFormateado;
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('person.juridica', 'person.persona')
            ->with('specialty.instance.judicialdistrict')
            ->whereBetween('exp_fecha_inicio', [$request->fechaDesde,$request->fechaHasta])
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
                    'especialidad' => ucwords(strtolower($proceeding->specialty->esp_nombre)),
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
        
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        return $pdf->download('archivo.pdf');
    }

}
