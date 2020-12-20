<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acabados extends Model
{
    protected $table = 'acabados';

    public function getParametros()
    {
        return $this->hasMany(ParametroAcabado::class, 'fk_idAcabado', 'idAcabados');
    }

    public function getTrabajos()
    {
        return $this->hasMany(TrabajoAcabado::class, 'idAcabado', 'idAcabados');
    }
}
