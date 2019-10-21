<?php

namespace app\home1\controller;

use app\home1\model\Goods as GoodsModel;
use app\home1\model\SpecValue;
use think\Collection;

class Goods extends Common
{

    private $m;
    public function _initialize()
    {
        parent::_initialize();
        $this->m = new GoodsModel();
    }
    public function list()
    {
        $id = $this->request->param('cateid');
        if (!$this->checkid($id)) {
            $data = [];
        } else {
            $data = db('goods')
                ->where(
                    [
                        'cate_id' => $id,
                        'is_on_sale' => 1
                    ]
                )
                ->order('sort', 'desc')
                ->paginate(20);
            if (!$data) $data = [];
        }
        $this->assign('goods', $data);
        return view();
    }

    public function read()
    {
        $id = $this->request->param('id');
        $data = $this->m->with('goodsImage,specgoods')->find($id)->toArray();
        $ids = [];
        if (isset($data['specgoods'])) {
            foreach ($data['specgoods'] as $v) {
                $id = explode('_', $v['value_ids']);
                $ids = array_merge($ids, $id);
            }
            $ids = array_unique($ids);
            $a = SpecValue::with('spec')->where('id', 'in', $ids)->select();
            $a = (new Collection($a))->toArray();
            $mapping = [];
            foreach($data['specgoods'] as $key=> $v){
                $mapping[$v['value_ids']] = $v;
            }
            $specgoods = [];
            foreach ($a as $v) {
                if (!array_key_exists($v['spec_id'], $specgoods)) {
                    $specgoods[$v['spec_id']] = [
                        'name' => $v['spec_name'],
                        'id' => $v['spec_id'],
                        'value' => [[
                            'id' => $v['id'],
                            'name' => $v['spec_value']
                        ]],
                    ];
                } else {
                    $specgoods[$v['spec_id']]['value'][] = [
                        'id' => $v['id'],
                        'name' => $v['spec_value']
                    ];
                }
            }
            $data['specgoods'] = $specgoods;
        } else {
            $data['specgoods'] = [];
            $mapping = '';
        }
        
        return view('item', ['goodinfo' => $data,'mapping'=>json_encode($mapping)]);
    }
}
