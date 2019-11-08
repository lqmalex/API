<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CateModel extends Model
{
    const STATUS_YES = 1;
    const STATUS_NO = 0;

    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $table = 'pre_category';
}
