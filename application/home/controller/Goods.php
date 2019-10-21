<?php

namespace app\home\controller;

use app\home\model\Goods as GoodsModel;
use app\search\controller\Es;
use think\Exception;
use think\Request;

class Goods extends Common
{
    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new GoodsModel();
    }


    public function index()
    {
        try {
            $cate_id = input('get.cateid') ?? false;
            $search = input('get.keywords') ?? false;
            if (!$search) {
                $this->checkid($cate_id, '/index');
                $condtion['cate_id'] = ['=', $cate_id];
                $param['cateid'] = $cate_id;
                $res = $this->m->list($condtion, $param);
                if (!$res) {
                    $page = '';
                    $res = [];
                } else {
                    $page = $res->render();
                    $res = $res->toArray();
                }
            } else { 
                //全文搜索
                $essearch = new Es();
                $res = $essearch->goodsSearch();
                if(!$res){
                    throw new Exception('es搜索失败');
                }
                $page = $res->render();
                $res = $res->toArray();
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
        $this->assign(
            [
                'page' => $page,
                'list' => $res['data'],
            ]
        );
        return view();
    }

    public function create()
    {
        //
    }

    public function save(Request $request)
    { }


    public function read($id)
    {
        $this->checkid($id);
        $res = $this->m->with('goodsImage,specGoods')->find($id);
        if (!$res) {
            $this->error('数据异常');
        }
        $goodsinfo = $res->toArray();
        $ids = [];
        foreach ($res['spec_goods'] as $v) {
            $id = explode('_', $v['value_ids']);
            $ids = array_merge($ids, $id);
        }
        $ids = array_unique($ids);
        $ids = implode(',', $ids);

        $res  = \app\home\model\SpecValue::with('specValueName')->where('id', 'in', $ids)->select();
        if (!$res) {
            $this->error('数据异常');
        }
        $obj = new \think\Collection($res);
        $res = $obj->toArray();
        $spec_info = [];
        foreach ($res as $v) {
            if (array_key_exists($v['spec_id'], $spec_info)) {
                $spec_info[$v['spec_id']]['value'][] =  [
                    'name' => $v['spec_value'], 'id' => $v['id']
                ];
            } else {
                $spec_info[$v['spec_id']] = [
                    'id' => $v['spec_id'], 'name' => $v['spec_name'], 'value' => [['name' => $v['spec_value'], 'id' => $v['id']]]
                ];
            }
        }

        //js获取商品价格的数组
        $goodprice = [];
        foreach ($goodsinfo['spec_goods'] as $v) {
            $goodprice[$v['value_ids']] = ['spec_goods_id' => $v['id'], 'goods_id' => $v['goods_id'], 'price' => $v['price']];
        }

        //查看数组结构
        // echo '<pre/>';
        // dump($goodsinfo);
        // print_r($spec_info);
        // print_r($goodprice);
        // die;

        $this->assign([
            'goodinfo' => $goodsinfo,  //101
            'specinfo' => $spec_info,
            'goodprice' => json_encode($goodprice)
        ]);
        return $this->fetch('item');
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function delete($id)
    {
        //
    }
}
