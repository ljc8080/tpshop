<?php
namespace app\api\validate;

use think\Validate;

class Admin extends Validate{
    protected $rule = array(
        'username|管理员名称'=>'require|chsAlpha',
        'email|邮箱'=>'require|email|unique:admin,email',
        'role_id|所属角色'=>'require|integer|gt:0',
        'password|密码'=>'length:6,15|alphaNum'
    );

}