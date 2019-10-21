<?php

namespace app\home1\model;

use think\Model;

class User extends Model
{
    public function setPasswordAttr($v){
        return encryption($v);
    }

    public function add($data){
        return self::create($data,true);
    }
}
