<?php
namespace app\api\model;

use think\Model;

class Category extends Model
{

    //设置隐藏属性
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function brand(){
        return $this->hasMany('brand','cate_id','id');
    }

    public function add(array $data){
        return self::create($data,true);
    }

    public function edit(array $data,int $id){
       return $this->allowField(true)->save($data,['id'=>$id]);
    }
    
}
