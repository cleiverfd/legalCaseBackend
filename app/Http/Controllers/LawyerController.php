<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{
    LawyerResource
};
use App\Models\Proceeding;
use App\Models\Audience;
use App\Models\Lawyer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;

class LawyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request)
    {
        $lawyers = \App\Models\Lawyer::orderBy('created_at', 'DESC')->with('persona')->get();

        $data = $lawyers->map(function ($lawyer) {
            return [
                'nat_correo' => $lawyer->persona->nat_correo,
                'abo_id' => $lawyer->abo_id,
                'abo_carga_laboral' => $lawyer->abo_carga_laboral,
                'abo_disponibilidad' => $lawyer->abo_disponibilidad,
                'per_id' => $lawyer->persona->per_id,
                'nat_dni' => $lawyer->persona->nat_dni,
                'nat_apellido_paterno' => ucwords(strtolower($lawyer->persona->nat_apellido_paterno)),
                'nat_apellido_materno' => ucwords(strtolower($lawyer->persona->nat_apellido_materno)),
                'nat_nombres' => ucwords(strtolower($lawyer->persona->nat_nombres)),
                'nat_telefono' => $lawyer->persona->nat_telefono,
            ];
        });

        return \response()->json(['data' => $data], 200);
    }

    protected function show(Request $request)
    {
        $Lawyer = \App\Models\Lawyer::where('abo_id', $request->abo_id)->with('persona')->first();
        $data = LawyerResource::collection([$Lawyer]);
        return \response()->json(['data' => $data], 200);
    }

    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $persona = \App\Models\Person::create([
                'nat_dni' => trim($request->nat_dni),
                'nat_apellido_paterno' => strtoupper(trim($request->nat_apellido_paterno)),
                'nat_apellido_materno' => strtoupper(trim($request->nat_apellido_materno)),
                'nat_nombres' => strtoupper(trim($request->nat_nombres)),
                'nat_telefono' => strtoupper(trim($request->nat_telefono)),
                'nat_correo' => trim($request->nat_correo)
            ]);

            $user = \App\Models\User::create([
                'name' => strtoupper(trim($request->nat_apellido_paterno . ' '
                    . $request->nat_apellido_materno . ' ' . $request->nat_nombres)),
                'email' => trim($request->nat_correo),
                'usu_rol' => 'ABOGADO',
                'per_id' => $persona->nat_id,
                'password' => bcrypt(trim($request->nat_dni)),
            ]);
            $abogado = \App\Models\Lawyer::create([
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'nat_id' => $persona->nat_id,
            ]);

            \DB::commit();

            return \response()->json(['state' => 0, 'data' => $abogado], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function update(Request $request)
    {
        try {
            \DB::beginTransaction();
            $abogado = \App\Models\Lawyer::find($request->abo_id);
            $persona = \App\Models\Person::find($abogado->per_id);
            $persona->nat_dni = trim($request->nat_dni);
            $persona->nat_apellido_paterno = strtoupper(trim($request->nat_apellido_paterno));
            $persona->nat_apellido_materno = strtoupper(trim($request->nat_apellido_materno));
            $persona->nat_nombres = strtoupper(trim($request->nat_nombres));
            $persona->nat_telefono = strtoupper(trim($request->nat_telefono));
            $persona->nat_correo = trim($request->nat_correo);
            $persona->save();
            //actulizar  su usuario 
            $user = \App\Models\User::where('per_id', $persona->per_id)->first();
            $user->name = strtoupper(trim($request->nat_apellido_paterno . ' ' . $request->nat_apellido_materno . ' ' . $request->nat_nombres));
            $user->email = trim($request->nat_correo);
            $user->usu_rol = 'ABOGADO';
            $user->password = bcrypt(trim($request->nat_dni));
            $user->save();
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => 'actulizado correcto'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function eliminar($id)
    {
        try {
            \DB::beginTransaction();

            $abogado = \App\Models\Lawyer::find($id);
            if (!$abogado) {
                return \response()->json(['message' => 'Abogado no encontrado'], 404);
            }
            $abogado->delete();
            \DB::commit();

            return \response()->json(['message' => 'Abogado eliminado'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return \response()->json(['message' => 'Error al eliminar el abogado', 'exception' => $e->getMessage()], 500);
        }
    }
    protected function expedientes(Request $request)
    {
        try {
            \DB::beginTransaction();
            $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
                ->where('abo_id', $request->abo_id)
                ->with('person.juridica', 'person.persona', 'pretension', 'materia')
                ->get();
            $data = $proceedings->map(function ($proceeding) {
                $procesal = null;
                $tipo_persona = null;
                if ($proceeding) {
                    if ($proceeding->exp_demandante !== null) {
                        $person = $proceeding->demandante;
                        $procesal = 'demandante';
                    } elseif ($proceeding->exp_demandado !== null) {
                        $person = $proceeding->demandado;
                        $procesal = 'demandado';
                    }
                }
                $fecha_inicio = $proceeding->exp_fecha_inicio;
                $fecha_formateada = date('d-m-Y', strtotime($fecha_inicio));
                $commonData = [
                    'exp_id' => $proceeding->exp_id,
                    'numero' => $proceeding->exp_numero,
                    'fecha_inicio' => $fecha_formateada,
                    'pretencion' => ucwords(strtolower($proceeding->pretension->pre_nombre)),
                    'materia' => ucwords(strtolower($proceeding->materia->mat_nombre)),
                    'especialidad' => ucwords(strtolower($proceeding->exp_especialidad)),
                    'monto_pretencion' => $proceeding->exp_monto_pretencion,
                    'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                    'procesal' => $procesal
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
                        'dni' => $personData->nat_dni,
                        'apellido_paterno' => ucwords(strtolower($personData->nat_apellido_paterno)),
                        'apellido_materno' => ucwords(strtolower($personData->nat_apellido_materno)),
                        'nombres' => ucwords(strtolower($personData->nat_nombres)),
                        'telefono' => $personData->nat_telefono,
                        'correo' => strtolower($personData->nat_correo),
                    ];
                } elseif ($tipo_persona === 'juridica') {
                    $personDataArray = [
                        'ruc' => ucwords(strtolower($personData->jur_ruc)),
                        'razon_social' => ucwords(strtolower($personData->jur_razon_social)),
                        'telefono' => $personData->jur_telefono,
                        'correo' => strtolower($personData->jur_correo),
                    ];
                } else {
                    $personDataArray = [];
                }

                return array_merge($commonData, $personDataArray, ['tipo_persona' => $tipo_persona]);
            });

            return response()->json(['data' => $data], 200);

            \DB::commit();
            return \response()->json(['state' => 0, 'data' => ''], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function alertas(Request $request)
    {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::where('abo_id', $request->abo_id)
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->get();
            $alertas = collect();
            foreach ($expedientes as $expediente) {
                $alertasAbogado = $expediente->alertas()
                    ->whereDate('ale_fecha_vencimiento', '>=', $today)
                    ->get();

                foreach ($alertasAbogado as $alerta) {
                    $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                    $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
                    $alertas->push([
                        'ale_fecha_vencimiento' => $alerta->ale_fecha_vencimiento->toDateString(), // Obtén la fecha en formato 'Y-m-d'
                        'ale_descripcion' => $alerta->ale_descripcion,
                        'fecha' => $alerta->ale_fecha_vencimiento->format('d-m-Y'),
                        'ale_expediente' => $alerta->expediente ? $alerta->expediente->exp_numero : 'N/A',
                        'ale_porcentaje' => $porcentaje,
                        'ale_exp_id'  => $alerta->expediente ? $alerta->expediente->exp_id : 'N/A',
                        'ale_id' => $alerta->ale_id
                    ]);
                }
            }
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $alertas], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function audiencias(Request $request)
    {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::where('abo_id', $request->abo_id)
                ->whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->get();

            $audienciasFaltantes = collect();

            foreach ($expedientes as $expediente) {
                $audiencias = $expediente->audiencias()
                    ->whereDate('au_fecha', '>=', $today)
                    ->get();

                foreach ($audiencias as $audiencia) {
                    $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
                    $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);
                    $audienciasFaltantes->push([
                        'au_fecha' =>  $fechaAudiencia->toDateString(),
                        'au_hora' => $audiencia->au_hora,
                        'fecha' => $audiencia->au_fecha->format('d-m-Y'),
                        'au_lugar' => $audiencia->au_lugar,
                        'au_detalles' => $audiencia->au_detalles,
                        'porcentaje' => $porcentaje,
                        'exp_id' => $audiencia->exp_id,
                        'exp_numero' => $expediente->exp_numero,
                        'id' => $audiencia->au_id,
                    ]);
                }
            }
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $audienciasFaltantes], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function changeOfLawyer(Request $request)
    {
        try {
            $expedientesAsociados = $request->input('expedientes', []);
            $abogadoAnteriorId = $request->input('abogado_actual');
            $nuevoAbogadoId = $request->input('abogado_nuevo');

            // Validar que ambos abogados existan
            $abogadoAnterior = Lawyer::findOrFail($abogadoAnteriorId);
            $nuevoAbogado = Lawyer::findOrFail($nuevoAbogadoId);

            // Actualizar los expedientes
            Proceeding::whereIn('exp_id', $expedientesAsociados)
                ->where('abo_id', $abogadoAnterior->abo_id)
                ->update(['abo_id' => $nuevoAbogado->abo_id]);

            // Transferir la carga laboral del abogado anterior al nuevo abogado
            $cargaLaboralAnterior = $abogadoAnterior->abo_carga_laboral;
            // Actualizar el abogado anterior
            $abogadoAnterior->abo_carga_laboral = 0; // Carga laboral se establece a 0
            $abogadoAnterior->abo_disponibilidad = 'LIBRE'; // Disponibilidad se establece a 'libre'
            $abogadoAnterior->save();

            // Actualizar el nuevo abogado
            $nuevoAbogado->abo_carga_laboral += $cargaLaboralAnterior; // Transferir la carga laboral
            if ($nuevoAbogado->abo_disponibilidad === 'LIBRE') {
                // Cambiar la disponibilidad solo si está libre
                $nuevoAbogado->abo_disponibilidad = 'OCUPADO';
            }
            $nuevoAbogado->save();


            return response()->json(['message' => 'Cambio de abogado realizado con éxito.', 'state' => 0], 200);
        } catch (\Exception $e) {
            // Manejar cualquier error
            return response()->json(['error' => $e->getMessage(), 'state' => 1], 500);
        }
    }
}
