<?php

namespace app\usercenter\controller;

use think\Controller;

class Common extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
        $this->cate();
        if (!session('?home_userinfo')) {
            $this->redirect('home/index/index');
        }
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
}
