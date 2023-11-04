<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Exception;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function index(Request $request)
    {
        try {
            $pagos = \App\Models\Payment::with('person.juridica', 'person.persona', 'expediente')->get();

            $data = $pagos->map(function ($pago) {
                $person = $pago->person;
                $expediente = $pago->expediente;
                $tipo_persona = null;

                $commonData = [
                    'pa_id' => $pago->pa_id,
                    'pa_fecha_hora' => $pago->pa_fecha_hora,
                    'pa_monto' => $pago->pa_monto,
                    'pa_concepto' => $pago->pa_concepto,
                    'pa_metodo_pago' => $pago->pa_metodo_pago,
                    'exp_id' => $pago->exp_id,
                    'exp_numero' => $expediente->exp_numero
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
                        'nat_dni' => $personData->nat_dni,
                        'nat_apellido_paterno' => $personData->nat_apellido_paterno,
                        'nat_apellido_materno' => $personData->nat_apellido_materno,
                        'nat_nombres' => $personData->nat_nombres,
                        'nat_telefono' => $personData->nat_telefono,
                        'nat_correo' => $personData->nat_correo,
                    ];
                } elseif ($tipo_persona === 'juridica') {
                    $personDataArray = [
                        'nat_ruc' => $personData->jur_ruc,
                        'nat_razon_social' => $personData->jur_razon_social,
                        'nat_telefono' => $personData->jur_telefono,
                        'nat_correo' => $personData->jur_correo,
                    ];
                } else {
                    $personDataArray = [];
                }
                return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
            });
            return response()->json(['data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['state' => 1, 'exception' => (string) $e], 500);
        }
    }

    protected function store(Request $request)
    {
        try {
            $transaction = DB::transaction(function () use ($request) {
                $paymentData = [
                    'per_id' => trim($request->per_id),
                    'exp_id' => strtoupper(trim($request->exp_id)),
                    'pa_fecha_hora' => strtoupper(trim($request->pa_fecha_hora)),
                    'pa_monto' => $request->pa_monto,
                    'pa_concepto' => $request->pa_concepto,
                    'pa_metodo_pago' => $request->pa_metodo_pago,
                ];

                $payment = \App\Models\Payment::create($paymentData);

                // Si todo está bien dentro de la transacción, no es necesario hacer un commit explícito.

                return $payment;
            });

            return response()->json(['state' => 0, 'data' =>  $transaction], 200);
        } catch (Exception $e) {
            DB::rollBack(); // Realiza un rollback en caso de excepción.
            return response()->json(['state' => 1, 'exception' => $e->getMessage()], 500);
        }
    }

}
