<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    protected $guarded = [];
    use SoftDeletes;

//    protected $fillable = [
//        'name', 'ShortName', 'base_unit', 'operator', 'operator_value', 'is_active',
//    ];

    protected $casts = [
        'base_unit' => 'integer',
        'operator_value' => 'float',
        'is_active' => 'integer',

    ];

}
