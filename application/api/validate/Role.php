<?php
namespace app\api\validate;

use think\Validate;

class Role extends Validate{
    protected $rule = array(
        'role_name|角色名称'=>'require|chsAlphaNum|length:1,10',
        'desc|角色描述'=>'length:1,50',
        'auth_ids|角色所属权限'=>'require',
    );
}