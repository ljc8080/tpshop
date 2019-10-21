<?php

namespace app\home\model;

use think\Model;

class Category extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function parentCate($cate_id)
    {
        
        return self::where('id', $cate_id)->field('pid,level')->find()->toArray();
    }
}
