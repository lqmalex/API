<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagModel extends Model
{
    const STATUS_NO = 0;
    const STATUS_YES = 1;

    protected $table = 'pre_product_tag';

}
