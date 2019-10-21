<?php

namespace app\home\controller;

use app\home\model\Order as AppOrder;
use app\home\controller\Common;
use app\home\model\Cart;
use app\home\model\Goods;
use app\home\model\SpecGoods;
use think\Db;
use think\Exception;
use think\model\Collection;

//订单结算类
class Order extends Common
{
    /**
     *显示添加购物车后结算页面
     */
    public function settlement()
    {
        $userid = session('home_userinfo.id') ?? false;
        //判断是否登录，如果没有登录先去登录
        if (!$userid) {
            //做一个标识，记录一下购物车的地址，用户登录后直接跳到结算页面
            //这里用session而不是用缓存存储是为了要区分登陆的用户，缓存需要设置一个名称根据用户的id区分，读取时麻烦
            session('login_success_url', 'https://www.digou.ltd/order/settlement');
            $this->redirect('/user/login');
        }

        //查询用户的收货地址
        $address = db('address')
            ->field('create_time,update_time,delete_time', true)
            ->where('user_id', $userid)
            ->select();

        if (!$address) {
            $this->error('收货地址数据异常');
        }

        //根据用户信息查询购物车记录并关联查询商品详情
        $goodsinfo = Cart::with('goods,sku')->where([
            'user_id' => $userid,
            'is_selected' => 1
        ])->select();

        if (!$goodsinfo) {
            dump($goodsinfo);
            //$this->error('获取信息异常');
            
            die;
        }
        
        $goodsinfo = (new Collection($goodsinfo))->toArray();


        $total = 0;
        $num = 0;
        foreach ($goodsinfo as $v) {
            //遍历每条商品的数量并相加
            $num += $v['number'];
            //计算花费的总金额
            if (isset($v['sku']['price'])) {
                $total += $num * $v['sku']['price'];
            } else {
                $total += $num * $v['goods']['goods_price'];
            }
        }
        //compact('goodsinfo','address','total','num')
        //dump($data);die;
        $this->assign([
            'goodsinfo' => $goodsinfo,
            'address' => $address,
            'num' => $num,
            'total' => $total,
        ]);
        cache('paygoods_' . $userid, array_merge($goodsinfo, ['num' => $num], ['total' => $total]));
        return view();
    }

    public function add()
    {
        $address_id = input('post.address_id');
        $userid = session('home_userinfo.id') ?? false;
        if (!$userid) {
            $this->errorInfo('401');
        }

        //读取商品信息
        $goodsinfo = cache('paygoods_' . $userid);
        if (!$goodsinfo) {
            $this->errorInfo('提交订单失败，请重试');
        }

        //订单编号
        $order_sn = time() . mt_rand(1000000, 9999999) . $userid;

        //根据地址id查找收件人信息
        $address = db('address')
            ->field('create_time,update_time,delete_time', true)
            ->where(['user_id' => $userid, 'id' => $address_id])
            ->find();
        if (!$address) {
            $this->errorInfo('请重新选择收货地址');
        }
        Db::startTrans();
        try {
            //创建订单
            $data = [
                'user_id' => $userid,
                'consignee' => $address['consignee'],
                'order_sn' => $order_sn,
                'address' => $address['address'],
                'phone' => $address['phone'],
                'goods_price' => $goodsinfo['total'],
                'coupon_price' => $goodsinfo['total'],
                'order_amount' => $goodsinfo['total'],
                'total_amount' => $goodsinfo['total'],
            ];
           
            $order_id = Db::name('order')->insertGetId($data);
            if (!$order_id) {
                throw new Exception('创建订单失败' . __LINE__);
            }
            
            //根据订单id将商品添加到order_goods表
            $data = [];
            $goods = [];
            $spec_goods = [];
            foreach ($goodsinfo as $k => $v) {
                if ($k === 'num') {
                    break;
                }
                $store_count = $v['sku']['store_count'] ?? $v['goods']['goods_number'];
                if ($store_count < $goodsinfo['num']) {
                    throw new Exception('订单中含有库存不足的商品');
                }
                //组装信息，批量修改库存
                if (isset($v['sku']['store_count'])) {
                    $spec_goods[] = [
                        'id' => $v['sku']['id'],
                        'store_count' => $v['sku']['store_count'] - $goodsinfo['num'],
                        'store_frozen' => $v['sku']['store_frozen'] + $goodsinfo['num']
                    ];
                } 

                $goods[] = [
                    'id' => $v['goods']['id'],
                    'goods_number' => $v['goods']['goods_number'] - $goodsinfo['num'],
                    'frozen_number' => $v['goods']['frozen_number'] + $goodsinfo['num']
                ];

                //组装信息，批量添加订单
                $data[] = [
                    'order_id' => $order_id,
                    'goods_id' => $v['goods_id'],
                    'spec_goods_id' => $v['spec_goods_id'],
                    'number' => $goodsinfo['num'],
                    'goods_name' => $v['goods']['goods_name'],
                    'goods_logo' => $v['goods']['goods_logo'],
                    'goods_price' => $v['sku']['price'] ?? $v['goods']['goods_price'],
                    'spec_value_names' => $v['sku']['value_names'] ?? ''
                ];
            }

            $res = db('order_goods')->insertAll($data);
            if(!$res){
                throw new Exception('创建订单失败');
            }
            $good = new Goods();
            $specgoods = new SpecGoods();
            $res1 = $good->isUpdate(true)->saveAll($goods);
            $res2 = $specgoods->isUpdate(true)->saveAll($spec_goods);
            if(!$res1||!$res2){
                throw new Exception('库存更新失败');
            }
            //删除购物车记录
            $res = db('cart')->where(['user_id'=>$userid,'is_selected'=>1])->delete();
            if(!$res){
                throw new Exception('创建订单失败');
            }
        } catch (Exception $e) {
            Db::rollback();
            $this->errorInfo($e->getMessage().$e->getLine());
        }
        Db::commit();
        cache('paygoods_'.$userid,null);
        $this->successInfo($order_id,'创建订单成功');
    }

	//查询订单状态
    public function status()
    {
        //接收订单编号
        $order_sn = input('order_sn');
        //查询订单状态
        /*$order_status = \app\common\model\Order::where('order_sn', $order_sn)->value('order_status');
        return json(['code' => 200, 'msg' => 'success', 'data'=>$order_status]);*/
        //通过线上测试
        $res = http_request("http://pyg.tbyue.com/home/order/status/order_sn/{$order_sn}",'get');
        echo $res;die;
    }

    public function payresult()
    {
        $order_sn = input('order_sn');
        $order = AppOrder::where('order_sn', $order_sn)->find();

        if (!empty($order)) {
            return view('pay/paysuccess', ['total' => $order['total_amount'], 'type' => '扫码支付']);
        } else {
            return view('pay/payfail', ['msg' => '未知']);
        }
    }
}
