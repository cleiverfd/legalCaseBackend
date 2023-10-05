<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uuid;
class ProceedingController extends Controller
{
    public function __construct()
    {
         $this->middleware('auth');
    }
protected function index(Request $request)
{
    $proceedings = \App\Models\Proceeding::orderBy('created_at', 'DESC')
        ->with('person')
        ->get();

    $data = $proceedings->map(function ($proceeding) {
        $person = $proceeding->person;
        $personData = null;
        $type = null;
        if ($person) {
            if ($person->nat_id !== null) {
                $personData = $person->persona;
                $type = 'natural';
            } elseif ($person->jur_id !== null) {
                $personData = $person->juridica;
                $type = 'juridica';
            }
        }
        return array_merge($proceeding->toArray(), [
            'person_data' => $personData ? $personData->toArray() : null,
            'type' => $type,
        ]);
    });

    return response()->json(['data' => $data], 200);
}

    protected function registrarcaso(Request $request)
    {
        try {
            \DB::beginTransaction();
          
            $exp = \App\Models\Proceeding::create([
                'exp_numero' => strtoupper(trim($request->exp['exp_numero'])),
                'exp_fecha_inicio' => $request->exp['exp_fecha_inicio'],
                'exp_pretencion' =>strtoupper(trim($request->exp['exp_pretencion'])),
                'exp_materia' => strtoupper(trim($request->exp['exp_materia'])),
                'exp_juzgado' => strtoupper(trim($request->exp['exp_juzgado'])),
                'exp_monto_pretencion' => trim($request->exp['exp_monto_pretencion']),
                'exp_estado_proceso' =>'EN PROCESO',
            ]);
           // Inicializar variables
            $persona = null;
            $direccion = null;
            $per = null;
    
            // Verificar si la persona ya existe
            if ($request->tipopersona == 'NATURAL') {
                $persona = \App\Models\PeopleNatural::updateOrCreate(
                    ['nat_dni' => strtoupper(trim($request->pn['nat_dni']))],
                    [
                        'nat_apellido_paterno' => strtoupper(trim($request->pn['nat_apellido_paterno'])),
                        'nat_apellido_materno' => strtoupper(trim($request->pn['nat_apellido_materno'])),
                        'nat_nombres' => strtoupper(trim($request->pn['nat_nombres'])),
                        'nat_telefono' => strtoupper(trim($request->pn['nat_telefono'])),
                        'nat_correo' => strtoupper(trim($request->pn['nat_correo']))
                    ]
                );
    
                $per = \App\Models\Person::updateOrCreate(
                    ['nat_id' => $persona->nat_id],
                    []
                );
            } else {
                $persona = \App\Models\PeopleJuridic::updateOrCreate(
                    ['jur_ruc' => strtoupper(trim($request->pj['jur_ruc']))],
                    [
                        'jur_razon_social' => strtoupper(trim($request->pj['jur_razon_social'])),
                        'jur_telefono' => strtoupper(trim($request->pj['jur_telefono'])),
                        'jur_correo' => strtoupper(trim($request->pj['jur_correo'])),
                        'jur_rep_legal' => strtoupper(trim($request->pj['jur_rep_legal'])),
                    ]
                );
    
                $per = \App\Models\Person::updateOrCreate(
                    ['jur_id' => $persona->jur_id],
                    []
                );
            }
            // Obtener el ID de la persona
            $perId = $per->per_id;
    
            // Insertar o actualizar dirección
            $direccion = \App\Models\Address::updateOrCreate(
                ['per_id' => $perId],
                [
                    'dir_calle_av' => trim($request->dir['dir_calle_av']),
                    'dis_id' => trim($request->dir['dis_id']),
                    'pro_id' => trim($request->dir['pro_id']),
                    'dep_id' => trim($request->dir['dep_id']),
                ]
            );
    
            /*Actulizar el expediente  asignando laersona y el abogado*/ 
            $EX=\App\Models\Proceeding::find($exp->exp_id);
            $EX->exp_demandante= strtoupper(trim($perId));
            $EX->abo_id=$request->abo_id;
            $EX->save();
            /*ACTULIZAR ESTADO DE ABOGADO */
            $abogado=\App\Models\Lawyer::find($request->abo_id);
            $abogado->abo_disponibilidad='OCUPADO';
            $abogado->abo_carga_laboral=1;
            $abogado->save();
            \DB::commit();

            return \response()->json(['state'=>0,'data'=>$EX],200);
        }catch(Exception $e){
            \DB::rollback();
            return ['state' => '1', 'exception' => (string) $e];
        }
    }
}
