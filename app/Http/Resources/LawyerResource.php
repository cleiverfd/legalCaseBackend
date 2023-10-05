<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LawyerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'abo_id'=> $this->abo_id,
            'abo_carga_laboral' =>$this->abo_carga_laboral,
            'abo_disponibilidad'=>$this->abo_disponibilidad,
            'nat_id'=>$this->nat_id,
            'nat_dni'=>$this->persona->nat_dni,
            'nat_apellido_paterno'=>$this->persona->nat_apellido_paterno,
            'nat_apellido_materno'=>$this->persona->nat_apellido_materno,
            'nat_nombres'=>$this->persona->nat_nombres,
            'nat_telefono'=>$this->persona->nat_telefono,
            'nat_correo'=>$this->persona->nat_correo,
           
        ];
    }
}
