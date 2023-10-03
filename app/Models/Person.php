<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Person extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'persons';
    protected $primaryKey = 'per_id';
    protected $fillable = [
        'nat_id',
        'jur_id',  
    ];
    protected $dates = ['deleted_at'];
}
