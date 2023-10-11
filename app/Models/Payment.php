<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'pa_id';
    protected $fillable = [
        'pa_fecha_hora',
        'pa_monto',
        'pa_concepto',
        'pa_metodo_pago',
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
