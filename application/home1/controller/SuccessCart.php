<?php

namespace app\home1\controller;


class SuccessCart extends Common
{
    public function index()
    {
        $userid = $this->checklogin();
        if (!$userid) $userid = '_nologin';
        $info = cache('successcart' . $userid) ?? false;
        if (!$info) {
            $this->redirect('home1/cart/index');
        }
        extract($info);
        if ($spec_goods_id == 0) {
            $condition = [
                'goods_id' => $goods_id
            ];
        } else {
            $condition = [
                'b.id' => $spec_goods_id,
                'goods_id' => $goods_id
            ];
        }
        $res = db('goods')
        ->alias('a')
        ->join('spec_goods b','a.id=b.goods_id')
        ->where($condition)
        ->find();
        if (!$info) {
            $this->error('发生未知错误，请稍后加入购物车');
        }
        $res['number'] = $number;
        $this->assign([
            'goodinfo' => $res
        ]);
        cache('successcart' . $userid,null);
        return view('cart/success-cart');
    }
}
