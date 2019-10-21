<?php

namespace app\api\controller;

use app\api\model\Auth as AuthModel;
use think\Exception;

class Auth extends Common
{
    private $m;
    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new AuthModel();
    }

    public function index()
    {
        $type = input('get.type') ?? 'cate';
        $res = $this->m->show();
        if ($res) {
            if ($type == 'cate') {
                $res = get_cate_list($res);
            } else {
                $res = get_tree_list($res);
            }
        } else {
            $this->errorInfo('获取权限列表异常');
        }
        $this->successInfo($res);
    }

    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'Auth');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        if ($data['is_nav'] == 0) {
            if (empty($data['auth_c']) || empty($data['auth_a'])) {
                $this->errorInfo('请填写控制器和方法');
            }
        }
        if ($data['pid'] == 0) {
            $data['pid_path'] == 0;
            $data['level'] == 0;
        } else {
            $res = db('auth')
                ->where('id', $data['pid'])
                ->field('pid_path,level')
                ->find();
            if (!$res) {
                $this->errorInfo('系统繁忙请稍后再试');
            }
            $data['pid_path'] = $res['pid_path'] . "_{$data['pid']}";
            $data['level'] = $res['level'] + 1;
        }
        $res = $this->m->add($data);
        if ($res) {
            $this->successInfo($res, '添加成功');
        } else {
            $this->errorInfo('添加失败', 500);
        }
    }

    public function read($id)
    {
        $res = $this->m->queryone($id);
        if (!$res) {
            $this->errorInfo('获取权限失败');
        } else {
            $this->successInfo($res);
        }
    }

    public function update($id)
    {
        $data = input('put.');
        $res = $this->validate($data, 'Auth');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        if ($data['is_nav'] == 0) {
            if (empty($data['auth_c']) || empty($data['auth_a'])) {
                $this->errorInfo('请填写控制器和方法');
            }
        }
        $pinfo = db('auth')
            ->where('id', $data['pid'])
            ->field('level,pid_path')
            ->find();
        if (!$pinfo) {
            $this->errorInfo('系统繁忙，请稍后再试');
        }
        $level = db('auth')
            ->where('id', $id)
            ->column('level');
        if ($pinfo['level'] >= $level[0]) {
            $this->errorInfo('父级权限选择失败');
        }
        if ($data['pid'] == 0) {
            $data['pid_path'] == 0;
            $data['level'] == 0;
        } else {
            $data['pid_path'] = $pinfo['pid_path'] . "_{$data['pid']}";
            $data['level'] = $pinfo['level'] + 1;
        }
        $res = $this->m->edit($data, $id);
        if (!$res) {
            $this->errorInfo('修改失败');
        } else {
            $this->successInfo($res);
        }
    }

    public function delete($id)
    {
        //权限下有子权限，不得删除数据
        try {
            $res = db('auth')->where('pid', $id)->count();
            if ($res > 0) {
                throw new Exception('请先删除该权限下的子权限');
            }else{
                $res = db('auth')->where('id',$id)->delete();
                if(!$res){
                    throw new Exception('删除失败');
                }
            }
        } catch (Exception $e) {
            $this->errorInfo($e->getMessage());
        }
        $this->successInfo('删除成功');
    }

    public function nav()
    {
        $res = $this->m->nav();
        if ($res) {
            $this->successInfo(get_tree_list($res));
        } else {
            $this->errorInfo('获取数据异常');
        }
    }
}
