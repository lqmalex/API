<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SkuModel extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;

    protected $table = 'pre_sku';
}
