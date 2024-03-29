<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class product_warehouse extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    protected $table = 'product_warehouse';

//    protected $fillable = [
//        'product_id', 'warehouse_id', 'qte',
//    ];

    protected $casts = [
        'product_id' => 'integer',
        'warehouse_id' => 'integer',
        'qty' => 'double',
    ];

    public function warehouse()
    {
        return $this->belongsTo('App\Models\Warehouse');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function productVariant()
    {
        return $this->belongsTo('App\Models\ProductVariant');
    }

}
