<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{
    LawyerResource
};
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
        $Lawyer = \App\Models\Lawyer::OrderBy('created_at', 'DESC')->with('persona')->get();
        $data = LawyerResource::collection($Lawyer);

        return \response()->json(['data' => $data], 200);
    }
    protected function show(Request $request)
    {
        $Lawyer = \App\Models\Lawyer::where('abo_id',$request->abo_id )->with('persona')->first();
         $data = LawyerResource::collection([$Lawyer]);
        return \response()->json(['data' => $data], 200);
    }
    
    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();

            $persona = \App\Models\PeopleNatural::create([
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

            $persona = \App\Models\PeopleNatural::find($request->nat_id);
            $persona->nat_dni = trim($request->nat_dni); 
            $persona->nat_apellido_paterno = strtoupper(trim($request->nat_apellido_paterno));
            $persona->nat_apellido_materno = strtoupper(trim($request->nat_apellido_materno));
            $persona->nat_nombres = strtoupper(trim($request->nat_nombres));
            $persona->nat_telefono =strtoupper(trim($request->nat_telefono));
            $persona->nat_correo = trim($request->nat_correo);
            $persona->save();
           //actulizar  su usuario 
            $user = \App\Models\User::where('per_id', $persona->nat_id)->first();
            $user->name = strtoupper(trim($request->nat_apellido_paterno . ' ' . $request->nat_apellido_materno . ' ' . $request->nat_nombres));
            $user->email = trim($request->nat_correo);
            $user->usu_rol = 'ABOGADO';
            $user->password = bcrypt(trim($request->nat_dni));
            $user->save();
            \DB::commit();
            return \response()->json(['state' => 0, 'data'=>'actulizado correcto'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }

    protected function eliminar($id)
    {
        try {
            \DB::beginTransaction();

            // Buscar el abogado por ID
            $abogado = \App\Models\Lawyer::find($id);

            if (!$abogado) {
                return \response()->json(['message' => 'Abogado no encontrado'], 404);
            }

            // Eliminar el registro de abogado
            $abogado->delete();

            // También puedes eliminar la persona natural y el usuario si es necesario
            // $abogado->persona->delete();
            // $abogado->user->delete();

            \DB::commit();

            return \response()->json(['message' => 'Abogado eliminado'], 200);
        } catch (Exception $e) {
            \DB::rollback();
            return \response()->json(['message' => 'Error al eliminar el abogado', 'exception' => $e->getMessage()], 500);
        }
    }
}
