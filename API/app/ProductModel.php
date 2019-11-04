<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductModel extends Model
{
    protected $table = 'pre_product';

    public function tag(){
        return $this->hasMany('App\tagModel','product_id');
    }

    public function sku(){
        return $this->hasMany('App\skuModel','product_id');
    }
}
