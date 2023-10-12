<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;
class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $his = \App\Models\History::create([
                'per_id' => trim($request->per_id),
                'exp_id' => strtoupper(trim($request->exp_id)),
                'his_fecha_hora' => strtoupper(trim($request->his_fecha_hora)),
                'his_medio_comuniacion' => $request->his_medio_comunicacion,
                'his_detalle' => $request->his_detalle,
                
            ]);
            \DB::commit();

            return \response()->json(['state' => 0, 'data' => $his], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
