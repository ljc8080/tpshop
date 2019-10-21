<?php

namespace app\home1\model;

use think\Model;

class SpecValue extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function spec(){
        return $this->belongsTo('spec','spec_id','id')->bind('spec_name');
    }
}
