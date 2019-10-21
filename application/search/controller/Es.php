<?php

namespace app\search\controller;

use think\Controller;
use think\Exception;
use tool\es\MyElasticsearch;

class Es extends Controller
{
    public function goodsSearch($keywords = null)
    {
        $es = new MyElasticsearch();
        if (!$keywords) $keywords = input('get.keywords');
        //es分页参数
        $size = 10;
        $page = input('get.page') ?? 1;
        if (!preg_match('/^[1-9]+\d*$/', $page)) {
            $page = 1;
        }
        $from = ($page - 1) * $size;
        $body = [
            'query' => [
                'bool' => [
                    'should' => [
                        [
                            'match' => [
                                'cate_name' => [
                                    'query' => $keywords,
                                    'boost' => 4, // 权重大
                                ]
                            ]
                        ],
                        [
                            'match' => [
                                'goods_name' => [
                                    'query' => $keywords,
                                    'boost' => 3,
                                ]
                            ]
                        ],
                        [
                            'match' => [
                                'goods_desc' => [
                                    'query' => $keywords,
                                    'boost' => 2,
                                ]
                            ]
                        ]
                    ],
                ],
            ],
            'sort' => ['id' => ['order' => 'desc']],
            'from' => $from,   //第几条开始取
            'size' => $size    //取几条
        ];
        try {
            //查询到的数组结构
            $res = $es->search_doc('goods_index', 'goods_type', $body);
            //从数组结构中组装商品信息
            $data = array_column($res['hits']['hits'],'_source');
            //获取总数量
            $total = $res['hits']['total']['value'];
            $list = \tool\es\EsPage::paginate($data, $size, $total, ['query'=>['keywords'=>$keywords]]);
            return $list;
        } catch (Exception $e) {
            return false;
        }
    }
}
