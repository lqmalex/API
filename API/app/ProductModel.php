<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    const STATUS_UP = 1;
    const STATUS_DEL = 0;
    const STATUS_DOWN = 2;

    protected $table = 'pre_product';

    public function tag(){
        return $this->hasMany('App\TagModel','product_id');
    }

    public function sku(){
        return $this->hasMany('App\SkuModel','product_id');
    }
}
