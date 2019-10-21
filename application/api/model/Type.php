<?php

namespace app\api\model;

use think\Model;

class Type extends Model
{
    protected $visible = ['id', 'type_name', 'specs', 'spec_values','attrs'];

    public function specs()
    {
        return $this->hasMany('spec', 'type_id', 'id');
    }

    public function attrs(){
        return $this->hasMany('attribute', 'type_id', 'id');
    }

    public function goods(){
        return $this->hasMany('goods', 'type_id', 'id');
    }

    public function show()
    {
        $res =  self::select();
        if ($res) {
            $obj = new \think\Collection($res);
            return $obj->toArray();
        } else {
            return [];
        }
    }

    public function queryone($id)
    {
        return self::with('specs,specs.specValues,attrs')->find($id);
    }
}
