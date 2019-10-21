<?php

namespace app\home\controller;

use app\home\model\User;
use think\Exception;
use think\Session;
use think\Validate;

class ThirdLogin extends Common
{

    public function qq()
    {
        require_once("./test/thirdlogin/tencent/qq/API/qqConnectAPI.php");
        $qc = new \QC();
        $token = $qc->qq_callback();
        $openid = $qc->get_openid();
        $qc = new \QC($token, $openid);
        $info = $qc->get_user_info();
        //判断数据库中是否存在openid
        $res = db('user')->where('openid', $openid)->find();
        $data = [
            'nickname' => $info['nickname'],
            'figure_url' => $info['figureurl_qq'],
            'last_login_time' => time(),
        ];
        if ($res) {
            $data['id'] = $res['id'];
            //同步信息
            $res1 = db('user')->where('id', $res['id'])->update($data);
            if ($res1 === false) {
                $this->error('登陆失败');
            } else {
                $data['id'] = $res['id'];
                session('home_userinfo', $data);
                $this->shopcarinfo();
                $url = Session::pull('login_success_url')??'https://www.digou.ltd/';
                $this->redirect($url);
            }
        } else {
            //新增信息
            $data['is_third'] = 1;
            $data['username'] = uniqid();
            $data['open_type'] = 'qq';
            session('relevance', $data);
            $this->view->engine->layout('common/login_register');
            return view('login_register/relevance', ['type' => 'qq']);
        }
    }

    public function relevance()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $validate = new Validate([
                'phone|手机号' => 'require|regex:/^1[3-9]\d{9}$/',
                'email|邮箱' => 'require|email',
                'type|类型' => 'require'
            ]);
            if (!$validate->check($data)) {
                $this->errorInfo($validate->getError());
            }
            $res = db('user')->where('phone', $data['phone'])->where('open_type', $data['type'])->count();
            if ($res !== 0) {
                $this->errorInfo('手机号码已存在');
            }
            $data = array_merge($data, session('relevance'));
            $res = (new User())->add($data);
            if (!$res) {
                $this->errorInfo('登录失败');
            }
            session('relevance', null);
            session('home_userinfo', $data);
            $this->successInfo($res);
        } else {
            $this->errorInfo('非法请求');
        }
    }

    public function alipay()
    {
        require_once  './test/thirdlogin/ali/alipay/config.php';
        require_once  './test/thirdlogin/ali/alipay/service/AlipayOauthService.php';
        $aop = new \AlipayOauthService($config);
        try {
            $auth_code = $aop->auth_code();
            $token = $aop->get_token($auth_code);
            $info = $aop->get_user_info($token);
            $openid = $info['user_id'];
            $res = db('user')
                ->where(
                    [
                        'open_type' => 'alipay',
                        'openid' => $openid
                    ]
                )->find();
            $data = [
                'nickname' => $info['nick_name'],
                'figure_url' => $info['avatar'],
                'last_login_time' => time(),
            ];
            if ($res) {
                $data['id'] = $res['id'];
                $res = db('user')
                    ->where('id',$res['id'])
                    ->update($data);
                    
                session('home_userinfo', $data);
                $this->shopcarinfo();
                $url = Session::pull('login_success_url')??'https://www.digou.ltd/';
                $this->redirect($url);
            } else {
                $data['is_third'] = 1;
                $data['username'] = uniqid();
                $data['open_type'] = 'alipay';
                session('relevance', $data);
                $this->view->engine->layout('common/login_register');
                return view('login_register/relevance', ['type' => 'alipay']);
            }
        } catch (Exception $e) {
            $this->error('系统繁忙');
        }
    }
}
