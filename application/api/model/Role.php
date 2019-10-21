<?php

namespace app\api\model;

use think\Collection;
use think\Model;

class Role extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function show(){
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
        $res =  self::update($data,['id'=>$id],true);
        if ($res) {
            return $res->toArray();
        } else {
            return false;
        }
    }
}
