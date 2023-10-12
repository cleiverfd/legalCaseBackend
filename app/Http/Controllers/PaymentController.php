<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $pay = \App\Models\Payment::create([
                'per_id' => trim($request->per_id),
                'exp_id' => strtoupper(trim($request->exp_id)),
                'pa_fecha_hora' => strtoupper(trim($request->pa_fecha_hora)),
                'pa_monto' => $request->pa_monto,
                'pa_concepto' => $request->pa_concepto,
                'pa_metodo_pago' => $request->pa_metodo_pago,
            ]);
            \DB::commit();

            return \response()->json(['state' => 0, 'data' => $pay], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
