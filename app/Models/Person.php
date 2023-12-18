<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Person extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'persons';
    protected $primaryKey = 'per_id';
    protected $fillable = [
        'jur_ruc',
        'jur_razon_social',
        'jur_telefono',
        'jur_correo',
        'jur_rep_legal',
        'nat_dni',
        'nat_apellido_paterno',
        'nat_apellido_materno',
        'nat_nombres',
        'nat_telefono',
        'nat_correo',
        'per_condicion',
        'tipo_procesal'
    ];
    protected $dates = ['deleted_at'];
    public function procesal()
    {
        return $this->hasMany(Procesal::class, 'per_id','per_id');
    }
}
