<?php
namespace app\api\validate;

use think\Validate;

class Auth extends Validate{
    protected $rule = array(
        'auth_name|权限名称'=>'require|chs',
        'pid|父级权限'=>'require|integer|gt:0',
        'auth_c|控制器名称'=>'length:1,15',
        'auth_a|方法名称'=>'length:1,20',
        'is_nav|是否菜单权限'=>'require|in:0,1'
    );
}