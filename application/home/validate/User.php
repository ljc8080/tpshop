<?php

namespace app\home\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = array(
        'code|验证码' => 'require|length:4|integer',
        'phone|手机号' => 'require|unique:user,username|regex:/^1[3-9]\d{9}$/',
        'password|密码' => 'require|confirm:repassword',
        'username|用户名称' => 'require|chsAlphaNum|length:1,10',
        'email|邮箱' => 'require|email',
        'remname|记住密码'=>'require|in:0,1',
        'loginname|登录名称' => 'require'
    );
    protected $message  = array(
        'phone.regex' => '手机号码格式不正确',
        'password.confirm' => '确认密码输入不正确'
    );

    protected $scene = array(
        'captcha' => 'phone',
        'reg' => ['code', 'phone', 'password'],
        'login' => ['loginname' => 'require', 'password' => 'require','remname'],
        'relevance' => ['phone'=>'require|regex:/^1[3-9]\d{9}$/', 'username', 'email']
    );

    public static function checkColumn($column)
    {
        $email = '/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/';
        $phone = '/^1[3-9]\d{9}$/';
        if (preg_match($phone, $column)) {
            return 'phone';
        } elseif (preg_match($email, $column)) {
            return 'email';
        } else {
            return 'username';
        }
    }
}
