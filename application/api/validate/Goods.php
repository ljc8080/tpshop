<?php
namespace app\api\validate;

use think\Validate;

class Goods extends Validate
{
    protected $rule = array(
        'goods_name|商品名称' => 'require|length:1,30',
        'goods_remark|商品简介' => 'require',
        'cate_id|所属类别' => 'require|number|gt:0',
        'brand_id|所属品牌' => 'require|number|gt:0',
        'goods_price|商品价格' => 'require|float|egt:0',
        'market_price|市场价格' => 'require|float|egt:0',
        'cost_price|成本价格' => 'require|float|egt:0',
        'goods_logo|商品logo' => 'require',
        'is_free_shipping|是否包邮' => 'require|in:0,1',
        'mould_id|运费模板' => 'number|gt:0',
        'weight|商品重量' => 'float|gt:0',
        'volume|商品体积' => 'float|gt:0',
        'goods_number|总库存' => 'number|egt:0',
        'keywords|商品关键字' => 'length:1,15',
        'is_hot|是否热卖' => 'in:0,1',
        'is_on_sale|是否上架' => 'in:0,1',
        'is_recommend|是否推荐' => 'in:0,1',
        'is_new|是否新品' => 'in:0,1',
        'sort|商品排序' => 'number|gt:0',
        'goods_images|商品图片' => 'require|array',
        'type_id|商品类型' => 'require|number|gt:0',
        'item|商品规格值' => 'require|array',
        'attr|商品属性值' => 'require|array'
    );

    protected $scene = array(
        'edit' => array(
            'goods_name',
            'goods_remark',
            'cate_id',
            'brand_id',
            'goods_price',
            'market_price',
            'cost_price',
            'goods_logo',
            'is_free_shipping',
            'mould_id',
            'weight',
            'volume',
            'goods_number',
            'keyword',
            'is_hot',
            'is_on_sale',
            'is_recommend',
            'is_new',
            'sort|',
            'goods_images' => 'array',
            'type_id',
            'item',
            'attr'
        )
    );
}
