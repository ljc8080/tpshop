<?php

namespace app\api\controller;

use app\api\model\Category;
use app\api\model\Type;
use app\api\model\Goods as GoodsModel;
use app\api\model\GoodsImages;
use think\Db;
use think\Exception;


/* 
    'goods_images' => [
        '/uploads/goods/20190101/dfsssadsadsada.jpg',
        '/uploads/goods/20190101/adsafasdadsads.jpg',
        '/uploads/goods/20190101/dsafadsadsaasd.jpg',
    ],
    'item' => [
        '18_21' => [
            'value_ids'=>'18_21', 
            'value_names'=>'颜色：黑色；内存：64G', 
            'price'=>'8900.00', 
            'cost_price'=>'5000.00', 
            'store_count'=>100
        ],
        '18_22' => [
            'value_ids'=>'18_22', 
            'value_names'=>'颜色：黑色；内存：128G', 
            'price'=>'9000.00', 
            'cost_price'=>'5000.00', 
            'store_count'=>50
        ]
    ],
    'attr' => [
        '7' => ['id'=>'7', 'attr_name'=>'毛重', 'attr_value'=>'150g'],
        '8' => ['id'=>'8', 'attr_name'=>'产地', 'attr_value'=>'国产'],
    ]
*/

class Goods extends Common
{
    private $m;

    protected function _initialize()
    {
        parent::_initialize();
        $this->m = new GoodsModel();
    }

    public function index()
    {
        $pagecode = input('get.page') ?? 10;
        $condition = [];
        $search = input('get.keywords') ?? false;
        if (!empty($search)) {
            $condition['goods_name'] = ['like', "%{$search}%"];
        }
        $res = $this->m->show($pagecode, $condition);
        $this->successInfo($res);
    }

    public function save()
    {
        $data = input('post.');
        $res = $this->validate($data, 'goods');
        if ($res !== true) {
            $this->errorInfo($res);
        } elseif (!file_exists('.' . $data['goods_logo'])) {
            $this->errorInfo('商品logo地址不正确');
        } elseif (empty($data['attr']) || empty($data['item'])) {
            $this->errorInfo('请先添加商品规格或属性');
        } elseif (empty($data['goods_images'])) {
            $this->errorInfo('请先上传商品图片');
        }
        $response = [];
        $logo = $data['goods_logo'];
        $savepath = str_replace('uploads', 'thumb', $logo);
        $res = $this->thumb('.' . $logo, '.' . $savepath, ['width' => 200, 'height' => 240]);
        if (!$res) {
            $this->errorInfo('商品logo生成失败', 500);
        } else {
            $response['goods_logo'] = $savepath;
        }
        if (isset($data['goods_desc'])) {
            $data['goods_desc'] = remove_xss($data['goods_desc']);
        }
        $data['goods_attr'] = json_encode($data['attr']);
        try {
            Db::startTrans();
            $goodid = $this->m->add($data);
            if (!$goodid) {
                throw new Exception('商品添加失败');
            }

            foreach ($data['goods_images'] as $v) {
                $url = '.' . str_replace('uploads', 'thumb', $v);
                $imgname = basename($url);
                $imgpath = '.' . dirname($url);
                $bigpath = $imgpath . "/800_$imgname";
                $smallpath = $imgpath . "/400_$imgname";
                $res = $this->thumb($url, $bigpath, ['width' => 800, 'height' => 800]);
                if (!$res) {
                    throw new Exception('商品图片big生成错误');
                }
                $res = $this->thumb($url, $smallpath, ['width' => 800, 'height' => 800]);
                if (!$res) {
                    throw new Exception('商品图片small生成错误');
                }
                $imgdata[] = ['goods_id' => $goodid, 'pics_big' => $bigpath, 'pics_sma' => $smallpath];
            }
            $res = db('goods_images')->insertAll($imgdata);
            if (!$res) {
                throw new Exception('商品图片添加失败');
            }

            $spec_goods = [];
            foreach ($data['item'] as $v) {
                if (!is_array($v)) {
                    continue;
                }
                $v['goods_id'] = $goodid;
                $spec_goods[] = $v;
            }
            if (empty($spec_goods)) {
                throw new Exception('请填写商品sku');
            }
            $res = db('spec_goods')->insertAll($spec_goods);
            if (!$res) {
                throw new Exception('商品规格属性关联失败');
            }
            Db::commit();
            $data = $this->m->with('category,type,brand')->getById($goodid);
            $this->successInfo($data, '添加成功');
        } catch (Exception $e) {
            Db::rollback();
            $this->errorInfo($e->getMessage() . $e->getLine() . $e->getFile());
        }
    }


    public function read($id)
    {
        try {
            $data = $this->m->with('categoryAll,goodsImage,specGoods,brand')->find($id);
            if (empty($data)) {
                throw new Exception('数据异常');
            }
            $data['category'] = $data['category_all'];
            $data['category']['pid_path'] = explode('_', $data['category']['pid_path']);
            unset($data['category_all']);
            $type =  Type::with('specs,attrs,specs.specValues')->find($data['type_id']);
            //specs  attrs  spec_values  
            if (empty($type)) {
                throw new Exception('数据异常');
            }
            $data['type'] = $type;
            $this->successInfo($data);
        } catch (Exception $e) {
            $this->errorInfo($e->getMessage() . $e->getLine() . $e->getFile());
        }
    }

    public function edit($id)
    {
        try {
            $data = $this->m->with('category,type,specGoods,goodsImage')->find($id);
            if (empty($data)) {
                throw new Exception('数据异常');
            }
            $typedata = Type::with('attrs,specs,specs.specValues')->find($data['type_id']);
            if (empty($typedata)) {
                throw new Exception('数据异常');
            }
            $data['type'] = $typedata;
            $catepath = explode('_', $data['pid_path']);
            if (empty($catepath)) {
                throw new Exception('无法获取分类信息');
            }
            $category = [];
            $res = Category::with('brand')->where('pid', $catepath[2])->select();
            if (empty($typedata)) {
                throw new Exception('获取三级分类异常');
            } else {
                $category['cate_three'] = $res;
            }
            $res = Category::with('brand')->where('pid', $catepath[1])->select();
            if (empty($typedata)) {
                throw new Exception('获取二级分类异常');
            } else {
                $category['cate_two'] = $res;
            }
            $res = db('category')->field(['create_time', 'update_time', 'delete_time'], true)->select();
            if (empty($typedata)) {
                throw new Exception('获取一级分类异常');
            } else {
                $category['cate_two'] = $res;
            }
            $data['category'] = $category;
        } catch (Exception $e) {
            $this->errorInfo($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
        }
        $this->successInfo($data);
    }

    public function update($id)
    {
        $data = input('put.');
        $res = $this->validate($data, 'goods.edit');
        if ($res !== true) {
            $this->errorInfo($res);
        } elseif (!file_exists('.' . $data['goods_logo'])) {
            $this->errorInfo('商品logo地址不正确');
        } elseif (empty($data['attr']) || empty($data['item'])) {
            $this->errorInfo('商品规格或属性有误');
        } elseif (empty($data['goods_images'])) {
            $this->errorInfo('商品图片错误');
        }
        $data['goods_attr'] = json_encode($data['goods_attr']);
        //处理商品logo
        $logo = $data['goods_logo'];
        $savepath = str_replace('uploads', 'thumb', $logo);
        $res = $this->thumb('.' . $logo, '.' . $savepath, ['width' => 200, 'height' => 240]);
        if (!$res) {
            $this->errorInfo('商品logo生成失败', 500);
        }
        if (isset($data['goods_desc'])) {
            $data['goods_desc'] = remove_xss($data['goods_desc']);
        }
        try {
            Db::startTrans();
            //修改数据
            $res = $this->m->edit($data, $id);
            if (!$res) {
                throw new Exception('添加失败');
            }
            //添加商品图片
            if (isset($data['goods_images'])) {
                $imgdata = [];
                foreach ($data['goods_images'] as $v) {
                    $url = '.' . str_replace('uploads', 'thumb', $v);
                    $imgname = basename($url);
                    $imgpath = '.' . dirname($url);
                    $bigpath = $imgpath . "/800_$imgname";
                    $smallpath = $imgpath . "/400_$imgname";
                    $res = $this->thumb($url, $bigpath, ['width' => 800, 'height' => 800]);
                    if (!$res) {
                        throw new Exception('商品图片big生成错误');
                    }
                    $res = $this->thumb($url, $smallpath, ['width' => 800, 'height' => 800]);
                    if (!$res) {
                        throw new Exception('商品图片small生成错误');
                    }
                    $imgdata[] = ['goods_id' => $id, 'pics_big' => $bigpath, 'pics_sma' => $smallpath];
                }
                $res = db('goods_images')->insertAll($imgdata);
                if (!$res) {
                    throw new Exception('商品图片添加失败');
                }
            }
            //删除spu重新添加
            $res = db('spec_goods')->where('id', $id)->delete();
            if ($res) {
                $spec_goods = [];
                foreach ($data['item'] as $v) {
                    if (!is_array($v)) {
                        continue;
                    }
                    $v['goods_id'] = $id;
                    $spec_goods[] = $v;
                }
                if (empty($spec_goods)) {
                    throw new Exception('请填写商品sku');
                }
            } else {
                throw new Exception('商品spu删除修改失败');
            }
            $res = db('spec_goods')->insertAll($spec_goods);
            if (!$res) {
                throw new Exception('商品规格属性关联失败');
            }
        } catch (Exception $e) {
            Db::rollback();
            $this->errorInfo($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
        }
        Db::commit();
        $info = $this->m->with('category,brand,type')->find($id);
        if (!$info) $info = [];
        $info['goods_logo'] = $savepath;
        $this->successInfo($info);
    }

    public function delete($id)
    {
        //先判断是否上架
        $res = db('goods')->where('id', $id)->column('is_on_sale');
        if (!$res) {
            $this->errorInfo('删除失败', 500);
        }
        if ($res[0] != 0) {
            $this->errorInfo('删除失败，请先下架商品', 400);
        }
        //删除后，删除商品相册,删除商品sku,删除相册文件
        try {
            Db::startTrans();
            $res = db('goods')->where('id', $id)->delete();
            if (!$res) {
                throw new Exception('商品删除失败');
            }
            $res = db('spec_goods')->where('goods_id', $id)->delete();
            if (!$res) {
                throw new Exception('商品sku删除失败');
            }
            $images = [];
            $res = db('goods_images')->field('pics_big', 'pics_sma')->where('goods_id', $id)->select();
            if (!$res) {
                throw new Exception('无法获取到商品图片');
            }
            foreach ($res as $v) {
                $images[] = $v;
            }
            $res = db('goods_images')->where('goods_id', $id)->delete();
            if (!$res) {
                throw new Exception('商品图片删除失败');
            }
            foreach ($images as $v) {
                if (file_exists('.' . $v)) {
                    unlink('.' . $v);
                    $path = dirname($v);
                    $name = basename($v);
                    $uploadname = substr($name, 0, 4);
                    if (!$uploadname) {
                        continue;
                    }
                    $uploadpath = str_replace('thumb', 'uploads', $path);
                    $src = '.' . $uploadpath . '/' . $uploadname;
                    if (file_exists($src)) {
                        unlink($src);
                    }
                }
            }
        } catch (Exception $e) {
            Db::rollback();
        }
        Db::commit();
    }

    public function delpics($id)
    {
        $m = new GoodsImages();
        $res = $m->del($id);
        if (empty($res)) {
            $this->errorInfo('删除失败');
        }
        foreach ($res as $v) {
            if (file_exists('.' . $v)) {
                unlink('.' . $v);
                $path = dirname($v);
                $name = basename($v);
                $uploadname = substr($name, 0, 4);
                if (!$uploadname) {
                    continue;
                }
                $uploadpath = str_replace('thumb', 'uploads', $path);
                $src = '.' . $uploadpath . '/' . $uploadname;
                if (file_exists($src)) {
                    unlink($src);
                }
            }
        }
        $this->successInfo('删除成功');
    }
}
