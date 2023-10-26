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

    protected function getRecentProceedings(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->take(5)
            ->get();

        $data = $proceedings->map(function ($proceeding) {
            $person = $proceeding->person;
            $personData = null;

            if ($person) {
                $type = $person->nat_id !== null ? 'natural' : 'juridica';

                $personData = $type === 'natural' ? $person->persona : $person->juridica;

                $result = array(
                    'exp_id' => $proceeding->exp_id,
                    'numero' => $proceeding->exp_numero,
                    'fecha_inicio' => $proceeding->exp_fecha_inicio,
                    'pretencion' => $proceeding->exp_pretencion,
                    'materia' => $proceeding->exp_materia,
                    'especialidad' => $proceeding->exp_especialidad,
                    'monto_pretencion' => $proceeding->exp_monto_pretencion,
                    'estado_proceso' => $proceeding->exp_estado_proceso,
                    'tipo_persona' => $type,
                );

                if ($type === 'natural') {
                    $result['nat_id'] = $person->nat_id;
                    $result['dni'] = $personData->nat_dni;
                    $result['apellido_paterno'] = $personData->nat_apellido_paterno;
                    $result['apellido_materno'] = $personData->nat_apellido_materno;
                    $result['nombres'] = $personData->nat_nombres;
                    $result['telefono'] = $personData->nat_telefono;
                    $result['correo'] = $personData->nat_correo;
                } else if ($type === 'juridica') {
                    $result['jur_id'] = $person->jur_id;
                    $result['ruc'] = $personData->jur_ruc;
                    $result['razon_social'] = $personData->jur_razon_social;
                    $result['telefono'] = $personData->jur_telefono;
                    $result['correo'] = $personData->jur_correo;
                }

                return $result;
            }

            return null;
        })->filter();

        return response()->json(['data' => $data], 200);
    }

    public function pdfabogados()
    {
        $abogados = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();
        $pdf = PDF::loadView('vista_pdf_abo', ['data' => $abogados]);
        //return $pdf->stream();
        return $pdf->download('archivo.pdf');
    }
    
    protected function pdfexptramite(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'EN TRAMITE')
            ->with('person.address')
            ->with('specialty.instance.judicialdistrict')
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
        $pdf = PDF::loadView('pdfExpedienteTramite', ['data' => $data]);
        //return $pdf->stream();
        return $pdf->download('archivo.pdf');
        // return response()->json(['data' => $data], 200);

    }
    protected function pdfexpejecucion(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('exp_estado_proceso', 'EN EJECUCION')
            ->with('person.address')
            ->with('specialty.instance.judicialdistrict')
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
        $pdf = PDF::loadView('vista_pdf_exp_ejc', ['data' => $data]);
        //return $pdf->stream();
        return $pdf->download('archivo.pdf');
        // return response()->json(['data' => $data], 200);

    }
    protected function pdfexps(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->with('person.address')
            ->with('specialty.instance.judicialdistrict')
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
        $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
        //return $pdf->stream();
        return $pdf->download('archivo.pdf');
        // return response()->json(['data' => $data], 200);

    }
    protected function pdfdemandantes(Request $request)
    {
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
          ->with('specialty.instance.judicialdistrict')
           ->where('exp_fecha_inicio', 'LIKE', $fechaBuscada . '%') // Filtra por el mes y año
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
     $pdf = PDF::loadView('vista_pdf_exps', ['data' => $data]);
    //return $pdf->stream();
     return $pdf->download('archivo.pdf');
   // return response()->json(['data' => $data,'fecha'=>$fechaBuscada], 200);
    }
    protected function pdfmateria(Request $request)
    {
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
        ->with('person.address')
        ->where('exp_materia',$request->exp_materia)
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
}
