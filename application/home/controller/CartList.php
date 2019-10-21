<?php

namespace app\home\controller;

use app\home\model\Cart;


class CartList extends Common
{
    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new Cart();
    }

    public function index()
    {
        /**根据购物车的信息查询商品信息,sku信息**/
        //封装了根据是否登录，取出购物车存放的商品字段的方法
        $shopcarinfo = $this->getAllCart();
        $goodsinfo = [];
        if (empty($shopcarinfo)) {
            $goodsinfo = [];
        } else { 
            //根据购物车中的数据逐条查询对应的商品信息
            foreach ($shopcarinfo as  $v) {
                $info = $this->m->queryGoods($v['goods_id'], $v['spec_goods_id']);

                if ($info) {
                    //把商品数量添加进去,首页渲染
                    $info['number'] = $v['number'];
                    //把购物车表的id添加进去，js做修改操作时方便获取
                    $info['car_id'] = $v['id'];
                    //把是否选中添加进去
                    $info['is_selected'] = $v['is_selected'];
                    
                } else {
                    $info = [];
                }
                $goodsinfo[] = $info;
            }
        }
        return view('shop_car/index', ['goodsinfo' => $goodsinfo]);
    }

    private function getAllCart()
    {
        //判断用户是否登录，根据情况取出数据表或者cookie中的信息
        $userid = session('home_userinfo.id') ?? false;
        if ($userid) {
            $shopcarinfo = db('cart')->where('user_id', $userid)->select();
            return $shopcarinfo ?? [];
        } else {
            $goodsinfo = cookie('shopcar') ?? [];
            return $goodsinfo;
        }
    }
}
