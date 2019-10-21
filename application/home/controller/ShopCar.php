<?php

namespace app\home\controller;

use app\home\controller\Common;
use app\home\model\Cart as ShopCarModel;
use think\Exception;
use think\Validate;

class ShopCar extends Common
{

    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new ShopCarModel();
    }

    public function add()
    {
        $data = input('post.');
        //验证
        $validate = new Validate([
            'goods_id|商品id' => 'require|integer|gt:0',
            'spec_id|商品SKU' => 'integer|gt:0',
            'number|商品数量' => 'require|integer|gt:0',
        ]);
        $specid = $data['spec_goods_id'] ?? 0;
        if (!$validate->check($data)) {
            $this->errorInfo($validate->getError());
        }
        //判断是否登录
        $userid = session('home_userinfo.id') ?? false;
        if ($userid) {
            //查询购物车表中是否存在,没有添加,有则修改数量
            $condition = [
                'user_id' => $userid,
                'goods_id' => $data['goods_id'],
                'spec_goods_id' => $specid
            ];
            $param = [
                'number' => $data['number']
            ];
            $res = $this->m->edit($condition, $param);
            if (!$res) {
                $this->errorInfo('加入购物车失败');
            }
        } else {
            $info = cookie('shopcar') ?? [];
            $mapping = $data['goods_id'] . '_' . $specid;
            $data['id'] = $mapping;
            $data['is_selected'] = 1;
            if (empty($info)) {
                $info[$mapping] = $data;
                $info[$mapping]['number'] = $data['number'];
            } else if (!array_key_exists($mapping, $info)) {
                $info[$mapping] = $data;
                $info[$mapping]['number'] = $data['number'];
            } else {
                $info[$mapping]['number'] += $data['number'];
            }
            cookie('shopcar', $info, 86400);
        }
        //设置一个缓存,跳转到添加购物车成功页面时读取
        cache('add_car_success', $data);
        $this->successInfo();
    }


    public function successcart()
    {
        //读取缓存信息(添加成功购物车时设置的)
        $info = cache('add_car_success') ?? false;
        if (!$info) {
            //没有缓存读取get请求参数
            $info = input('get.');
            $validate = new Validate([
                'goods_id|商品id' => 'require|integer|gt:0',
                'spec_id|商品SKU' => 'integer|gt:0',
                'number|商品数量' => 'require|integer|gt:0',
            ]);
            if (!$validate->check($info)) {
                $this->error('参数格式错误');
            }
        }
        //读取成功删除缓存
        cache('add_car_success', null);
        $spec_id = $info['spec_goods_id'] ?? 0;
        //根据读取的参数查询商品详细信息
        $res = $this->m->queryGoods($info['goods_id'], $spec_id);
        if (!$res) {
            $this->error('查询信息失败错误');
        }

        return view('success-cart', ['addCarGood' => $res]);
    }


    public function modify()
    {
        $data = input('post.');
        $validate = new Validate([
            'car_id|购物车id' => 'require',
            'number|商品数量' => 'require|integer|gt:0',
        ]);
        if (!$validate->check($data)) {
            $this->errorInfo('提交的参数格式有误' . __FILE__ . __LINE__);
        }
        extract($data);
        if (session('?home_userinfo')) {
            $res = $this->m->editnum($car_id, $number);
        } else {
            try {
                $info = cookie('shopcar');
                $info[$car_id]['number'] = $number;
                cookie('shopcar', $info);
                $res = true;
            } catch (Exception $e) {
                $res = false;
            }
        }
        if ($res) {
            $this->successInfo('修改成功');
        } else {
            $this->errorInfo('修改失败', 500);
        }
    }

    public function del()
    {
        $id = input('delete.id');
        if (session('?home_userinfo')) {
            $res = db('cart')->where('id', $id)->delete();
        } else {
            try {
                $info = cookie('shopcar');
                unset($info[$id]);
                cookie('shopcar', $info);
                $res = true;
            } catch (Exception $e) {
                $res = false;
            }
        }
        if ($res) {
            $this->successInfo('删除成功');
        } else {
            $this->errorInfo('删除失败');
        }
    }


    public function editstatus()
    { 
        $data = input('post.');
        $validate = new Validate([
            'id' => 'require',
            'is_selected|状态' => 'require|in:0,1',
        ]);
        if (!$validate->check($data)) {
            $this->errorInfo('参数格式错误');
        }
        if(session('?home_userinfo')){
            $res = $this->m->editstatus($data);
        }else{
            try{
                $info = cookie('shopcar');
                if($data['id']=='*'){
                    foreach($info as $k =>$v){
                        $info[$k]['is_selected'] = $data['is_selected'];
                    }    
                }else{
                    $info[$data['id']]['is_selected'] = $data['is_selected'];
                }
                 cookie('shopcar',$info);
                $res = true;
            }catch(Exception $e){
                $res = false;
            }
        }
        if ($res) {
            $this->successInfo('选择成功');
        } else {
            $this->errorInfo('选择失败');
        }
    }


    public function delcook()
    {
        cookie('shopcar', null);
    }
}
