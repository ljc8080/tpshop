<?php

namespace app\api\model;

use think\Exception;
use think\Model;

class Goods extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function category()
    {
        return $this->belongsTo('category', 'cate_id', 'id')->bind('cate_name,pid_path');
    }

    public function type()
    {
        return $this->belongsTo('type', 'type_id', 'id')->bind('type_name');
    }

    public function brand()
    {
        return $this->belongsTo('brand', 'brand_id', 'id')->bind('name');
    }


    public function categoryAll()
    {
        return $this->belongsTo('category', 'cate_id', 'id');
    }

    public function typeAll()
    {
        return $this->belongsTo('type', 'type_id', 'id');
    }


    public function specGoods()
    {
        return $this->hasMany('spec_goods', 'goods_id', 'id');
    }

    public function goodsImage()
    {
        return $this->hasMany('goods_images', 'goods_id', 'id');
    }

    public function brandAll()
    {
        return $this->belongsTo('brand', 'brand_id', 'id');
    }

    public function getGoodsAttrAttr($v)
    {
        $res = empty($v) ? [] : json_decode($v);
        return $res;
    }

    public function show($page, $condition)
    {
        try {
            return self::with('category,type,brand')->where($condition)->paginate($page);
        } catch (Exception $e) {
            return [];
        }
    }

    public function add($data){
        try{
            $obj = self::create($data,true);
            return $obj->id;
        }catch(Exception $e){
            return false;
        }
    }

    public function edit($data,$id){
        try{
            return $this->allowField(true)->save($data,['id'=>$id]);
        }catch(Exception $e){
            return false;
        }
    }

}
