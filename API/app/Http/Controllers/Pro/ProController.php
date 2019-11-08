<?php

namespace App\Http\Controllers\Pro;

use App\CateModel;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PreController;
use App\NavModel;
use App\ProductModel;
use App\skuModel;
use App\tagModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use \Exception;

class ProController extends Controller
{
    private $product;
    private $tag;
    private $sku;

    public function __construct()
    {
        $this->product = new ProductModel();
        $this->tag = new tagModel();
        $this->sku = new skuModel();
    }

    /**
     * 列表
     * @param Request $request
     * @return array
     */
    public function getProduct(Request $request)
    {
        if (!empty($request->name)) {
            $this->product = $this->product->where('name', 'like', '%' . $request->name . '%');
        }

        $perPage = $request->total === null ? 5 : 0;
        $columns = ['*'];
        $pageName = 'page';
        $currentPage = $request->page === null ? 1 : $request->page;
        $res = $this->product->where('status', '!=', '0')->with(['tag' => function ($query) {
            $query->where('status', '!=', '0');
        }, 'sku' => function ($query) {
            $query->where('status', '!=', '0');
        }])->paginate($perPage, $columns, $pageName, $currentPage);

        return [
            'status' => true,
            'data' => $res,
        ];
    }

    /**
     * 上/下架
     * @param Request $request
     */
    public function changeShelf(Request $request)
    {
        $res = $this->sku->where('id', '=', $request->id)->update(['status' => $request->type]);

        if ($res) {
            $info = [
                'status' => true,
            ];
        } else {
            $info = [
                'status' => false,
                'msg' => '修改失败',
            ];
        }

        return $info;
    }

    /**
     * 软删除
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        $res = (new ProductModel())->where('id', '=', $request->id)->update(['status' => 0]);

        if ($res) {
            $info = [
                'status' => true,
                'msg' => '删除成功',
            ];
        } else {
            $info = [
                'status' => false,
                'msg' => '删除失败',
            ];
        }

        return $info;
    }


    public function tagDelete(Request $request)
    {
        $res = $this->tag->where('id', '=', $request->id)->update(['status' => 0]);

        if ($res) {
            $info = [
                'status' => true,
                'msg' => '删除成功',
            ];
        } else {
            $info = [
                'status' => false,
                'msg' => '删除失败'
            ];
        }

        return $info;
    }

    /**
     * 编辑
     * @param Request $request
     * @return array
     */
    public function edit(Request $request)
    {
        $arr = json_decode($request->getContent(), true);
        foreach ($arr as $key => $val) {
            $k = $key;
        }

        switch ($k) {
            case "pro":
                if (empty($arr[$k]['content'])) {
                    if (empty($arr[$k]['name'])) {
                        return [
                            'status' => false,
                            'msg' => '名称不能为空'
                        ];
                    }

                    if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $arr[$k]['name'])) {
                        return [
                            'status' => false,
                            'msg' => '不允许含有特殊字符'
                        ];
                    }

                    if (empty($arr[$k]['sort'])) {
                        return [
                            'status' => false,
                            'msg' => '排序不允许为空',
                        ];
                    }

                    $cate_model = (new CateModel())->find($arr[$k]['category_id']);
                    if (empty($cate_model)) {
                        return [
                            'status' => false,
                            'msg' => '分类不存在',
                        ];
                    }

                    $res = $this->product->where('id', '=', $arr[$k]['id'])->update($arr[$k]);
                } else {
                    $res = $this->product->where('id', '=', $arr[$k]['content']['id'])->update($arr[$k]);
                }

                break;
            case "tag":
                if (!is_numeric($arr[$k]['tag_id'])) {
                    return [
                        'status' => false,
                        'msg' => '标签类型id类型错误',
                    ];
                }

                if ($arr[$k]['tag_id'] > 3 || $arr[$k]['tag_id'] < 1) {
                    return [
                        'status' => false,
                        'msg' => '标签类型id无效',
                    ];
                }

                $res = $this->tag->where('id', '=', $arr[$k]['id'])->update($arr[$k]);
                break;
            case 'sku':
                if (empty($arr[$k]['attr1']) && empty($arr[$k]['attr2']) && empty($arr[$k]['attr3'])) {
                    return [
                        'status' => false,
                        'msg' => '至少填写一个属性'
                    ];
                }

                if (empty($arr[$k]['attr1'])) {
                    unset($arr[$k]['attr1']);
                }

                if (empty($arr[$k]['attr2'])) {
                    unset($arr[$k]['attr2']);
                }

                if (empty($arr[$k]['attr3'])) {
                    unset($arr[$k]['attr3']);
                }

                if ($arr[$k]['price'] === null) {
                    return [
                        'status' => false,
                        'msg' => '售价不能为空'
                    ];
                }

                if ($arr[$k]['original_price'] === null) {
                    return [
                        'status' => false,
                        'msg' => '原价不能为空'
                    ];
                }

                if (!is_numeric($arr[$k]['original_price'])) {
                    return [
                        'status' => false,
                        'msg' => '原价必须是数字'
                    ];
                }

                if (!is_numeric($arr[$k]['price'])) {
                    return [
                        'status' => false,
                        'msg' => '售价必须是数字'
                    ];
                }

                if ($arr[$k]['original_price'] < 0) {
                    return [
                        'status' => false,
                        'msg' => '原价不能少于0'
                    ];
                }

                if ($arr[$k]['price'] < 0) {
                    return [
                        'status' => false,
                        'msg' => '售价不能少于0'
                    ];
                }

                if (!empty($arr[$k]['quantity']) && !is_numeric($arr[$k]['quantity'])) {
                    return [
                        'status' => false,
                        'msg' => '库存必须是数字'
                    ];
                }

                $res = $this->sku->where('id', '=', $arr[$k]['id'])->update($arr[$k]);
                break;
            default:
                $info = [
                    'status' => false,
                    'msg' => '编辑失败'
                ];
                break;
        }

        if ($res) {
            $info = [
                'status' => true,
                'msg' => '编辑成功'
            ];
        } else {
            $info = [
                'status' => false,
                'msg' => '编辑失败'
            ];
        }

        return json_encode($info);
    }

    /**
     * 添加
     * @param Request $request
     * @return false|string
     */
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $arr = json_decode($request->getContent(), true);

            if (!empty($arr['pid'])) {
                $pro_model = (new ProductModel())->find($arr['pid']);
                if (empty($pro_model)) {
                    throw new Exception('商品id错误');
                }

                foreach ($arr as $key => $val) {
                    $k = $key;
                }

                switch ($k) {
                    case 'tag':
                        if (empty($arr[$k]['tag_id'])) {
                            throw new Exception('标签id不能为空');
                        }

                        if (!is_numeric($arr[$k]['tag_id'])) {
                            throw new Exception('标签id类型错误');
                        }

                        if ($arr[$k]['tag_id'] > 3 || $arr[$k]['tag_id'] < 1) {
                            throw new Exception('标签id无效');
                        }

                        $this->tag->product_id = $request->pid;
                        $this->tag->tag_id = $arr[$k]['tag_id'];
                        $this->tag->value = $arr[$k]['value'];
                        $res = $this->tag->save();
                        break;
                    case 'sku':
                        if (empty($arr[$k]['attr1']) && empty($arr[$k]['attr2']) && empty($arr[$k]['attr3'])) {
                            throw new Exception('至少填写一个属性');
                        }

                        if ($arr[$k]['price'] === null) {
                            throw new Exception('售价不能为空');
                        }

                        if ($arr[$k]['original_price'] === null) {
                            throw new Exception('原价不能为空');
                        }

                        if (!is_numeric($arr[$k]['original_price'])) {
                            throw new Exception('原价必须是数字');
                        }

                        if (!is_numeric($arr[$k]['price'])) {
                            throw new Exception('售价必须是数字');
                        }


                        if (!empty($arr[$k]['quantity']) && !is_numeric($arr[$k]['quantity'])) {
                            throw new Exception('库存必须是数字');
                        }

                        if (empty($arr[$k]['sort'])) {
                            throw new Exception('排序不允许为空');
                        }

                        $this->sku->product_id = $request->pid;
                        $this->sku->original_price = $arr[$k]['original_price'];
                        $this->sku->price = $arr[$k]['price'];
                        $this->sku->attr1 = $arr[$k]['attr1'];
                        $this->sku->attr2 = $arr[$k]['attr2'];
                        $this->sku->attr3 = $arr[$k]['attr3'];
                        $this->sku->quantity = $arr[$k]['quantity'];
                        $this->sku->sort = $arr[$k]['sort'];
                        $res = $this->sku->save();
                        break;
                    default:
                        $res = false;
                        break;
                }

                if ($res) {
                    $info = [
                        'status' => true,
                        'msg' => '添加成功',
                    ];
                } else {
                    throw new Exception('添加失败');
                }
            } else {
                $tagArr = $arr['tag'];
                $skuArr = $arr['sku'];
                unset($arr['tag']);
                unset($arr['sku']);

                $model = (new CateModel())->find($arr['category_id']);
                if (empty($model)) {
                    throw new Exception('分类id无效');
                }

                if (empty($arr['name'])) {
                    throw new Exception('名称不能为空');
                }

                if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $arr['name'])) {
                    throw new Exception('不允许含有特殊字符');
                }

                if (empty($arr['sort'])) {
                    throw new Exception('排序不允许为空');
                }

                $this->product->category_id = $arr['category_id'];
                $this->product->name = $arr['name'];
                $this->product->content = $arr['content'];
                $this->product->sort = $arr['sort'];
                $pro_res = $this->product->save();
                $pro_id = $this->product->id;

                if ($pro_id > 0) {
                    foreach ($tagArr as $key => $val) {
                        if (empty($val['tag_id'])) {
                            throw new Exception('标签id不能为空');
                        }

                        if (!is_numeric($val['tag_id'])) {
                            throw new Exception('标签id类型错误');
                        }

                        if ($val['tag_id'] > 3 || $val['tag_id'] < 1) {
                            throw new Exception('标签id无效');
                        }
                        $this->tag->product_id = $pro_id;
                        $this->tag->tag_id = $val['tag_id'];
                        $this->tag->value = $val['value'];
                        $tag_res = $this->tag->save();
                        $this->tag = new tagModel();
                    }

                    foreach ($skuArr as $key => $val) {
                        if (empty($val['attr1']) && empty($val['attr2']) && empty($val['attr3'])) {
                            throw new Exception('至少填写一个属性');
                        }

                        if ($val['price'] === null) {
                            throw new Exception('售价不能为空');
                        }

                        if ($val['original_price'] === null) {
                            throw new Exception('原价不能为空');
                        }

                        if (!is_numeric($val['original_price'])) {
                            throw new Exception('原价必须是数字');
                        }

                        if (!is_numeric($val['price'])) {
                            throw new Exception('售价必须是数字');
                        }


                        if (!empty($val['quantity']) && !is_numeric($val['quantity'])) {
                            throw new Exception('库存必须为数字');
                        }

                        if (empty($val['sort'])) {
                            throw new Exception('排序不允许为空');
                        }
                        $this->sku->product_id = $pro_id;
                        $this->sku->original_price = $val['original_price'];
                        $this->sku->price = $val['price'];
                        $this->sku->attr1 = $val['attr1'];
                        $this->sku->attr2 = $val['attr2'];
                        $this->sku->attr3 = $val['attr3'];
                        $this->sku->quantity = $val['quantity'];
                        $this->sku->sort = $val['sort'];
                        $sku_res = $this->sku->save();
                        $this->sku = new skuModel();
                    }

                    if ($pro_res && $tag_res && $sku_res) {
                        $info = [
                            'status' => true,
                            'msg' => '添加成功'
                        ];
                    } else {
                        throw new Exception('添加失败');
                    }
                } else {
                    throw new Exception('添加失败');
                }
            }

            DB::commit();

            return json_encode($info);

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'msg' => $e->getMessage(),
            ];
        }
    }
}

