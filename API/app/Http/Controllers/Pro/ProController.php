<?php

namespace App\Http\Controllers\Pro;

use App\Http\Controllers\Controller;
use App\ProductModel;
use App\skuModel;
use App\tagModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;

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
        $res = $this->product->where('status','!=','0')->with('tag', 'sku')->paginate($perPage, $columns, $pageName, $currentPage);

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
        $res = $this->product->where('id', '=', $request->id)->update(['status' => $request->type]);

        if ($res) {
            $info = [
                'status' => true,
            ];
        } else {
            $info = [
                'status' => false,
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
                $res = $this->product->where('id', '=', $arr[$k]['id'])->update($arr[$k]);
                break;
            case "tag":
                $res = $this->tag->where('id', '=', $arr[$k]['id'])->update($arr[$k]);
                break;
            case 'sku':
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
                foreach ($arr as $key => $val) {
                    $k = $key;
                }

                switch ($k) {
                    case 'tag':
                        $this->tag->product_id = $request->pid;
                        $this->tag->tag_id = $arr[$k]['tag_id'];
                        $this->tag->value = $arr[$k]['value'];
                        $res = $this->tag->save();
                        break;
                    case 'sku':
                        $this->sku->product_id = $request->pid;
                        $this->sku->original_price = $arr[$k]['original_price'];
                        $this->sku->price = $arr[$k]['price'];
                        $this->sku->attr1 = $arr[$k]['attr1'];
                        $this->sku->attr2 = $arr[$k]['attr2'];
                        $this->sku->attr3 = $arr[$k]['attr3'];
                        $this->sku->quantity = $arr[$k]['quantity'];
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
                    $info = [
                        'status' => false,
                        'msg' => '添加失败',
                    ];
                }
            } else {
                $tagArr = $arr['tag'];
                $skuArr = $arr['sku'];
                unset($arr['tag']);
                unset($arr['sku']);

                $this->product->category_id = $arr['category_id'];
                $this->product->name = $arr['name'];
                $this->product->content = $arr['content'];
                $this->product->sort = $arr['sort'];
                $pro_res = $this->product->save();
                $pro_id = $this->product->id;


                foreach ($tagArr as $key => $val) {
                    $this->tag->product_id = $pro_id;
                    $this->tag->tag_id = $val['tag_id'];
                    $this->tag->value = $val['value'];
                    $tag_res = $this->tag->save();
                    $this->tag = new tagModel();
                }

                foreach ($skuArr as $key => $val) {
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
                    $info = [
                        'status' => false,
                        'msg' => '添加失败'
                    ];
                }
            }

            DB::commit();

            return json_encode($info);

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'msg' => '添加失败',
            ];
        }
    }
}
