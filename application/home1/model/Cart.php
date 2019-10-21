<?php

namespace app\home1\model;

use think\Exception;
use think\Model;

class Cart extends Model
{
    public function add($data)
    {
        try {
            extract($data);
            $obj = self::where('user_id', $user_id)
                ->where('goods_id', $goods_id)
                ->where('spec_goods_id', $spec_goods_id)
                ->find();
            if (!$obj) {
                //购物车商品不存在，添加到购物车
                return self::create($data, true);
            } else {
                //购物车商品存在，修改数量
                $obj->number += $number;
                $obj->update_time = time();
                $obj->save();
                return $obj->toArray();
            }
        } catch (Exception $e) { 
            return false;
        }
    }
}
