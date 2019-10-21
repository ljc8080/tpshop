<?php

namespace app\api\controller;


class Upload extends  Common
{
    private $rules = ['size' => 1024 * 1024 * 3, 'ext' => 'jpg,png,gif', 'type' => 'image/jpeg,image/png/image/gif'];
    private $dir = ROOT_PATH . 'public' . DS . 'uploads' . DS;
    private $allowpath = ['goods', 'category', 'brand'];

    //上传单文件(logo)
    public function logo()
    {
        $type = input('post.type') ?? false;
        $img = input('file.logo') ?? false;
        if (!$type) {
            $this->errorInfo('type参数不能为空');
        } elseif (!$img) {
            $this->errorInfo('请上传图片');
        } else {
            if (!in_array($type, $this->allowpath)) {
                $this->errorInfo('type参数错误');
            }
            $res = $img->validate($this->rules)->move($this->dir . $type);
            if (!$res) {
                $this->errorInfo($img->getError());
            } else {
                $this->successInfo(DS . 'uploads' . DS . $type . DS . $res->getSaveName());
            }
        }
    }

    //上传多文件(图片)
    public function images()
    {
        $type = input('post.type') ?? 'goods';
        $img = input('file.images') ?? false;
        if (!$img) {
            $this->errorInfo('请上传图片');
        } else if (!in_array($type, $this->allowpath)) {
            $this->errorInfo('type参数错误');
        }

        $data = [];
        foreach ($img as $v) {
            $res = $v->validate($this->rules)->move($this->dir . $type);
            if (!$res) {
                $data['error'][] = ['name' => $v->getInfo('name'), 'msg' => $v->getError()];
            } else {
                $url = DS . 'uploads' . DS . $type . DS . $res->getSaveName();
                $data['success'][] = str_replace('\\','/',$url);
            }
        }
        if (array_key_exists('error', $data)) {
            $info = '部分文件上传失败';
        } else {
            $info = '上传成功';
        }
        $this->successInfo($data, $info);
    }
}
