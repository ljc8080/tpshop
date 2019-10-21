<?php

namespace app\api\controller;

use app\api\model\Admin as AdminModel;

class Admin extends Common
{
    private $m;
    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new AdminModel();
    }

    public function index()
    {
        $keyword = input('get.keyword') ?? false;
        $page = input('get.page') ?? 10;
        if ($keyword !== false && strlen($keyword) > 10) {
            $keyword = substr($keyword, 0, 10);
        }
        if (!preg_match('/^[1-9]+\d*$/', $page)) {
            $page = 10;
        }
        $condition = [];
        if (!empty($keyword)) {
            $condition['username'] = ['like', "%{$keyword}%"];
        }
        $res = $this->m->show($condition, $page);
        $this->successInfo($res);
    }


    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'admin');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        if (!isset($data['password'])) {
            $data['password'] = '123456';
        }
        $res = $this->m->add($data);
        if (!$res) {
            $this->errorInfo('添加失败', 500);
        } else {
            unset($res['password']);
            $this->successInfo($res);
        }
    }


    public function read($id)
    {
        $res = $this->m->queryone($id);
        if (!$res) {
            $this->errorInfo('获取信息异常', 500);
        } else {
            $this->successInfo($res);
        }
    }


    public function update($id)
    {
        //判断是否允许修改
        $userid = cookie('userid');
        if ($id == 1 && $userid != 1) {
            $this->errorInfo('无权修改超级管理员');
        }
        $auth = db('admin')
            ->alias('a')
            ->join('role b', 'a.role_id = b.id')
            ->where('a.id', $id)
            ->column('role_auth_ids');
        if ($auth && $auth[0] == '*' && $userid != 1) {
            $this->errorInfo('无权修改一级管理员');
        }
        $reset_pwd = input('put.type') ?? false;
        //重置密码
        if ($reset_pwd && $reset_pwd == 'reset_pwd') {
            $res = db('admin')->where('id', $id)->update(['password' => encryption('123456')]);
            if (!$res) {
                $this->errorInfo('密码重置错误');
            }
            if ($id == $userid) {
                $login = new LoginRegister();
                $login->logout();
            } else {
                $this->successInfo('修改成功');
            }
        } else {
            //修改用户信息
            $data = input('put.');
            $data['id'] = $id;
            $res = $this->validate($data, 'admin');
            if ($res !== true) {
                $this->errorInfo($res);
            }
            if (isset($data['username'])) {
                unset($data['username']);
            }
            $res = $this->m->edit($data, $id);
            if (!$res) {
                $this->errorInfo('修改失败');
            } else {
                unset($res['password']);
                $this->successInfo($res, '修改成功');
            }
        }
    }


    public function delete($id)
    {
         //判断是否允许删除
         $userid = cookie('userid');
         if ($id == 1) {
             $this->errorInfo('无权删除超级管理员');
         }
         $auth = db('admin')
             ->alias('a')
             ->join('role b', 'a.role_id = b.id')
             ->where('a.id', $id)
             ->column('role_auth_ids');
         if ($auth && $auth[0] == '*' && $userid != 1) {
             $this->errorInfo('无权删除一级管理员');
         }
         if($userid==$id){
             $this->errorInfo('不能自己删除自己');
         }
         $res = db('admin')->where('id',$id)->delete();
         if(!$res){
             $this->errorInfo('删除失败');
         }
         $this->successInfo('删除成功');
    }
}
