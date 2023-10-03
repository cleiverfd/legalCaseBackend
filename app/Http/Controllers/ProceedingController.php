<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProceedingController extends Controller
{
    protected function datosgeneralesexpediente(Request $request)
    {
        try {
            \DB::beginTransaction();
          
            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($request->exp_numero)),
                'exp_fecha_inicio' => $request->exp_fecha_inicio,
                'exp_pretencion' =>strtoupper(trim($request->exp_pretencion)),
                'exp_materia' => strtoupper(trim($request->exp_materia)),
                'exp_juzgado' => strtoupper(trim($request->exp_juzgado)),
                'exp_monto_pretencion' => trim($request->exp_monto_pretencion),
            ]);
 
            \DB::commit();
            return \response()->json(['state' => 0, 'data' => $exp, 200]);
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    protected function parteprocesal(Request $request)
    {
        try {
            \DB::beginTransaction();
    
            // Inicializar variables
            $persona = '';
            $direccion = '';
            $per='';
    
            if (($request->tipopersona) == 'NATURAL') {
                $persona = \App\Models\PeopleNatural::create([
                    'nat_dni' => strtoupper(trim($request->pn['nat_dni'])),
                    'nat_apellido_paterno' => strtoupper(trim($request->pn['nat_apellido_paterno'])),
                    'nat_apellido_materno' => strtoupper(trim($request->pn['nat_apellido_materno'])),
                    'nat_nombres' => strtoupper(trim($request->pn['nat_nombres'])),
                    'nat_telefono' => strtoupper(trim($request->pn['nat_telefono'])),
                    'nat_correo' => strtoupper(trim($request->pn['nat_correo']))
                ]);
    
                $per = \App\Models\Person::create([
                    'nat_id' => $persona->nat_id,
                ]);
    
                $direccion = \App\Models\Address::create([
                    'dir_calle_av' => trim($request->dir['dir_calle_av']),
                    'dis_id' => trim($request->dir['dis_id']),
                    'pro_id' => trim($request->dir['prov_id']),
                    'dep_id' => trim($request->dir['dep_id']),
                    'per_id' => trim($per->per_id),
                ]);
            } else {
                $persona = \App\Models\PeopleJuridic::create([
                    'jur_ruc' => strtoupper(trim($request->pj['jur_ruc'])),
                    'jur_razon_social' => strtoupper(trim($request->pj['jur_razon_social'])),
                    'jur_telefono' => strtoupper(trim($request->pj['jur_telefono'])),
                    'jur_correo' => strtoupper(trim($request->pj['jur_correo'])),
                    'jur_rep_legal' => strtoupper(trim($request->pj['jur_rep_legal'])),
                ]);
    
                $per = \App\Models\Person::create([
                    'jur_id' => $persona->jur_id,
                ]);
    
                $direccion = \App\Models\Address::create([
                    'dir_calle_av' => trim($request->dir['dir_calle_av']),
                    'dis_id' => trim($request->dir['dis_id']),
                    'pro_id' => trim($request->dir['prov_id']),
                    'dep_id' => trim($request->dir['dep_id']),
                    'per_id' => trim($per->per_id),
                ]);
            }
    
            \DB::commit();
    
            return response()->json([
                'state' => 0,
                'persona' => $per,
                'direccion' => $direccion,
                'people' => $persona
            ], 200);
    
        } catch (Exception $e) {
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
    
    protected function asignarabogado(Request $request)
    {
        try{
            \DB::beginTransaction();
            /*Actulizar el expediente  asignando la persona y el abogado*/ 
            $exp=\App\Models\Proceeding::find($request->exp_id);
            $exp->exp_demandante= strtoupper(trim($request->exp_demandante));
            $exp->abo_id=$request->abo_id;
            $exp->save();
            \DB::commit();
            return \response()->json(['state'=>0,'data'=>$exp],200);
        }catch(Exception $e){
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
