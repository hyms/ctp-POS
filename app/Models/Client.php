<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    protected $guarded = [];
    use SoftDeletes;
//    protected $fillable = [
//        'name', 'code', 'adresse', 'email', 'phone', 'country', 'city','tax_number'
//
//    ];

    protected $casts = [
        'code' => 'integer',
    ];
}
