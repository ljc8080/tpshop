<?php

namespace app\script\controller;

use tool\es\MyElasticsearch;

class Es
{

    public function create()
    {
        try {
            $es = new MyElasticsearch();
            //如果存在则删除
            if ($es->exists_index('goods_index')) $es->delete_index('goods_index');
            //创建索引
            $es->create_index('goods_index');
            $i = 0;
            while (true) {
                $goods =  db('goods')
                    ->alias('a')
                    ->join('category b', 'a.cate_id=b.id')
                    ->field('a.id,cate_name,goods_name,goods_desc, goods_price,goods_logo,comments_num,collect_num')
                    ->limit($i,500)
                    ->select();
                    if(!$goods){
                        break;
                    }
                foreach($goods as $v){
                    //添加文档
                    $es->add_doc($v['id'],$v,'goods_index','goods_type');
                }
                $i+=500;
            }
            die('success');
        } catch (\Exception $e) {
            echo 'error:'.$e->getMessage();
            die();
        }
    }
}
