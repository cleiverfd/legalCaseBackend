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
        'per_id',
        'exp_id',
        'abo_id'
    ];
    protected $dates = ['deleted_at'];
    public function exp()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id', 'exp_id');
    }
}
