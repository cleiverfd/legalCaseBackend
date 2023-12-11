<?php

namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class History extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'his_id';
    protected $fillable = [
        'his_fecha_hora',
        'his_medio_comuniacion',
        'his_detalle',
        'exp_id',
        'procesal_natural_id',
        'procesal_juridic_id',

    ];
    protected $dates = ['deleted_at'];

    public function expediente()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id');
    }

    public function persona()
    {
        return $this->belongsTo(ProcesalNatural::class, 'procesal_natural_id');
    }
    public function empresa()
    {
        return $this->belongsTo(PeopleJuridic::class, 'procesal_juridic_id');
    }
}
