<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;

class PersonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function traerexpedientes(Request $request)
{
    try {
        \DB::beginTransaction();

        $persona = null;
        $person = null;

        if (strlen($request->documento) === 8) {
            $persona = \App\Models\PeopleNatural::where('nat_dni', $request->documento)->first();
            if (!$persona) {
                return response()->json(['state' => 1], 404);
            }
            $person = \App\Models\Person::where('nat_id', $persona->nat_id)->first();
        } else {
            $persona = \App\Models\PeopleJuridic::where('jur_ruc', $request->documento)->first();
            if (!$persona) {
                return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
            }
            $person = \App\Models\Person::where('jur_id', $persona->jur_id)->first();
        }

        if (!$person) {
            return response()->json(['state' => 1, 'message' => 'Persona no encontrada'], 404);
        }

        $exp = \App\Models\Proceeding::where('exp_demandante', $person->per_id)->get();

        \DB::commit();

        return response()->json(['state' => 0, 'data' => $person, 'persona' => $persona, 'exps' => $exp], 200);
    } catch (Exception $e) {
        \DB::rollback();
        return ['state' => '1', 'exception' => (string) $e];
    }
}
    protected function detalledemandante($doc)
    {
        $persona=null;
        $person=null;
        if (strlen($doc) === 8) {
            // Buscar en la tabla person_natual
            $persona=\App\Models\PeopleNatural::where('nat_dni', $doc)->first();
            $person=\App\Models\Person::where('nat_id',$persona->nat_id)->first();
        } else {
            // Buscar en la tabla person_juridic
            $persona =\App\Models\PeopleJuridic::where('jur_ruc', $doc)->first();
            $person=\App\Models\Person::where('jur_id',$persona->jur_id)->first();
        }
        $address=\App\Models\Address::where('per_id',$person->per_id)
        ->with('district.province.departament')
        ->first();
        $history= \App\Models\History::where('per_id',$person->per_id)
        ->with('exp')
        ->get();
        $pagos=\App\Models\Payment::where('per_id',$person->per_id)
        ->with('exp')
        ->get();;        
    return response()->json(['data' => $persona,'direccion'=>$address,
    'historial'=>$history,'pagos'=>$pagos], 200);
       
    }
}
