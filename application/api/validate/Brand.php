<?php
namespace app\api\validate;

use think\Validate;

class Brand extends Validate {
    protected $rule = array(
        'name|品牌名称'=>'require|length:1,40',
        'cate_id|分类id'=>'require|integer|egt:0',
        'is_hot|是否热门'=>'require|in:0,1',
        'sort|排序'=>'require|integer|gt:0'
    );
    
    protected $scene = array(
        'add'=>['cate_name','pid','is_show','is_hot','sort','logo'],
        'edit'=>['cate_name','pid','is_show','is_hot','sort','logo'],
    );
}