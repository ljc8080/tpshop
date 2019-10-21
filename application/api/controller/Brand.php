<?php

namespace app\api\controller;

use app\api\model\Brand as BrandModel;

class Brand extends Common
{

    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new BrandModel();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $keywords = input('keyword') ?? false;
        $cate_id = input('cate_id') ?? false;
        if (!$cate_id) {
            $condition = [];
            if ($keywords && !empty($keywords)) {
                $condition['name'] = ["like", "%{$keywords}%"];
            };
            $res = $this->m->show($condition);
        } else {
            $res = $this->m->show($cate_id);
        }
        if ($res == false) {
            $this->errorInfo('获取数据异常', 500);
        } else {
            $this->successInfo($res);
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'Brand');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        $logo = $data['logo'] ?? false;
        if ($logo) {
            if (!file_exists('.' . $logo)) {
                $this->errorInfo($logo . '不是一个文件');
            }
            $brand_logo_dir = str_replace('uploads', 'thumb', $logo);
            $res = $this->thumb('.' . $logo, '.' . $brand_logo_dir, ['width' => 200, 'height' => 100]);
            if (!$res) {
                $this->errorInfo('缩略图生成失败');
            }
            $data['logo'] = $brand_logo_dir;
        }
        $data['desc'] = $data['desc'] ?? '';
        $data['url'] = $data['url'] ?? '';
        $res = $this->m->add($data);
        if (!$res) {
            $this->errorInfo('添加失败', 500);
        } else {
            $this->successInfo($res, '添加成功');
        }
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = $this->m->getone($id);
        if (!$res) {
            $this->errorInfo('获取信息品牌异常', 500);
        } else {
            $this->successInfo($res);
        }
    }

    /**
     * 保存更新的资源
     */
    public function update($id)
    {
        $data = input('put.');
        $res = $this->validate($data, 'Brand');
        if ($res !== true) {
            $this->errorInfo($res);
        }
        $src = db('brand')->where('id', $id)->column('logo');
        $logo = $data['logo'];
        if (isset($logo)) {
            if ($logo != $src[0]) {
                $brand_logo_dir = str_replace('uploads', 'thumb', $logo);
                $res = $this->thumb('.' . $logo, '.' . $brand_logo_dir, ['width' => 200, 'height' => 100]);
                if (!$res) {
                    $this->errorInfo('生成缩略图失败');
                }
                $data['logo'] = $brand_logo_dir;
            } else {
                $data['logo'] = $src[0];
            }
        } else {
            if (!$src) {
                $this->errorInfo('获取缩略图信息失败', 500);
            }
            $data['logo'] = $src[0];
        }
        $res = $this->m->edit($data, $id);
        if (!$res) {
            $this->errorInfo('修改失败', 500);
        } else {
            $info = $this->m->getone($id);
            if (!$info) {
                $info = [];
            }
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
        //品牌下有商品不能删除
        $res = db('goods')
            ->alias('a')
            ->join('brand b', 'a.brand_id = b.id')
            ->where('b.id', $id)
            ->count('a.id');
        if ($res === false) {
            $msg = '系统繁忙，请稍后再试';
            $code = 500;
            $this->errorInfo($msg, $code);
        } elseif ($res > 0) {
            $msg = '请先删除品牌下的商品';
            $code = 400;
            $this->errorInfo($msg, $code);
        }
        $res = db('goods')->where('id', $id)->delete();
        if ($res) {
            $this->successInfo(null, '删除成功');
        } else {
            $this->errorInfo('删除失败', 500);
        }
    }
}
