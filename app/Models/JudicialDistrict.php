<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class JudicialDistrict extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'judicial_districts';
    protected $primaryKey = 'judis_id';
    protected $fillable = [
        'judis_nombre'
    ];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
