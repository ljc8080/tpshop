<?php
namespace app\api\validate;

use think\Validate;

class Category extends Validate {
    protected $rule = array(
        'id'=>'require|regex:/^\d+$/',
        'cate_name|类别名称'=>'require|length:1,20',
        'pid|父分类'=>'require|integer|egt:0',
        'is_show|是否显示'=>'require|in:0,1',
        'is_hot|是否热门'=>'require|in:0,1',
        'sort|排序'=>'require|integer|gt:0',
        'logo'=>'require'
    );
    
    protected $scene = array(
        'show'=>['id'],
        'add'=>['cate_name','pid','is_show','is_hot','sort','logo'],
        'edit'=>['cate_name','pid','is_show','is_hot','sort','logo'],
    );

}