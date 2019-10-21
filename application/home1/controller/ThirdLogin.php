<?php

namespace app\home1\controller;

use app\home1\model\User;
use think\Exception;
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
            'sex' => $info['gender'] == '男' ? '0' : '1',
            'birthday' => $info['year'],
            'place' => $info['province'],
            'figure_url' => $info['figureurl_qq'],
            'last_login_time' => time()
        ];
        if ($res) {
            //同步信息
            $res = db('user')->where('openid', $openid)->update($data);
            if ($res === false) {
                $this->error('登陆失败');
            } else {
                session('home1_userinfo', $data);
                $this->redirect('home1/index/index');
            }
        } else {
            //新增信息
            $data['is_third'] = 1;
            $data['username'] = uniqid();
            $data['open_type'] = 'qq';
            $data['openid'] = $openid;
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
            db('profile')->insert(['uid'=>$res['id']]);
            session('relevance', null);
            session('home1_userinfo', $data);
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
                'place' => $info['city'],
                'figure_url' => $info['avatar'],
                'last_login_time' => time()
            ];
            if ($res) {
                $res = db('user')
                    ->where(
                        [
                            'open_type' => 'alipay',
                            'openid' => $openid
                        ]
                    )
                    ->update($data);
                session('home1_userinfo', $data);
                $this->redirect('home1/index/index');
            } else {
                $data['is_third'] = 1;
                $data['username'] = uniqid();
                $data['open_type'] = 'alipay';
                $data['openid'] = $openid;
                session('relevance', $data);
                $this->view->engine->layout('common/login_register');
                return view('login_register/relevance', ['type' => 'alipay']);
            }
        } catch (Exception $e) {
            // dump($e->getMessage());
            // dump($e->getLine());
            // dump($e->getFile());
            // die;
            $this->error('系统繁忙');
        }
    }
}
