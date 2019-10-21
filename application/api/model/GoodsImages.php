<?php

namespace app\api\model;

use think\Exception;
use think\Model;

class GoodsImages extends Model
{
    public function del($id){
        try{
            $obj = self::where('id',$id)->field('pics_big,pics_sma')->find();
            // $res = self::destroy($id);
            // if(!$res){
            //     throw new Exception();
            // }
            return $obj;
        }catch(\Exception $e){
            return false;
        }  
    }
}
