<?php

namespace App\Models;

use Spatie\Permission\Models\Role as roleModal;

class Role extends roleModal
{
    protected $guarded = ['id'];
//    protected $fillable = ['name','status', 'label', 'description'];

}
