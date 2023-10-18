<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AudienceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(Request $reques)
    {
        try {
            $audiencias = \App\Models\Audience::with('exp','person.persona', 'person.juridica')->get();

            foreach ($audiencias as $audiencia) {
                if (!is_null($audiencia->person->nat_id)) {
                    $audiencia->tipoPersona = "Natural";
                } elseif (!is_null($audiencia->person->jur_id)) {
                    $audiencia->tipoPersona = "Jurídica";
                } else {
                    $audiencia->tipoPersona = "Desconocida";
                }
            }
            
            return \response()->json(['state' => 0, 'data' => $audiencias], 200);
        } catch (Exception $e) {
            return \response()->json(['state' => 1, 'error' => (string) $e], 500);
        }
    }

    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $audience = \App\Models\Audience::create([
                'per_id' => trim($request->per_id),
                'exp_id' => strtoupper(trim($request->exp_id)),
                'au_fecha' => strtoupper(trim($request->au_fecha)),
                'au_hora' => $request->au_hora,
                'au_lugar' => $request->au_lugar,
                'au_detalles' => $request->au_detalles,
            ]);
            \DB::commit();

            return \response()->json(['state' => 0, 'data' => $audience], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
