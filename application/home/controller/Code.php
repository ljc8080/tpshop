<?php

namespace app\home\controller;

use think\Controller;
use think\Validate;

class Code extends Controller
{
    public function send()
    {
        //redis限制ip
        $mobile = input('get.phone');
        $moudle = input('get.moudle')??false;
        if(!$moudle){
            $this->errorInfo('请选择一个模块');
        }
        $validate = new Validate([
            'phone|手机号'  => 'require|regex:/^1[3-9]\d{9}$/',
        ]);
        if (!$validate->check(['phone' => $mobile])) {
            $this->errorInfo($validate->getError());
        }
        $config = config('mobile_code');
        extract($config);
        $num = null;
        for ($i = 0; $i < $code_length; $i++) {
            $num .= mt_rand(0, 9);
        }

        $mobilecode = cache($mobile . 'code') ?? [];
        $total = cache($mobile . 'total') ?? 0;
        if ($total > 200) {
            $this->errorInfo('该手机号超出了一天内最多的发送次数');
        }
        cache($mobile . 'total', $total + 1);
        if (empty($mobilecode)) {
            $mobilecode[$mobile] = [
                $moudle =>[
                    'lasttime' => time()
                ]
            ];
            cache($mobile . 'code', $mobilecode, 180);
        } else {
            if (time() - $mobilecode[$mobile][$moudle]['lasttime'] <= 180) {
                $this->errorInfo('你发送的太频繁');
            }
        }
        $appkey = 123;
        $url = $url . "?mobile={$mobile}&appkey={$appkey}&content={$content}{$num}";
        $res = http_request($url, 'get', null, true);
        if (!is_array($res)) {
            /* {"code":"10000","charge":false,"remain":0,"msg":"查询成功","result":"<?xml version=\"1.0\" encoding=\"utf-8\" ?><returnsms>\n <returnstatus>Success</returnstatus>\n <message>ok</message>\n <remainpoint>-1222643</remainpoint>\n <taskID>108510639</taskID>\n <successCounts>1</successCounts></returnsms>"} */

            //{code: "10001", charge: false, msg: "错误的请求appkey"}
           $this->successInfo(json_decode($res), '验证码：' . $num);
        } else {
            $this->errorInfo($res[0], 500);
        }
    }
}
