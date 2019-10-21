<?php

namespace app\usercenter\controller;

use think\captcha\Captcha;
use think\Exception;
use think\Validate;

class Safe extends Common
{

    public function index()
    {
        if (request()->isPost()) {
            $this->successInfo('123');
        }
        return view();
    }

    public function editpass()
    {
        if (!request()->isPost()) {
            return;
        }
        $userid = session('home_userinfo.id');
        $password = input('post.password');
        $validate = new Validate([
            'password|密码' => 'require|length:6,16|alphaDash'
        ]);
        if (!$validate->check(['password'=>$password])) {
            $this->errorInfo($validate->getError());
        }
        try {
            db('user')->where('id', $userid)->update(['password' => encryption($password)]);
        } catch (Exception $e) {
            $this->errorInfo($e);
        }
        $this->successInfo(null, '修改成功');
    }

    public function captcha(){
        $config = config('captcha');
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    public function editphone(){
        if(request()->isGet()){
            return view();
        }else{
            
        }
    }
}
