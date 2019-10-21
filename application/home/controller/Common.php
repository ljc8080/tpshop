<?php

namespace app\home\controller;

use app\home\model\Cart;
use think\Controller;

class Common extends Controller
{
    protected function _initialize()
    {
        parent::_initialize();
        $this->allowRequest();
        $this->cate();
    }
    private function allowRequest()
    {

        //允许的源域名
        header("Access-Control-Allow-Origin: *");
        //允许的请求头信息
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        //允许的请求类型
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        //允许携带证书式访问（携带cookie）
        header('Access-Control-Allow-Credentials:true');
    }

    private function cate()
    {
        //查询分类信息写入缓存
        if (!cache('home_category')) {
            $category = db('category')
                ->where('is_show', '1')
                ->field('create_time,update_time,delete_time', true)
                ->order('sort', 'desc')
                ->select();
            if (!$category) {
                $category = [];
            } else {
                $category = get_tree_list($category);
            }
            cache('home_category', $category, 86400);
        } else {
            $category = cache('home_category');
        }
        $this->assign('category', $category);
    }

    public function checkid($id, $url = null)
    {
        $reg = "/^[1-9]+\d*$/";
        if (!preg_match($reg, $id)) {
            if($url){
                $this->redirect($url);
            }else{
                return false;
            } 
        }else{
            return true;
        }
    }


    //登陆后购物车迁移
    protected function shopcarinfo()
    {
        $userid = session('home_userinfo.id') ?? false;
        $goodsinfo = cookie('shopcar') ?? [];
        if(!$userid){
            return false;
        }
        if (!empty($goodsinfo)) {
            foreach ($goodsinfo as $v) {
                $condition = [
                    'user_id' =>  $userid,
                    'goods_id' => $v['goods_id'],
                    'spec_goods_id' => $v['spec_goods_id'],
                ];
                $m = new Cart();
               $m->edit($condition, ['number'=>$v['number']]);
            }
        }
        cookie('shopcar',null);
    }
}
