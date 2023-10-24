<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'persons';
    protected $primaryKey = 'per_id';
    protected $fillable = [
        'nat_id',
        'jur_id',
    ];
    protected $dates = ['deleted_at'];

    public function persona()
    {
        return $this->belongsTo(PeopleNatural::class, 'nat_id', 'nat_id');
    }

    public function natural()
    {
        return $this->belongsTo(PeopleNatural::class, 'nat_id', 'nat_id');
    }

    public function juridica()
    {
        return $this->belongsTo(PeopleJuridic::class, 'jur_id', 'jur_id');
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

    public function pagos()
    {
        return $this->belongsTo(Payment::class, 'pa_id');
    }
}
