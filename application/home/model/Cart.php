<?php

namespace app\home\model;

use think\Exception;
use think\Model;

class Cart extends Model
{
    protected $hidden = ['create_time', 'update_time', 'delete_time'];

    public function goods(){
        return $this->belongsTo('goods','goods_id','id');
    }

    public function sku(){
        return $this->belongsTo('spec_goods','spec_goods_id','id');
    }

    public function edit($condition, $param)
    {
        try {
            //先在数据库中查询
            $obj = self::where($condition)->find();

            if (!$obj) {
                //如果没有查到，新增记录
                $data = [];
                foreach ($condition as $k => $v) {
                    $data[$k] = $v;
                }
                $data = array_merge($data, $param);
                return self::create($data, true);
            } else {
                //查到了修改记录
                foreach ($param as $k => $v) {
                    //支持动态修改多种字段(修改数量不要使用此方法)
                    if ($k == 'number') {
                        $obj->$k += $v;
                    } else {
                        $obj->$k = $v;
                    }
                }
                $obj->save();
                return true;
            }
        } catch (Exception $e) {
            return $e->getMessage() . $e->getLine();
        }
    }

    public function queryGoods($goods_id, $spec_goods_id = 0)
    {
        //该方法第一次查询从数据库查，以后从缓存中读取
        //动态设置缓存名称
        $name =  "shopcar_{$goods_id}_{$spec_goods_id}";
        //判断是否写到缓存中
        $info = cache($name);

        //没写到缓存中则查询数据库，否则直接读取缓存
        if (!$info) {
            $condtion = [
                'b.id' => ['=', $goods_id],
            ];
            if ($spec_goods_id) {
                $condtion['a.id'] = ['=', $spec_goods_id];
            }
            $info = db('spec_goods')
                ->alias('a')
                ->join('goods b', 'a.goods_id=b.id', 'left')
                ->where($condtion)
                ->find();
            if (!$info) {
                return false;
            } else {
                cache($name, $info, 86400);
            }
        }
        return $info;
    }

    public function editnum($id, $num)
    {

        return self::save(['number' => $num], ['id' => $id]);
    }

    public function editStatus($data)
    {
        $userid = session('home_userinfo.id') ?? false;
        if (!$userid) return false;
        extract($data);
        if ($id == '*') {
            return $this->where('user_id', $userid)->update(['is_selected'=>$is_selected]);
        } else {
            return $this->allowField(true)->isUpdate(true)->save($data);
        }
    }
}
