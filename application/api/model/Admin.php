<?php

namespace app\api\model;

use think\Model;

class Admin extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function role(){
        return $this->belongsTo('role','role_id','id')->bind('role_auth_ids');
    }

    public function verify($username,$password){
        return self::where([
            'username'=>$username,
            'password'=>$password
        ])->find();
    }

    public function setPasswordAttr($v){
        return encryption($v);
    }

    public function show($conditon,$page){
        return self::where($conditon)->paginate($page);
    }

    public function queryone($id){
        return self::get($id);
    }

    public function add($data){
        return self::create($data,true);
    }

    public function edit($data,$id){
        return self::update($data,['id'=>$id],true);
    }
}
