<?php

namespace app\home1\controller;

use app\home1\model\Cart as AppCart;
use think\Validate;

class Cart extends Common
{
    private $m;
    public function _initialize()
    {
        parent::_initialize();
        $this->m = new AppCart();
    }

    public function add()
    {
        if (!request()->isPost()) {
            $this->errorInfo('非法请求');
        }
        $userid = $this->checklogin();
        $data = input('post.');
        $validate = new Validate([
            'goods_id|商品id' => 'require|integer|gt:0',
            'ids|sku' => 'require',
            'number|商品数量' => "require|integer|gt:0"
        ]);
        if (!$validate->check($data)) {
            $this->errorInfo($validate->getError());
        }
        $spec_id = db('spec_goods')
            ->where(
                [
                    'goods_id' => $data['goods_id'],
                    'value_ids' => $data['ids']
                ]
            )->value('id');
        if (!$spec_id) {
            $this->errorInfo('商品sku错误');
        }
        if ($userid) {
            $data['user_id'] = $userid;
            $data['spec_goods_id'] = $spec_id;
            $res = $this->m->add($data);
            if ($res) {
                cache('successcart' . $userid, $res);
                $this->successInfo($res);
            } else {
                $this->errorInfo('添加失败', 500);
            }
        } else {
            $info = cookie('cart') ?? [];
            if (!array_key_exists($spec_id, $info)) {
                $info[$spec_id] = [
                    'spec_goods_id' => $spec_id,
                    'goods_id' => $data['goods_id'],
                    'number' => $data['number']
                ];
            } else {
                $info[$spec_id]['number'] += $data['number'];
            }
            cache('successcart_nologin', [
                'spec_goods_id' => $spec_id,
                'goods_id' => $data['goods_id'],
                'number' => $data['number']
            ]);
            cookie('cart', $info);
        }
        $this->successInfo(null, '添加成功');
    }

    public function index(){
        $userid = $this->checklogin();
        if($userid){
            //$data = db('')
        }else{

        }
        return view();
    }
}
