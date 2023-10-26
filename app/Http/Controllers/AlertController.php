<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;
class AlertController extends Controller
{     public function __construct()
    {
        $this->middleware('auth');
    }
    protected function store(Request $request)
    { 
        try {
            DB::beginTransaction(); 

            $au_fecha = strtoupper(trim($request->ale_fecha_vencimiento));
            $hoy = date('Y-m-d');
            $au_fecha_obj = new DateTime($au_fecha);
            $hoy_obj = new DateTime($hoy);

            // Calcula la diferencia de días
            $interval = $au_fecha_obj->diff($hoy_obj);
            $dias_faltantes = $interval->days;

            $alert = \App\Models\Alert::create([
                'ale_fecha_vencimiento'=>$request->ale_fecha_vencimiento,
                'ale_descripcion'=>$request->ale_descripcion,
                'exp_id'=>$request->exp_id,
                'cal_id'=>$request->cal_id,
               'ale_dias_faltantes'=> $dias_faltantes,
            ]);


            DB::commit();

            return response()->json(['state' => 0, 'data' => $alert], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }
}
