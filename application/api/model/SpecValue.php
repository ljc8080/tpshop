<?php

namespace app\api\model;

use think\Model;

class SpecValue extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];
}
