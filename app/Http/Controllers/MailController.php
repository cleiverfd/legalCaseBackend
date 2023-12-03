<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\envio;
use Illuminate\Support\Facades\Mail;
use App\Models\Proceeding;
use App\Models\Audience;
use App\Models\Lawyer;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Uuid; 
class MailController extends Controller
{
    public function mail() {
        try {
            \DB::beginTransaction();
            $today = Carbon::now('America/Lima')->startOfDay();
            $expedientes = Proceeding::whereIn('exp_estado_proceso', ['EN TRAMITE', 'EN EJECUCION'])
                ->with('abogado.persona')
                ->get();
            // $alertas 
            foreach ($expedientes as $expediente) {
                $alertasAbogado = $expediente->alertas()
                    ->whereDate('ale_fecha_vencimiento', '>=', $today)
                    ->get();

                foreach ($alertasAbogado as $alerta) {
                    $fechaVencimiento = Carbon::parse($alerta->ale_fecha_vencimiento);
                    $diasFaltantes = $fechaVencimiento->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $alerta->ale_dias_faltantes, 2);
                    if($diasFaltantes<=7){
                        $nombre = ucwords(strtolower(
                            $expediente->abogado->persona->nat_apellido_paterno . ' ' .
                            $expediente->abogado->persona->nat_apellido_materno . ' ' .
                            $expediente->abogado->persona->nat_nombres
                        ));
                        $asunto = 'Recordatorio de Alerta:';
                        $descripcion1 = 'Hola, te recordamos que tienes una alerta del Expediente ' . $expediente->exp_numero ;
                        $descripcion2 = 'Con detalle: ' . $alerta->ale_descripcion ;
                        $descripcion3 = 'Con fecha de vencimiento: ' . $alerta->ale_fecha_vencimiento->format('d-m-Y');
                        
                        $destino = $expediente->abogado->persona->nat_correo;
                        $correo = new envio($nombre, $asunto, $descripcion1,$descripcion2,$descripcion3);
                        $correo->subject('Recordatorio de Alerta-Legal Case UNPRG');
                        Mail::to($destino)->send($correo);                    
                    }
                    
                }
            }
            //audiencias
            foreach ($expedientes as $expediente) {
                $audiencias = $expediente->audiencias()
                    ->whereDate('au_fecha', '>=', $today)
                    ->get();

                foreach ($audiencias as $audiencia) {
                    $fechaAudiencia = Carbon::parse($audiencia->au_fecha);
                    $diasFaltantes = $fechaAudiencia->startOfDay()->diffInDays($today);
                    $porcentaje = round($diasFaltantes / $audiencia->au_dias_faltantes, 2);
                        if($diasFaltantes<=7){
                            $nombre = ucwords(strtolower(
                                $expediente->abogado->persona->nat_apellido_paterno . ' ' .
                                $expediente->abogado->persona->nat_apellido_materno . ' ' .
                                $expediente->abogado->persona->nat_nombres
                            ));
                            $asunto = 'Recordatorio de Audiencia:';
                            $descripcion1 = 'Hola, te recordamos que tienes una audiencia del Expediente :' . $expediente->exp_numero.',
                             a realizarse en:'.$audiencia->au_lugar;
                            $descripcion2 = 'Con detalle: ' . $audiencia->au_detalles;
                            $descripcion3 = 'El dia : ' . $audiencia->au_fecha->format('d-m-Y').'
                             a horas:'.$audiencia->au_hora;
                            $destino = $expediente->abogado->persona->nat_correo;
                            $correo = new envio($nombre, $asunto, $descripcion1,$descripcion2,$descripcion3);
                            $correo->subject('Recordatorio de Audiencia-Legal Case UNPRG');
                            Mail::to($destino)->send($correo);                    
                        }
                }
            }

        \DB::commit();
         return response()->json(['state' =>0 ,'data'=>'OK'], 200);
        } catch (\Exception $e) {
            return response()->json(['state' => 1, 'exception' => (string)$e], 500);
        }
    }
}
