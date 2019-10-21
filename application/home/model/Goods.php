<?php

namespace app\home\model;

use think\Exception;
use think\Model;

class Goods extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    
    public function goodsImage(){
        return $this->hasMany('goodsImages','goods_id','id');
    }

    public function specGoods(){
        return $this->hasMany('specGoods','goods_id','id');
    }

    public function getGoodsAttrAttr($v){
        return json_decode($v);
    }

    // public function list(array $condtion, $param)
    // {
    //     $page = config('page.goods') ?? 30;
    //     return $this->where($condtion)->paginate($page, false, [
    //         'query' => $param
    //     ]);
    // }
    
    public function list(array $condtion, $param)
    {
        $page = config('page.goods') ?? 30;
        return $this->where($condtion)->paginate($page, false, [
            'query' => $param
        ]);
    }


    public function resemble($conditon,$limit=200){
        try{
            $data =  $this->where($conditon)->limit($limit)->select();
            return (new \think\Collection($data))->toArray();
        }catch(Exception $e){
            return false;
        } 
    }
}
