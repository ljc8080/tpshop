<?php

namespace app\api\model;

use think\Model;

class Attribute extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function getAttrValuesAttr($v){
        $res = empty($v)?[]:explode(',',$v);
        return $res;
    }
}
