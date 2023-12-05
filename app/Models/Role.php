<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as roleModal;

class Role extends roleModal
{
    use SoftDeletes;
    protected $guarded = ['id'];
//    protected $fillable = ['name','status', 'label', 'description'];

}
