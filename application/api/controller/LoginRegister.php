<?php

namespace app\api\controller;

use app\api\model\Admin;
use tool\jwt\Token;

class LoginRegister extends Common
{
    /*
    *显示验证码src接口 
     */
    public function captcha()
    {
        $uniqid = uniqid('captcha', true);
        $data = array(
            'url' => captcha_src($uniqid),
            'uniqid' => $uniqid
        );
        $this->successInfo($data);
    }

    /* 
*登录接口
 */
    public function login()
    {
        //接收数据并进行验证
        $data = input('post.');
        $res = $this->verify($data);
        if ($res !== true) {
            $this->errorInfo($res);
        }
        
        extract($data);
        $res = captcha_check($code, $uniqid);
        if (!$res) {
            $this->errorInfo('验证码错误');
        }

        $m = new Admin();
        $res = $m->verify($username, encryption($password));
        if (!$res) {
            $this->errorInfo('用户名或密码错误');
        }

        $token = Token::getToken($res['id']);
        $data = [];
        $data['token'] = $token;
        $data['user_id'] = $res['id'];
        $data['username'] = $res['username'];
        $data['nickname'] = $res['nickname'];
        $data['email'] = $res['email'];
        $this->successInfo($data);
    }

    public function logout()
    {
        $token = Token::getRequestToken();
        $arr = cache('delete_token') ?? [];
        $arr[] = $token;
        cache('delete_token', $arr, 3600 * 24);
        cookie('userid',null);
        $this->successInfo();
    }

    /* 
    *注销接口
    */

    private function verify($data)
    {
        $rule = [
            'username|用户名' => 'require|length:1,10|chsAlphaNum',
            'password|密码' => 'require|length:6,16|chsDash',
            'uniqid|验证码标识' => 'require',
            'code|验证码' => 'require|alphaNum'
        ];
        return $this->validate($data, $rule);
    }
}
