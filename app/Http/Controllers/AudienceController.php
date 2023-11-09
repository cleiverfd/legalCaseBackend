<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Audience;
use Exception;
use DateTime;

class AudienceController extends Controller
{
    protected $db;

    public function __construct(DB $db)
    {
        $this->middleware('auth:api');
        $this->db = $db;
    }

    protected function index()
    {
        $audiencias = Audience::with('exp', 'person.persona', 'person.juridica')->get();

        $result = $audiencias->map(function ($audiencia) {
            $tipoPersona = $this->getTipoPersona($audiencia->person);

            $response = [
                'aud_id' => $audiencia->au_id,
                'aud_fecha' => $audiencia->au_fecha->format('d-m-Y'),
                'aud_hora' => $audiencia->au_hora,
                'aud_lugar' => $audiencia->au_lugar,
                'aud_detalles' => $audiencia->au_detalles,
                'per_id' => $audiencia->per_id,
                'exp_id' => $audiencia->exp->exp_id,
                'exp_numero' => $audiencia->exp->exp_numero,
                'tipo_persona' => $tipoPersona,
            ];

            if ($tipoPersona === "Natural") {
                $response += [
                    'nat_id' => $audiencia->person->nat_id,
                    'nat_dni' => $audiencia->person->persona->nat_dni,
                    'nat_apellido_paterno' => $audiencia->person->persona->nat_apellido_paterno,
                    'nat_apellido_materno' => $audiencia->person->persona->nat_apellido_materno,
                    'nat_nombres' => $audiencia->person->persona->nat_nombres,
                    'nat_telefono' => $audiencia->person->persona->nat_telefono,
                    'nat_correo' => $audiencia->person->persona->nat_correo,
                ];
            } elseif ($tipoPersona === "Jurídica") {
                $response += [
                    'jur_id' => $audiencia->person->juridica->jur_id,
                    'jur_ruc' => $audiencia->person->juridica->jur_ruc,
                    'jur_razon_social' => $audiencia->person->juridica->jur_razon_social,
                ];
            }

            return $response;
        });

        return response()->json(['data' => $result], 200);
    }

    protected function store(Request $request)
    {
        try {
            DB::beginTransaction(); 

            $au_fecha = strtoupper(trim($request->au_fecha));
            $hoy = date('Y-m-d'); // Obtiene la fecha actual en el formato 'Año-Mes-Día'
            $au_fecha_obj = new DateTime($au_fecha);
            $hoy_obj = new DateTime($hoy);

            // Calcula la diferencia de días
            $interval = $au_fecha_obj->diff($hoy_obj);
            $dias_faltantes = $interval->days;

            $audience = Audience::create([
                'per_id' => trim($request->per_id),
                'exp_id' => strtoupper(trim($request->exp_id)),
                'au_fecha' => $au_fecha,
                'au_link'=>$request->au_link,
                'au_hora' => $request->au_hora,
                'au_lugar' => $request->au_lugar,
                'au_detalles' => $request->au_detalles,
                'au_dias_faltantes' => $dias_faltantes, // Guarda la cantidad de días faltantes
            ]);


            DB::commit();

            return response()->json(['state' => 0, 'data' => $audience], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['state' => 1, 'error' => $e->getMessage()], 500);
        }
    }


    protected function getTipoPersona($person)
    {
        if (!is_null($person->nat_id)) {
            return "Natural";
        } elseif (!is_null($person->jur_id)) {
            return "Jurídica";
        } else {
            return "Desconocida";
        }
    }
}
