<?php

namespace app\home1\controller;

use think\Controller;

class Common extends Controller
{
    public function _initialize()
    {
        $this->cate();
        $this->rempass();
    }

    private function cate()
    {
        $cate = cache('category') ?? false;
        if (!$cate) {
            $cate = db('category')->field('create_time,update_time,delete_time', true)->order('sort', 'desc')->select();
            if ($cate) {
                $cate = get_tree_list($cate);
            } else {
                $cate = [];
            }
            cache('category', $cate, 86400);
        }
        $this->assign('cate', $cate);
    }

    private function rempass()
    {
        $userid = cookie('rempass') ?? false;
        if ($userid) {
            $info = db('user')->find($userid);
            if ($info) {
                session('home1_userinfo', $info);
            }
        }
    }

    public function checklogin($fn = null)
    {
        $userid = session('home1_userinfo.id') ?? false;
        if (!$userid) {
            if ($fn) {
                $fn();
            } else {
                return false;
            }
        } else {
            return $userid;
        }
    }

    public function checkid($id)
    {
        $reg = "/^[1-9]+\d*$/";
        return preg_match($reg, $id);
    }
}
