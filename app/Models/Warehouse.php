<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    protected $guarded = [];
    use SoftDeletes;

//    protected $fillable = [
//        'name', 'mobile', 'country', 'city', 'email', 'zip',
//    ];

    public function assignedUsers()
    {
        return $this->belongsToMany('App\Models\User');
    }

}
