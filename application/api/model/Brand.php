<?php

namespace app\api\model;

use think\Model;

class Brand extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function category(){
        return $this->belongsTo('Category','cate_id','id')->bind('cate_name');
    }
    
    public function show($condition)
    {
        if (is_array($condition)) {
            return $this->alias('a')
                ->join('category b', 'a.cate_id = b.id')
                ->order('sort', 'desc')
                ->field(['a.*', 'b.cate_name'])
                ->where($condition)
                ->paginate(15)
                ->toArray();
        } else {
            $data =  self::where('cate_id', $condition)->select();
            $obj = new \think\Collection($data);
            return $obj->toArray();
        }
    }

    public function add($data){
        return self::create($data,true)->toArray();
    }

    public function edit($data,$id){
        return $this->allowField(true)
        ->save($data,['id'=>$id]);
    }

    public function getone($id){
        $obj = self::get($id);
        if(is_object($obj)){
            return $obj->toArray();
        }else{
            return false;
        }
    }
}
