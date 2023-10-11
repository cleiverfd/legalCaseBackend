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
    protected function show($doc)
    {
        // Buscar el expediente por su ID
        $person= \App\Models\Proceeding::with('person')
        ->with('specialty.instance.judicialdistrict')
            ->find($id);

       
        // Devuelve la respuesta JSON con los detalles del expediente
        return response()->json(['data' => $data], 200);
    }

}
