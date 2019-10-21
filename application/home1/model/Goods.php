<?php

namespace app\home1\model;

use think\Model;

class Goods extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function goodsImage()
    {
        return $this->hasMany('goods_images', 'goods_id', 'id');
    }

    public function specgoods()
    {
        return $this->hasMany('spec_goods', 'goods_id', 'id');
    }

    public function getGoodsAttrAttr($v){
        return json_decode($v,true);
    }
}
