<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NavModel extends Model
{
    /**
     * 与模型关联的数据表。
     *
     * @var string
     */
    protected $table = 'pre_nav';


    /******时间管理******/
    #定义是否默认维护时间，默认是true.改为false，则以下时间相关设定无效
    public $timestamps = true;


}
