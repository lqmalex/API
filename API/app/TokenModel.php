<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TokenModel extends Model
{
    protected $table = 'pre_token';

    public $timestamps = false;

    public function user(){
        return $this->hasMany('App\UserModel','id','user_id');
    }
}
