<?php

namespace app\api\controller;

use tool\jwt\Token;
use think\Controller;
use think\Exception;
use think\Image;

class Common extends Controller
{
    private $allow = ['LoginRegister/login', 'LoginRegister/captcha'];

    private function allowRequest()
    {

        //允许的源域名
        header("Access-Control-Allow-Origin: http://localhost:8080");
        //允许的请求头信息
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");
        //允许的请求类型
        header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE,OPTIONS,PATCH');
        //允许携带证书式访问（携带cookie）
        header('Access-Control-Allow-Credentials:true');
    }

    protected function _initialize()
    {
        $this->allowRequest();
        $this->checkLogin();
        if (!$this->checkAuth()) {
            $this->errorInfo('你没有足够的权限访问', 400);
        }
    }


    private function checkLogin()
    {
        $c =  $this->request->controller();
        $a = $this->request->action();
        $allow = $c . '/' . $a;
        if (!in_array($allow, $this->allow)) {
            try {
                $userid = Token::getUserId();
                if (!$userid) {
                    $this->errorInfo('请先登录');
                } else {
                    cookie('userid',$userid,3600*24);
                }
            } catch (\Exception $e) {
                $error = [];
                $error['msg'] = $e->getMessage();
                $error['file'] = $e->getFile();
                $error['line'] = $e->getFile();
                $this->errorInfo($error, 500);
            }
        }
    }

    protected function checkId($id)
    {
        $reg = '/^[1-9]+\d*$/';
        return preg_match($reg, $id);
    }

    protected function thumb($imgpath, $savepath, array $config = null)
    {
        if (!file_exists($imgpath)) {
            return false;
        }
        $imghandle = Image::open($imgpath);
        $conf = $config ?? config('thumb');
        $savepath = str_replace('uploads', 'thumb', $savepath);
        $dir = dirname($savepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $res = $imghandle->thumb($conf['width'], $conf['height'])->save($savepath);
        if ($res->width()) {
            return true;
        } else {
            return false;
        }
    }

    private function checkAuth()
    {
        $allow = ['LoginRegister/login', 'Index/index','LoginRegister/captcha','LoginRegister/logout'];
        $c = $this->request->controller();
        $a = $this->request->action();
        $auth = $c . '/' . $a;
        if (in_array($auth, $allow)) {
            return true;
        }
        $userid = cookie('userid') ?? false;
        if (!$userid) {
            return false;
        }
        if ($userid == 1) {
            return true;
        }
        try {
            $res = \app\api\model\Admin::with('role')->where('id', $userid)->find()->toArray();
            if (!$res) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
        if ($res['role_auth_ids'] == '*') {
            return true;
        }
        $id = db('auth')
            ->where([
                'auth_c' => $c,
                'auth_a' => $a
            ])->column('id');
        if (!$id) {
            return false;
        }
        $role_auth_ids = explode(',', $res['role_auth_ids']);
        if (!in_array($id[0], $role_auth_ids)) {
            return false;
        } else {
            return true;
        }
    }
}
