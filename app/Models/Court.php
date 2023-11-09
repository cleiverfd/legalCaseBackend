<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Court extends Model
{
    use HasFactory,SoftDeletes;
    protected $primaryKey = 'co_id';
    protected $fillable = [
        'co_nombre',
    ];
    protected $dates = ['deleted_at'];
}
