<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesType extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    protected $table = 'sales_type';
//    protected $fillable = [
//        'code', 'name',
//    ];

}
