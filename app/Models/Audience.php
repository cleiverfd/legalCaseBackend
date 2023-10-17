<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Audience extends Model
{ 
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'au_id';
    protected $fillable = [
        'au_fecha',
        'au_hora',
        'au_lugar',
        'au_detalles',
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
