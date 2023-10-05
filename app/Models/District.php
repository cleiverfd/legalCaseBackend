<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class District extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'districts';
    protected $primaryKey = 'dis_id';
    protected $fillable = [
        'dis_nombre',
        'pro_id'];
    protected $dates = ['deleted_at']; 
}
