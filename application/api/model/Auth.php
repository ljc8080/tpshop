<?php

namespace app\api\model;

use think\Model;
use think\model\Collection;

class Auth extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function show()
    {
        $res =  self::select();
        if ($res) {
            $obj = new Collection($res);
            return $obj->toArray();
        } else {
            return false;
        }
    }

    public function queryone($id){
        $res = self::get($id);
        if(!$res){
            return false;
        }else{
            return $res->toArray();
        }
    }

    public function add($data){
        return self::create($data,true);
    }

    public function edit($data,$id){
        return self::update($data,['id'=>$id],true);
    }

    public function nav(){
        $res = self::where('is_nav',1)->select();
        if(!$res){
            return false;
        }else{
            $obj = new Collection($res);
            return $obj->toArray();
        }
    }
}
