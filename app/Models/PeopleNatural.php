<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PeopleNatural extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'nat_id';
    protected $fillable = [
        'nat_dni',
        'nat_apellido_paterno',
        'nat_apellido_materno',
        'nat_nombres',
        'nat_telefono',
        'nat_correo'
    ];
    protected $dates = ['deleted_at'];
    public function abogado()
    {
        return $this->belongsTo(Lawyer::class, 'nat_id', 'nat_id');
    }
}
