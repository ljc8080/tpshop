<?php

namespace app\api\controller;

use app\api\model\Category as CategoryModel;

class Category extends Common
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new CategoryModel();
    }

    public function index()
    {
        $pid = input('get.pid') ?? null;
        $type = input('get.type') ?? 'tree';
        if ($type != 'cate' && $type != 'tree') {
            $this->errorInfo('type参数错误');
        }
        $condition = [];
        if ($pid) {
            $res = $this->validate(['pid' => $pid], 'Category.show');
            if ($res !== true) {
                $this->errorInfo($res);
            }
            $condition['pid'] = $pid;
        }
        $res = db('category')
            ->order('sort', 'desc')
            ->field(['pid_path'], true)
            ->where($condition)
            ->select();
        if (!$res || count($res) == 0) {
            $this->errorInfo('获取数据异常', 500);
        }
        if (!isset($pid)) {
            $fn = "get_{$type}_list";
            $res = $fn($res);
        }
        $this->successInfo($res);
    }


    /**
     * 保存新建的资源
     */
    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'category.add');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        $logo = $data['logo'];
        if (!file_exists('.' . $logo)) {
            $this->errorInfo('文件不存在');
        }
        if ($data['pid'] == 0) {
            $data['pid_path'] = 0;
            $data['pid_pathname'] = '';
        } else {
            $info = db('category')->field('pid_path,cate_name,pid_path_name')->getById($data['pid']);
            if (!$info) {
                $this->error('获取分类信息失败', 500);
            }
            $data['pid_path'] = "{$info['pid_path']}_{$data['pid']}";
            $data['pid_path_name'] = "{$info['pid_path_name']}_{$info['cate_name']}";
        }
        $savepath = str_replace('uploads', 'thumb', $logo);
        $res = $this->thumb('.' . $logo, '.' . $savepath);
        if (!$res) {
            $this->errorInfo('缩略图绘制失败');
        }
        $data['image_url'] = $savepath;
        unset($data['logo']);
        $res = $this->m->add($data);
        $info = $res->toArray();
        $this->successInfo($info, '添加成功');
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = db('category')
            ->field(['pid_path'], true)
            ->getById($id);
        if ($res) {
            $this->successInfo($res);
        } else {
            $this->errorInfo('获取分类信息失败');
        }
    }


    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update($id)
    {
        $data = input('put.');
        $res = $this->validate($data, 'category.edit');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        if ($id == $data['pid']) {
            $this->errorInfo('分类选择错误，修改无效' . __LINE__);
        }
        if ($data['pid'] == 0) {
            $data['pid_path'] = 0;
            $data['pid_path_name'] = $data['cate_name'];
        } else {
            $res = db('category')
                ->field('image_url,level')
                ->getById($id);
            $res1 = db('category')->where('id', $data['pid'])
                ->field('level,pid_path,pid_path_name')
                ->find();
            if (!$res || !$res1) {
                $this->errorInfo('获取分类信息异常' . __LINE__);
            }
            //pid的级别必须大于当前id的级别
            if ($res['level'] >= $res1['level']) {
                $this->errorInfo('分类选择错误，修改无效' . __LINE__);
            }
            $data['pid_path'] = "{$res1['pid_path']}_{$data['pid']}";
            $data['pid_path_name'] = "{$res1['pid_path_name']}_{$data['cate_name']}";
        }
        $data['image_url'] = $data['logo'];
        if ($data['logo'] != substr_replace('thumb', 'uploads', $data['logo'])) {
            $savepath = str_replace('uploads', 'thumb', $data['logo']);
            $res = $this->thumb('.' . $data['logo'], '.' . $savepath);
            if (!$res) {
                $this->errorInfo('缩略图生成失败');
            } else {
                $data['image_url'] = $savepath;
            }
        }
        unset($data['logo']);
        $res = $this->m->edit($data, $id);
        if ($res === false) {
            $this->errorInfo('更新失败');
        } else {
            $info = db('category')->getById($id);
            if (!$info) $info = [];
            $this->successInfo($info, '修改成功');
        }
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {

        //查询是否有子分类
        $res = db('category')
            ->where('pid', $id)
            ->count();
        if ($res != 0) {
            $this->errorInfo('删除失败，请先删除该类别下的子分类');
        }
        //查询品牌和商品是否绑定该分类，如果有不能删除
        $res = db('goods')
            ->alias('a')
            ->join('category b', 'a.cate_id = b.id')
            ->where('b.id=' . $id)
            ->count('a.id');
        if ($res != 0) {
            $this->errorInfo('删除失败，请先删除该类别下的商品');
        }
        $res = db('brand')
            ->alias('a')
            ->join('category b', 'a.cate_id = b.id')
            ->where('b.id=' . $id)
            ->count('a.id');
        if ($res != 0) {
            $this->errorInfo('删除失败，请先删除该类别下的品牌');
        }

        $res = db('category')->where('id', $id)->delete();
        if ($res) {
            $this->successInfo(null, '删除成功');
        } else {
            $code = $res == 0 ? 400 : 500;
            $this->errorInfo('删除失败', $code);
        }
    }
}
