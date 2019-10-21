<?php

namespace app\home1\controller;

use app\home1\model\User;
use think\Cookie;
use think\Db;
use think\Validate;

class LoginRegister extends Common
{
    private $m;

    public function _initialize()
    {
        parent::_initialize();
        $this->m = new User();
    }

    public function login()
    {
        
        if (request()->isGet()) {
            $this->view->engine->layout('common/login_register');
            return view();
        } elseif (request()->isPost()) {
            $data = input('post.');
            $validate = new Validate([
                'password|密码' => 'require|length:6,15',
                'remlogin|记住密码' => 'require|in:0,1',
                'loginname|登录名称' => "require|regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]{1,13}$/u"
            ]);
            if (!$validate->check($data)) {
                $this->errorInfo($validate->getError());
            }
            $info = Db::name('user')->where(function ($query) use ($data) {
                $query->where(
                    [
                        'password' => encryption($data['password']),
                        'phone' => $data['loginname']
                    ]
                );
            })->whereOr(function ($query) use ($data) {
                $query->where(
                    [
                        'password' => encryption($data['password']),
                        'email' => $data['loginname']
                    ]
                );
            })->whereOr(function ($query) use ($data) {
                $query->where(
                    [
                        'password' => encryption($data['password']),
                        'username' => $data['loginname']
                    ]
                );
            })
                ->find();
            if(!$info){
                $this->errorInfo('用户名或密码错误');
            }
        
            if($data['remlogin']==1){
                cookie('rempass',$info['id'],85600);
            }
            unset($info['password']);
            session('home1_userinfo',$info);
            $this->successInfo($info,'登陆成功'); 
        } else {
            $this->errorInfo('非法请求');
        }
    }

    public function register()
    {
        if (request()->isGet()) {
            $this->view->engine->layout('common/login_register');
            return view();
        } elseif (request()->isPost()) {
            $data = input('post.');
            $validate = new Validate([
                'code|验证码'  => 'require|length:4|integer',
                'phone|手机号'  => 'require|regex:/^1[3-9]\d{9}$/',
                'password|密码' => 'require|length:6,15|confirm:repassword'
            ]);
            if (!$validate->check($data)) {
                $this->errorInfo($validate->getError());
            }
            $codeinfo = cache($data['phone'] . 'code');
            if (!$codeinfo) {
                $this->errorInfo('验证码已过期');
            } elseif (!array_key_exists($data['phone'], $codeinfo)) {
                $this->errorInfo('手机号码错误');
            } elseif ($codeinfo[$data['phone']]['info']['code'] != $data['code']) {
                $this->errorInfo('验证码错误');
            }
            //验证码比对通过，删除缓存
            cache($data['phone'] . 'code', null);
            $data = [
                'username' => uniqid(),
                'phone' => $data['phone'],
                'password' => $data['password'],
                'is_third' => '0',
            ];
            $res = $this->m->add($data);
            if (!$res) {
                $this->errorInfo('注册失败，请重试');
            }
            $this->successInfo(null);
        } else {
            $this->errorInfo('非法请求');
        }
    }

    public function code()
    {
        //redis限制ip
        $mobile = input('get.phone');
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
                'info' => [
                    'code' => $num,
                    'lasttime' => time()
                ],
                'lasttime' => time()
            ];
            cache($mobile . 'code', $mobilecode, 180);
        } else {
            if (time() - $mobilecode[$mobile]['lasttime'] <= 180) {
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

    public function is_reg()
    {
        $phone = input('get.phone');
        $validate = new Validate([
            'phone|手机号'  => 'require|regex:/^1[3-9]\d{9}$/',
        ]);
        if (!$validate->check(['phone' => $phone])) {
            $this->errorInfo($validate->getError());
        }
        $res = db('user')->where('phone', $phone)->where('is_third', '0')->count('id');
        if ($res !== 0) {
            $this->errorInfo('用户名已注册');
        }
    }

    public function logout(){
        if(Cookie::has('rempass')){
            Cookie::delete('rempass');
        }
        session('home1_userinfo',null);
        $this->redirect('home1/index/index');
    }
}
