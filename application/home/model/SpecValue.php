<?php

namespace app\home\model;

use think\Model;

class SpecValue extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];
    public function specValueName(){
        return $this->belongsTo('spec','spec_id','id')->bind('spec_name');
    }
}
