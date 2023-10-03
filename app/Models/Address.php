<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Address extends Model
{ 
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'dir_id';
    protected $fillable = [
        'dir_calle_av',
        'dis_id',
        'prov_id',
        'dep_id',
        'per_id',
    ];
    protected $dates = ['deleted_at'];
}
