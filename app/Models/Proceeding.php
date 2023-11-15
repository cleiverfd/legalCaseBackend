<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proceeding extends Model
{
    use HasFactory, SoftDeletes;
    protected $primaryKey = 'exp_id';
    protected $fillable = [
        'exp_numero',
        'exp_fecha_inicio',
        'exp_pretencion',
        'exp_materia',
        'exp_especialidad',
        'exp_monto_pretencion',
        'exp_estado_proceso',
        'exp_demandante',
        'exp_demandado',
        'exp_juzgado',
        'abo_id',
    ];

    protected $dates = ['deleted_at'];
    public function person()
    {
        return $this->belongsTo(Person::class, 'exp_demandante', 'per_id');
    }
    public function demandante()
    {
        return $this->belongsTo(Person::class, 'exp_demandante', 'per_id');
    }
    public function demandado()
    {
        return $this->belongsTo(Person::class, 'exp_demandado', 'per_id');
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'exp_especialidad', 'esp_id');
    }

    public function audiencias()
    {
        return $this->hasMany(Audience::class, 'au_id');
    }
    public function alertas()
    {
        return $this->hasMany(Alert::class, 'ale_id');
    }
    public function persona()
    {
        return $this->belongsTo(Person::class);
    }
    public function abogado()
    {
        return $this->belongsTo(Lawyer::class,'abo_id','abo_id');
    }
}
