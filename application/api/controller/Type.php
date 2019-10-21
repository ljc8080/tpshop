<?php

namespace app\api\controller;

use app\api\model\Type as TypeModel;
use think\Db;
use think\Request;

class Type extends Common
{

    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new TypeModel();
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $res = $this->m->show();
        $this->successInfo($res);
    }


    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save()
    { 
        
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        $res = $this->m->queryone($id);
        if($res){
            $this->successInfo($res);
        }else{
            $this->errorInfo('获取信息异常');
        }  
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //判断模型下是否有商品
        //$res = $this->m->with('goods')->where('type_id',$id)->count();
        $res = db('goods')->where('type_id', $id)->count('id');
        if ($res !== 0) {
            $this->errorInfo('请先删除该模型下的商品');
        }
        try {
            Db::startTrans();
            db('type')->where('id', $id)->delete();
            db('spec')->where('type_id', $id)->delete();
            db('spec_value')->where('type_id', $id)->delete();
            db('attribute')->where('type_id', $id)->delete();
            Db::commit();
            $this->successInfo('删除成功');
        } catch (\Exception $error) {
            Db::rollback();
            $this->errorInfo($error->getMessage() . $error->getLine(), 500);
        }
    }
}
