<?php

namespace App\Http\Controllers\Nav;

use App\CateModel;
use App\Http\Controllers\Controller;
use App\ProductModel;
use http\Cookie;
use Illuminate\Http\Request;
use App\NavModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;

class NavController extends Controller
{
    /**
     * 查询导航
     * @return false|string
     */
    public function getNav(Request $request)
    {
        $NavModel = new NavModel();
        $perPage = 5;
        $columns = ['*'];
        $pageName = 'page';
        $currentPage = $request->page === null ? 1 : $request->page;
        $res = $NavModel->where('status', '!=', '0')->paginate($perPage, $columns, $pageName, $currentPage);

        $info = [
            'status' => true,
            'data' => count($res) == 0 ? '暂无数据' : $res,
        ];

        return $info;
    }

    /**
     * 假删除
     * @param Request $request
     */
    public function del(Request $request)
    {
        $res = (new NavModel())->where('id', '=', $request->id)->update(['status' => 0]);

        if ($res > 0) {
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
     * 编辑
     * @param Request $request
     * @return array|mixed
     */
    public function edit(Request $request)
    {
        if (!$request->isMethod('get')) {
            if (empty($request->title)) {
                return [
                    'status' => false,
                    'msg' => '名称不能为空'
                ];
            }

            if (empty($request->link_type)) {
                return [
                    'status' => false,
                    'msg' => '链接类型不能为空'
                ];
            }

            if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $request->title)) {
                return [
                    'status' => false,
                    'msg' => '不允许含有特殊字符',
                ];
            }

            if (empty($request->link_target)) {
                return [
                    'status' => false,
                    'msg' => '链接目标不能为空'
                ];
            }

            if (!is_numeric($request->position_id)) {
                return [
                    'status' => false,
                    'msg' => '位置id类型无效'
                ];
            }

            if ($request->position_id > 4 || $request->position_id < 1) {
                return [
                    'status' => false,
                    'msg' => '位置id无效'
                ];
            }

            $model = (new NavModel())->find($request->id);
            if (empty($model)) {
                return [
                    'status' => false,
                    'msg' => '标签id无效'
                ];
            }

            $model->title = $request->title;
            $model->position_id = $request->position_id;
            $model->picture = $request->picture;
            $model->link_type = $request->link_type;
            $model->link_target = $request->link_target;
            $res = $model->save();

            if ($res) {
                $info = [
                    'status' => true,
                ];
            } else {
                $info = [
                    'status' => false,
                    'msg' => '编辑失败'
                ];
            }


            return $info;
        } else {
            $res = (new NavModel())->where('id', '=', $request->id)->where('status', '!=', '0')->get();

            return $res;
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     */
    public function create(Request $request)
    {
        if (empty($request->title)) {
            return [
                'status' => false,
                'msg' => '名称不能为空'
            ];
        }

        if (empty($request->link_type)) {
            return [
                'status' => false,
                'msg' => '链接类型不能为空'
            ];
        }

        if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $request->title)) {
            return [
                'status' => false,
                'msg' => '不允许含有特殊字符',
            ];
        }

        if (empty($request->link_target)) {
            return [
                'status' => false,
                'msg' => '链接目标不能为空'
            ];
        }

        if (!is_numeric($request->position_id)) {
            return [
                'status' => false,
                'msg' => '位置id类型无效'
            ];
        }

        if ($request->position_id > 4 || $request->position_id < 1) {
            return [
                'status' => false,
                'msg' => '位置id无效'
            ];
        }

        $model = new NavModel();
        $model->title = $request->title;
        $model->position_id = $request->position_id;
        $model->picture = $request->picture;
        $model->link_type = $request->link_type;
        $model->link_target = $request->link_target;

        $res = $model->save();

        if ($res) {
            $info = [
                "status" => true
            ];
        } else {
            $info = [
                "status" => false,
                "msg" => '添加失败'
            ];
        }


        return $info;
    }
}
