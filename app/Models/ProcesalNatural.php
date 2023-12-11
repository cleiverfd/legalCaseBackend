<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ProcesalNatural extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'proc_id';
    protected $fillable = [
        'nat_dni',
        'nat_apellido_paterno',
        'nat_apellido_materno',
        'nat_nombres',
        'nat_telefono',
        'nat_correo',
        'tipo_procesal',
        'condicion_procesal',
        'exp_id',
        'dir_id'
    ];
    protected $dates = ['deleted_at'];
    public function direccion()
    {
        return $this->belongsTo(Address::class, 'dir_id', 'dir_id');
    }
}