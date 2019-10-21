<?php

namespace app\home\controller;

use app\home\model\Goods;
use app\home\model\Order;
use app\home\model\OrderGoods;
use app\home\model\SpecGoods;
use think\Db;
use think\Exception;
use think\Validate;

class Pay extends Common
{
    public function index()
    {
        $userid = session('home_userinfo.id') ?? false;
        $order_id = input('get.oid') ?? false;
        if (!$order_id) {
            $this->error('参数错误');
        }
        if (!$userid) {
            $this->redirect('/user/login');
        } elseif (!$this->checkid($order_id)) {
            $this->error('参数错误');
        }

        //查询订单号及应付金额
        $res = db('order')->field('id,order_sn,order_amount')->where(['user_id' => $userid, 'id' => $order_id])->find();
        if (!$res) {
            $this->error('获取订单信息失败');
        }
        $pay = config('pay') ?? false;
        if (!$pay) {
            $this->error('获取支付方式失败');
        }
        //线上的地址
        $url = url('/home/order/qrpay', ['id' => $res['order_sn'], 'debug' => 'true'], true, "http://pyg.tbyue.com");
        //生成支付二维码
        $qrCode = new \Endroid\QrCode\QrCode($url);
        //二维码图片保存路径（请先将对应目录结构创建出来，需要具有写权限）
        if (!is_dir('./uploads/qrcode/')) {
            mkdir('./uploads/qrcode/', 0777, true);
        }
        $qr_path = '/uploads/qrcode/' . uniqid(mt_rand(100000, 999999), true) . '.png';
        //将二维码图片信息保存到文件中
        $qrCode->writeFile('.' . $qr_path);
        $this->assign('qr_path', $qr_path);
        $this->assign(['order' => $res, 'pay' => $pay]);
        return view();
    }

    //支付宝同步接口
    public function alicallback()
    {
        require_once("./plugins/pay/alipay/config.php");
        require_once './plugins/pay/alipay/pagepay/service/AlipayTradeService.php';

        $arr = input('get.');
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);
        if ($result) {
            return view('paysuccess', ['total' => $result['total_amount'], 'type' => '支付宝']);
        } else {
            return view('payfail', ['msg' => '未知']);
        }
    }

    public function pay()
    {
        $userid = session('home_userinfo.id') ?? false;
        if (!$userid) {
            $this->redirect('/user/login');
        }
        $data = input('post.');
        $validate = new Validate([
            'type'  => 'require|alpha',
            'oid' => 'require|integer|gt:0'
        ]);
        if (!$validate->check($data)) {
            $this->error('参数错误');
        }
        //根据订单号查询订单信息
        $orderinfo = db('order')
            ->field('order_sn,order_amount,user_note')
            ->where(['user_id' => $userid, 'id' => $data['oid']])
            ->find();
        if (!$orderinfo) {
            $this->error('订单信息错误');
        }

        switch ($data['type']) {
            case 'alipay':
                $data = [
                    'WIDout_trade_no' => $orderinfo['order_sn'],
                    'WIDsubject' => '品优购订单',
                    'WIDtotal_amount' => $orderinfo['order_amount'],
                    'WIDbody' => $orderinfo['user_note']
                ];
                $res = http_request('https://www.digou.ltd/plugins/pay/alipay/pagepay/pagepay.php', 'post', $data);
                echo $res;
                break;
            case 'wechat':

                break;
            case 'union':

                break;
            default:
                $this->errorInfo('选择的支付方式错误');
        }
    }

    //支付宝异步通知接口
    public function alinotify()
    {
        $userid = session('home_userinfo.id') ?? false;
        if (!$userid) echo 'fail';
        die;
        $arr = input('post.');
        trace('alinotify:' . $arr, 'info');
        require_once("./plugins/pay/alipay/config.php");
        require_once './plugins/pay/alipay/pagepay/service/AlipayTradeService.php';
        $result = $alipaySevice->check($arr);
        if ($result) {
            if ($_POST['trade_status'] == 'TRADE_FINISHED') {
                //退款的处理
                echo 'success';
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //支付成功的处理
                Db::startTrans();
                try {
                    //查询订单是否存在
                    $order = Order::where(['user_id' => $userid, 'id' => $arr['order_sn']])->find();
                    if (!$order) {
                        throw new Exception('订单不存在：' . $arr['order_sn']);
                    }

                    //查询订单的价格和支付宝返回的价格是否一致
                    if ($order['order_amount'] !== $arr['order_amount']) {
                        throw new Exception('金额不一致：' . $arr['order_amount']);
                    }

                    //查询订单是否是未付款状态
                    if ($order['order_status'] !== 0) {
                        throw new Exception('订单已完成付款：' . $order['order_status']);
                    }

                    //修改订单状态为已付款
                    $order->order_status = 1;
                    //修改支付时间
                    $order->create_time = time();
                    //记录支付方式
                    $order->payname = 'alipay';

                    //记录支付宝的交易信息 pay_log表
                    $res = db('pay_log')->insert(['order_sn' => $arr['order_sn'], 'json' => json($arr)]);
                    if (!$res) {
                        throw new Exception('记录支付宝信息失败');
                    }
                    //扣除库存
                    $res = OrderGoods::with('goods,sku')->where('order_id', $arr['order_sn'])->select();
                    if (!$res) {
                        throw new Exception('无法获取订单对应的商品');
                    }
                    $goods = [];
                    $spec_goods = [];
                    foreach ($res as $v) {
                        if (empty($v['spec_goods_id'])) {
                            $goods[] = [
                                'id' => $v['id'],
                                'frozen_number' => $v['goods']['frozen_number'] - $v['number']
                            ];
                        } else {
                            $spec_goods[] = [
                                'id' => $v['id'],
                                'store_frozen' => $v['spec_goods']['store_frozen'] - $v['number']
                            ];
                        }
                    }

                    $good = new Goods();
                    $specgoods = new SpecGoods();
                    $res1 = $good->isUpdate(true)->saveAll($goods);
                    $res2 = $specgoods->isUpdate(true)->saveAll($spec_goods);
                    if (!$res1 || !$res2) {
                        throw new Exception('库存更新失败');
                    }
                } catch (Exception $e) {
                    Db::rollback();
                    trace('alinotify-error:' . $e->getMessage() . ';Line:' . $e->getLine(), 'error');
                    echo 'fail';
                    exit;
                }
                Db::commit();
                echo 'success';
            }
        } else {
            echo 'fail';
        }
    }

    //扫码支付
    public function qrpay()
    {
        $agent = request()->server('HTTP_USER_AGENT');
        //判断扫码支付方式
        if (strpos($agent, 'MicroMessenger') !== false) {
            //微信扫码
            $pay_code = 'wx_pub_qr';
        } else if (strpos($agent, 'AlipayClient') !== false) {
            //支付宝扫码
            $pay_code = 'alipay_qr';
        } else {
            //默认为支付宝扫码支付
            $pay_code = 'alipay_qr';
        }
        //接收订单id参数
        $order_sn = input('id');
        //创建支付请求
        $this->pingpp($order_sn, $pay_code);
    }

    //发起ping++支付请求
    public function pingpp($order_sn, $pay_code)
    {
        //查询订单信息
        $order = Order::where('order_sn', $order_sn)->find();
        //ping++聚合支付
        \Pingpp\Pingpp::setApiKey(config('pingpp.api_key')); // 设置 API Key
        \Pingpp\Pingpp::setPrivateKeyPath(config('pingpp.private_key_path')); // 设置私钥
        \Pingpp\Pingpp::setAppId(config('pingpp.app_id'));
        $params = [
            'order_no'  => $order['order_sn'],
            'app'       => ['id' => config('pingpp.app_id')],
            'channel'   => $pay_code,
            'amount'    => $order['order_amount'] * 100,
            'client_ip' => '127.0.0.1',
            'currency'  => 'cny',
            'subject'   => 'Your Subject', //自定义标题
            'body'      => 'Your Body', //自定义内容
            'extra'     => [],
        ];
        if ($pay_code == 'wx_pub_qr') {
            $params['extra']['product_id'] = $order['id'];
        }
        //创建Charge对象
        $ch = \Pingpp\Charge::create($params);
        //跳转到对应第三方支付链接
        $this->redirect($ch->credential->$pay_code);
        die;
    }

    //p++扫码异步通知
    public function notify()
    {
        $event = json_decode(file_get_contents("php://input"));

        // 对异步通知做处理
        if (!isset($event->type)) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
            exit("fail");
        }
        switch ($event->type) {
            case "charge.succeeded":
                // 开发者在此处加入对支付异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            case "refund.succeeded":
                // 开发者在此处加入对退款异步通知的处理代码
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                break;
            default:
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                break;
        }
        try {
            //接收参数
            $params = file_get_contents("php://input");
            //获取请求头信息，用于获取签名
            $headers = \Pingpp\Util\Util::getRequestHeaders();
            // 签名在头部信息的 x-pingplusplus-signature 字段
            $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : null;
            //获取ping++公钥用于签名
            $pub_key_path = "./pingpp_rsa_public_key.pem";
            $pub_key_contents = file_get_contents($pub_key_path);
            //验证签名
            $result = openssl_verify($params, base64_decode($signature), $pub_key_contents, 'sha256');
            if ($result === 1) {
                // 验证通过
                $event = json_decode($params, true);
                // 对异步通知做处理
                if (!isset($event['type'])) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                    exit("fail");
                }
                switch ($event['type']) {
                    case "charge.succeeded":
                        // 开发者在此处加入对支付异步通知的处理代码
                        //修改订单状态
                        $order_sn = $event['data']['object']['order_no'];
                        $order = Order::where('order_sn', $order_sn)->find();
                        if (empty($order)) {
                            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
                            exit("fail");
                        }
                        $order->order_status = 1; //已付款、待发货
                        $order->pay_code = $event['data']['object']['channel'];
                        $order->pay_name = $event['data']['object']['channel'] == 'wx_pub_qr' ? '微信支付' : '支付宝';
                        $order->save();
                        db('paylog')->insert(['order_sn' => $order_sn, 'json' => $params]);
                        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                        break;
                    case "refund.succeeded":
                        // 开发者在此处加入对退款异步通知的处理代码
                        header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
                        break;
                    default:
                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request');
                        break;
                }
            } elseif ($result === 0) {
                http_response_code(400);
                echo 'verification failed';
                exit;
            } else {
                http_response_code(400);
                echo 'verification error';
                exit;
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            http_response_code(500);
            echo $msg;
            exit;
        }
    }
}
