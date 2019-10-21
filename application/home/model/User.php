<?php

namespace app\home\model;

use think\Exception;
use think\Model;

class User extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function setPasswordAttr($v)
    {
        return encryption($v);
    }


    public function add($data)
    {
        try {
            return self::create($data, true);
        } catch (Exception $e) {
            return false;
        }
    }

    public function is_allow($condition)
    {
        if (empty($condition)) {
            return false;
        }
        try {
            $data = $this->where($condition)->find();
            return $data;;
        } catch (Exception $e) {
            return false;
        }
    }

    public function thirduser($type, $key)
    {
        try {
            return $this->where(
                [
                    'open_type' => $type,
                    'openid' => $key
                ]
            )->find();
        } catch (Exception $e) {
            return false;
        }
    }

    public function edit($data, $condition)
    {
        try {
            return $this->allowField(true)
                ->where($condition)
                ->update($data);
        } catch (Exception $e) {
            return false;
        }
    }
}
