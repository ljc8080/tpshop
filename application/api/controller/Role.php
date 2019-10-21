<?php

namespace app\api\controller;

use app\api\model\Auth;
use app\api\model\Role as RoleModel;

class Role extends Common
{
    private $m;
    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new RoleModel();
    }
    public function index()
    {
        /* 
         {
            "id": 1,
            "role_name": "超级管理员",
            "desc": null,
            "role_auths":[
                {id: 1, auth_name: "首页", pid: 0, pid_path: "0", son: [...]},
                ...
            ]
        },
         */
        $data = $this->m->show();
        if (!$data) {
            $this->errorInfo('获取数据异常');
        }
        $a = new Auth();
        $res = $a->show();
        if (!$res) {
            $this->errorInfo('获取数据异常');
        }
        $auths = [];
        foreach ($res as $v) {
            $auths[$v['id']] = $v;
        }
        foreach ($data as $key => $v) {
            if ($v['id'] == 1) {
                $data[$key]['roles_auth'] = '*';
                continue;
            }
            $roleauth = explode(',', $v['role_auth_ids']);
            foreach ($roleauth as $v) {
                $data[$key]['role_auth'][] = $auths[$v];
            }
            $data[$key]['role_auth'] = get_tree_list($data[$key]['role_auth']);
        }

        $this->successInfo($data);
    }


    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'Role');
        if ($res !== true) {
            $this->errorInfo($res);
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
            $this->errorInfo('获取角色失败');
        } else {
            $this->successInfo($res);
        }
    }


    public function update($id)
    {
        $data = input('put.');
        $res = $this->validate($data, 'Role');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        $res = $this->m->edit($data, $id);
        if ($res) {
            $this->successInfo($res, '修改成功');
        } else {
            $this->errorInfo('修改失败', 500);
        }
    }

    public function delete($id)
    {
        $res = db('admin')->where('role_id', $id)->count();
        if ($res > 0) {
            $this->errorInfo('请先解除正在使用该角色的用户');
        } elseif ($res == 0) {
            $res = db('role')->where('id', $id)->delete();
            if (!$res) {
                $this->errorInfo('删除失败', 500);
            } else {
                $this->successInfo('删除成功');
            }
        } else {
            $this->errorInfo('系统异常请稍后再试', 500);
        }
    }
}
