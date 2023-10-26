<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alert extends Model
{ 
    use HasFactory,SoftDeletes;
    protected $table = 'alerts';
    protected $primaryKey = 'ale_id';
    protected $fillable = [
        'ale_fecha_vencimiento',
        'ale_descripcion',
        'exp_id',
        'ale_dias_faltantes',
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
