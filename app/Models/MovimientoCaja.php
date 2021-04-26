<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MovimientoCaja extends Model
{
    protected $table = 'movimientoCajas';
    public static $tables = 'movimientoCajas';
    protected $guarded = [];
    use SoftDeletes;
}
