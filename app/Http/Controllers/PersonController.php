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
