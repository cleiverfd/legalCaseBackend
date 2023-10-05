<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\{
    LawyerResource
};
use Uuid;

class LawyerController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }

    //Obtener todos los datos
    protected function index(Request $request){
        $Lawyer= \App\Models\Lawyer::OrderBy('created_at','DESC')->with('persona')->get();
        $data = LawyerResource::collection($Lawyer);

        return \response()->json(['data'=>$data],200);
    }  
    protected function registrar(Request $request)
    {
        try {
            \DB::beginTransaction();
          
            $persona = \App\Models\PeopleNatural::create([
                'nat_dni'=>trim($request->nat_dni),
                'nat_apellido_paterno'=>strtoupper(trim($request->nat_apellido_paterno)),
                'nat_apellido_materno'=>strtoupper(trim($request->nat_apellido_materno)),
                'nat_nombres'=>strtoupper(trim($request->nat_nombres)),
                'nat_telefono'=>strtoupper(trim($request->nat_telefono)),
                'nat_correo'=>strtoupper(trim($request->nat_correo))
            ]);
           
            $user=\App\Models\User::create([
                'name' => strtoupper(trim($request->nat_apellido_paterno . ' '
                 . $request->nat_apellido_materno .' ' . $request->nat_nombres)),
                'email'=>strtoupper(trim($request->nat_correo)),
                'usu_rol'=>'ABOGADO',
                'per_id'=>$persona->nat_id,
                'password'=> bcrypt(trim($request->nat_dni)),
            ]);
            $abogado=\App\Models\Lawyer::create([
                'abo_carga_laboral'=>0,
                'abo_disponibilidad'=>'LIBRE',
                'nat_id'=>$persona->nat_id,
            ]);
           
            \DB::commit();

            return \response()->json(['state'=>0,'data'=>$abogado],200);
        }catch(Exception $e){
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
 
}
