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
        $expTotalEnTramite = \App\Models\Proceeding::where('exp_estado_proceso', 
        'EN TRAMITE')->count();
        $expTotalEnEjecucion = \App\Models\Proceeding::where('exp_estado_proceso', 
        'EN EJECUCION')->count();
         $demandantes=\App\Models\Person::count();

        return \response()->json(['state'=> 200,'exptotal' => $expTotal,'exptramite'=>$expTotalEnTramite,
        'demandante'=> $demandantes,'expejecucion'=>$expTotalEnEjecucion
            ], 200);

    }
    protected function exprecientes(Request $request){
        $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
    ->with('person.address')
    ->take(5)
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
            ->where('exp_estado_proceso','EN TRAMITE')
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
        $pdf = PDF::loadView('vista_pdf_exp_tra', ['data' => $data]);
        //return $pdf->stream();
         return $pdf->download('archivo.pdf');
        // return response()->json(['data' => $data], 200);

    }
      
}
