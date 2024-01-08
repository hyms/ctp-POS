<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    protected $guarded = ['id'];
//    protected $fillable = array('name', 'label', 'description','guard_name');
    use SoftDeletes;
}
