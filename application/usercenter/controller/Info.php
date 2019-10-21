<?php

namespace app\usercenter\controller;

use think\Db;
use think\Exception;
use think\Validate;

class Info extends Common
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if (request()->isGet()) {
            //查询该用户的资料显示
            $userid = session('home_userinfo.id');
            $res = db('user')
                ->alias('a')
                ->join('profile b', 'a.id=b.uid')
                ->where('a.id', $userid)
                ->field('a.id,sex,nickname,phone,figure_url,is_vip,card,birthday,place,job')
                ->find();
            if (!$res) {
                $this->error('数据异常');
            }
            if ($res['birthday']) {
                $res['birthday'] = explode('/', $res['birthday']);
            }
            $job = db('job')->field('update_time,create_time,delete_time', true)->select();
            if (!$job) {
                $job = [];
            } else {
                $job = get_tree_list($job);
            }
            if ($res['job']) {
                $res1 = db('job')->where('id', $res['job'])->value('pid_path');
                if (!$res1) {
                    $userjob = [];
                } else {
                    $userjob = explode('_', $res1);
                    array_shift($userjob);
                    array_push($userjob, $res['job']);
                }
            } else {
                $userjob = [];
            }
            if (!$res['place']) {
                $res['place'] = [];
            } else {
                $res['place'] = explode('_', $res['place']);
            }
            //dump($res);die;
            $this->assign(['userinfo' => $res, 'job' => $job, 'userjob' => json_encode($userjob)]);
            return view();
        } elseif (request()->isPost()) { } else {
            $this->error('非法请求');
        }
    }

    public function job()
    {
        $pid = input('get.pid') ?? 0;
        $conidtion['pid'] = ['=', $pid];
        $job = db('job')->where($conidtion)->field('update_time,create_time,delete_time', true)->select();
        if ($job) {
            $this->successInfo($job);
        } else {
            return $this->errorInfo('获取信息异常');
        }
    }

    public function edit()
    {
        $userinfo = session('home_userinfo');
        $data = input('post.');
        $data['birthday'] = $this->request->post('birthday', null, 'trim');
        $validate = new Validate([
            'nickname|昵称'  => 'require|max:13',
            'sex|性别' => 'require|in:0,1',
            'job|工作种类' => 'regex:/^[1-9]+\d*$/',
        ]);
        if (!$validate->check($data)) {
            $this->errorInfo($validate->getError());
        }

        Db::startTrans();
        extract($data);
        try {
            db('profile')
                ->where('uid', $userinfo['id'])
                ->update(
                    [
                        'sex' => $sex,
                        'job' => $job,
                        'place' => $place,
                        'birthday' => $birthday,
                        'update_time' => time()
                    ]
                );
            db('user')->where('id', $userinfo['id'])->update(['nickname' => $nickname]);
        } catch (Exception $e) {
            Db::rollback();
            $this->errorInfo($e->getMessage());
        }
        Db::commit();

        $userinfo['nickname'] = $nickname;
        session('home_userinfo', $userinfo);
        $this->successInfo($data);
    }

    public function editheader()
    {
        $userinfo = session('home_userinfo');
        $userid = $userinfo['id'];
        $file = $this->request->file('figure_url');
        if (!$file) {
            $this->errorInfo('请上传一张图片');
        }
        $dir = "./uploads/user/figure/";
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $res = $file->validate(['size' => 1024 * 1024 * 13, 'ext' => 'jpg,png,gif', 'type' => 'image/jpeg,image/png,image/gif'])->move($dir);
        if (!$res) {
            $this->errorInfo($file->getError());
        }
        $src = $res->getSaveName();
        $path = $dir . $src;
        $image = \think\Image::open($path);
        $dir = dirname(substr($path, 1));
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $src = $dir . '/' . basename($path);
        $image->thumb(100, 100)->save('.' . $src);
        db('user')->where('id', $userid)->update(['figure_url' => 'http://www.pyg.com' . $src]);
        $this->successInfo('http://www.pyg.com' . $src);
    }
}
