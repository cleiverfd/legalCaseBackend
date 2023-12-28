<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proceeding;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid;
use App\Models\Person;
use App\Models\User;
use App\Models\Lawyer;
use App\Http\Requests\LawyerRequest;
use App\Http\Resources\LawyerResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

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

    // protected function registrar(Request $request)
    // {
    //     try {
    //         DB::beginTransaction();

    //         $persona = \App\Models\Person::create([
    //             'nat_dni' => trim($request->nat_dni),
    //             'nat_apellido_paterno' => strtoupper(trim($request->nat_apellido_paterno)),
    //             'nat_apellido_materno' => strtoupper(trim($request->nat_apellido_materno)),
    //             'nat_nombres' => strtoupper(trim($request->nat_nombres)),
    //             'nat_telefono' => strtoupper(trim($request->nat_telefono)),
    //             'nat_correo' => trim($request->nat_correo)
    //         ]);

    //         $user = \App\Models\User::create([
    //             'name' => strtoupper(trim($request->nat_apellido_paterno . ' '
    //                 . $request->nat_apellido_materno . ' ' . $request->nat_nombres)),
    //             'email' => trim($request->nat_correo),
    //             'usu_rol' => 'ABOGADO',
    //             'per_id' => $persona->per_id,
    //             'password' => bcrypt(trim($request->nat_dni)),
    //         ]);
    //         $abogado = \App\Models\Lawyer::create([
    //             'abo_carga_laboral' => 0,
    //             'abo_disponibilidad' => 'LIBRE',
    //             'per_id' => $persona->per_id,
    //         ]);

    //         DB::commit();

    //         return \response()->json(['state' => 0, 'data' => $abogado], 200);
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         return ['state' => '1', 'exception' => (string) $e];
    //     }
    // }
    public function store(Request $request)
    {
        try {
            // Validación de duplicados para DNI
            $existingDni = Person::withTrashed()
                ->where('nat_dni', $request->input('nat_dni'))
                ->first();

            if ($existingDni) {
                // Si ya existe una persona con el mismo DNI, devuelve un mensaje de error
                return response()->json([
                    'state' => 1,
                    'message' => 'Error al registrar abogado: La dni ya existe.',
                ], 422); // 422 Unprocessable Entity indica un error de validación
            }

            // Validación de duplicados para correo electrónico
            $existingEmail = Person::withTrashed()
                ->where('nat_correo', strtolower($request->input('nat_correo')))
                ->first();

            if ($existingEmail) {
                // Si ya existe una persona con el mismo correo electrónico, devuelve un mensaje de error
                return response()->json([
                    'state' => 1,
                    'message' => 'Error al registrar abogado: el correo electronico ya existe.',
                ], 422); // 422 Unprocessable Entity indica un error de validación
            }

            DB::beginTransaction();

            // Creación de la persona
            $persona = Person::create([
                'nat_dni' => $request->input('nat_dni'),
                'nat_apellido_paterno' => ucwords(strtolower($request->input('nat_apellido_paterno'))),
                'nat_apellido_materno' => ucwords(strtolower($request->input('nat_apellido_materno'))),
                'nat_nombres' => ucwords(strtolower($request->input('nat_nombres'))),
                'nat_telefono' => $request->input('nat_telefono'),
                'nat_correo' => strtolower($request->input('nat_correo')),
            ]);

            // Creación del usuario
            User::create([
                'name' => $request->input('nat_nombres'),
                'email' => $request->input('nat_correo'),
                'usu_rol' => 'ABOGADO',
                'per_id' => $persona->per_id,
                'password' => bcrypt($request->input('nat_dni')),
            ]);

            // Creación del abogado
            $abogado = Lawyer::create([
                'abo_carga_laboral' => 0,
                'abo_disponibilidad' => 'LIBRE',
                'per_id' => $persona->per_id,
            ]);

            DB::commit();

            // Recarga los modelos para obtener los datos actualizados de la base de datos
            $persona = $persona->fresh();
            $abogado = $abogado->fresh();

            // Combina los datos de persona y abogado en un solo objeto JSON
            $jsonData = array_merge($persona->toArray(), $abogado->toArray());

            return response()->json([
                'state' => 0,
                'message' => 'Abogado registrado exitosamente',
                'data' => $jsonData
            ], 201);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'state' => 1,
                'message' => 'Error al registrar abogado',
                'exception' => $e->getMessage()
            ], 500);
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
            $user->name = ucwords(strtolower(trim($request->nat_nombres.' '.$request->nat_apellido_paterno . ' ' . $request->nat_apellido_materno )));
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

    protected function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $abogado = Lawyer::findOrFail($id);
            $personaId = $abogado->per_id;

            $abogado->delete();
            User::where('per_id', $personaId)->delete();
            Person::findOrFail($personaId)->delete();

            DB::commit();

            return response()->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => 'Abogado no encontrado'], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Error al eliminar el abogado', 'exception' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    protected function expedientes(Request $request)
    {
        $procedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
            ->where('abo_id', $request->abo_id)
            ->with('procesal.persona', 'pretension', 'materia')
            ->get();

        $formattedData = [];
        foreach ($procedings as $proceeding) {
            $processedProcesals = $this->formatProcesalData($proceeding->procesal);
            $commonData = [
                'exp_id' => $proceeding->exp_id,
                'numero' => $proceeding->exp_numero,
                'fecha_inicio' => $proceeding->exp_fecha_inicio,
                'pretencion' => $proceeding->pretension->pre_nombre,
                'materia' => $proceeding->materia->mat_nombre,
                'monto_pretencion' => $proceeding->exp_monto_pretencion,
                'estado_proceso' => ucwords(strtolower($proceeding->exp_estado_proceso)),
                'multiple' => $proceeding->multiple,
                'procesal' => $processedProcesals,
            ];
            $formattedData[] = $commonData;
        }

        return response()->json(['data' => $formattedData], 200);
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

    protected function formatProcesalData($procesal)
    {
        $processedProcesals = [];

        foreach ($procesal as $procesalItem) {
            $data = [
                'proc_id' => $procesalItem->proc_id,
                'per_id' => $procesalItem->per_id,
                'tipo_procesal' => $procesalItem->tipo_procesal,
                'tipo_persona' => $procesalItem->tipo_persona,
            ];

            if ($procesalItem->tipo_persona === 'NATURAL') {
                $data = array_merge($data, [
                    'dni' => $procesalItem->persona->nat_dni,
                    'apellido_paterno' => ucwords(strtolower($procesalItem->persona->nat_apellido_paterno)),
                    'apellido_materno' => ucwords(strtolower($procesalItem->persona->nat_apellido_materno)),
                    'nombres' => ucwords(strtolower($procesalItem->persona->nat_nombres)),
                    'telefono' => $procesalItem->persona->nat_telefono,
                    'correo' => strtolower($procesalItem->persona->nat_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            } else {
                $data = array_merge($data, [
                    'ruc' => $procesalItem->persona->jur_ruc,
                    'razon_social' => ucwords(strtolower($procesalItem->persona->jur_razon_social)),
                    'telefono' => $procesalItem->persona->jur_telefono,
                    'correo' => strtolower($procesalItem->persona->jur_correo),
                    'condicion' => strtolower($procesalItem->persona->per_condicion),
                ]);
            }

            $processedProcesals[] = $data;
        }

        return $processedProcesals;
    }
}
