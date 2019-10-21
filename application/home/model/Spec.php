<?php

namespace app\home\model;

use think\Model;

class Spec extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];
}
