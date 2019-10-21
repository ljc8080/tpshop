<?php
//配置文件
return [
    'template'  =>  [
        'layout_on'     =>  true,
        'layout_name'   =>  'Common/layout',
    ],
    'mobile_code'=>[
        'url'=>'https://way.jd.com/kaixintong/kaixintong',
        'appkey'=>'3042f80d0b6810a126bee58ef17d1542',
        'content'=>'【凯信通】您的验证码是:',
        'code_length'=>4
    ],

    'page'=>[
        'goods'=>30
    ],
    'pay'=>[
        'alipay'=>['pay_code' => 'alipay', 'pay_name' => '支付宝', 'logo' => '/static/home/img/_/pay2.jpg'],
        'wechat'=>['pay_code' => 'wechat', 'pay_name' => '微信', 'logo' => '/static/home/img/_/pay3.jpg'],
        'unionpay'=>['pay_code' => 'union', 'pay_name' => '银联', 'logo' => '/static/home/img/_/pay4.jpg'],
    ],
    	//ping++聚合支付
        'pingpp' => [
            'api_key' => 'sk_test_KOGmH8SSuzrDOmLiDCzX9eX9',//test_key 或 live_key
            'app_id' => 'app_nTG0uPa5OWH4mH0m',// 应用app_id
            'private_key_path' => './pingpp_rsa_private_key.pem', //商户私钥文件路径
            'public_key_path' => './pingpp_rsa_public_key.pem', //ping++公钥文件路径
        ],
];
