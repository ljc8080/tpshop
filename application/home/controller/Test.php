<?php

namespace app\home\controller;

use think\Controller;
use think\Cookie;

class Test extends Controller
{
    public function index()
    {
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index'
        ];
        $r = $es->indices()->exists($params);
        if(!$r){
            $es->indices()->create($params);
        }
    }

    public function data()
    {
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index', //库
            'type' => 'test_type',   //表
            'id' => 101,   //主键，一般与mysql的同步
            'body' => ['id' => 102, 'title' => 'PHP从入门到精通', 'author' => '张三']
        ];

        $r = $es->index($params);
        dump($r);
        die;
    }

    public function edit()
    {
        $es = \Elasticsearch\ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build();
        $params = [
            'index' => 'test_index',
            'type' => 'test_type',
            'id' => 100,  //修改的条件
            'body' => [
                //必须以doc
                'doc' => ['id' => 100, 'title' => 'ES从入门到精通', 'author' => '张三']
            ]
        ];

        $r = $es->update($params);
        dump($r);
        die;
    }

    public function cookie(){
       
    }
}
