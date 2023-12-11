<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PeopleJuridic extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'jur_id';
    protected $fillable = [
        'jur_ruc',
        'jur_razon_social',
        'jur_telefono',
        'jur_correo',
        'jur_rep_legal',
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
