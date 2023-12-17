<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Procesal extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'procesals';
    protected $primaryKey = 'proc_id';
    protected $fillable = [
        'per_id',
        'exp_id',
        'tipo_procesal',
        'tipo_persona'
    ];
    protected $dates = ['deleted_at'];

    public function persona()
    {
        return $this->belongsTo(Person::class, 'per_id', 'per_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'per_id', 'per_id');
    }

    public function audiencias()
    {
        return $this->belongsTo(Audience::class, 'au_id');
    }

    public function historial()
    {
        return $this->belongsTo(History::class, 'his_id');
    }

    public function expedientes()
    {
        return $this->belongsTo(Proceeding::class, 'exp_id');
    }
}
