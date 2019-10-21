<?php

namespace app\home\model;

use think\Model;

class OrderGoods extends Model
{
    public function goods(){
        return $this->belongsTo('goods','goods_id','id');
    }

    public function sku(){
        return $this->belongsTo('spec_goods','spec_goods_id','id');
    }
}
