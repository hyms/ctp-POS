<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'Material';

    public function getTipoMaterial()
    {
        return $this->hasOne(TipoMaterial::class, 'idTipoMaterial', 'fk_idTipoMaterial');
    }

    public function getOrdenImprentaMateriales()
    {
        return $this->hasMany(OrdenImprentaMaterial::class, 'fk_idMaterial', 'idMaterial');
    }
}
