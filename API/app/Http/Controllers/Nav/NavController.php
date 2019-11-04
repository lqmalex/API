<?php

namespace App\Http\Controllers\Nav;

use App\CateModel;
use App\Http\Controllers\Controller;
use App\ProductModel;
use http\Cookie;
use Illuminate\Http\Request;
use App\NavModel;
use Illuminate\Support\Facades\Redis;
use mysql_xdevapi\Exception;

class NavController extends Controller{
    /**
     * 查询导航
     * @return false|string
     */
    public function getNav(Request $request) {
        $NavModel = new NavModel();
        $perPage = 5;
        $columns = ['*'];
        $pageName = 'page';
        $currentPage = $request->page === null ? 1 : $request->page;
        $res = $NavModel->where('status','!=','0')->paginate($perPage,$columns,$pageName,$currentPage);

        if (count($res) != 0) {
            $info = [
              'status' => true,
              'data' => $res,
            ];
        } else {
            $info = [
                'status' => false,
                'data' => null
            ];
        }
        return $info;
    }

    /**
     * 假删除
     * @param Request $request
     */
    public function del(Request $request){
        $res = (new NavModel())->where('id','=',$request->id)->update(['status'=>0]);

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
    public function edit(Request $request) {
        if (!$request->isMethod('get')) {
            if ($request->link_target != '') {
                $model = (new NavModel())->find($request->id);
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
                    ];
                }
            } else {
                $info = [
                    'status' => false,
                ];
            }

            return $info;
        } else {
            $res = (new NavModel())->where('id','=',$request->id)->where('status','!=','0')->get();

            return $res;
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     */
    public function create(Request $request) {
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
                "status" => false
            ];
        }

        return $info;
    }

    /**
     * 上传文件
     * @param Request $request
     * @return array
     */
    public function upload(Request $request) {
        $file = $request->file('image');    //获取文件所有信息

        if($file ->isValid()) { //判断文件是否存在
            $clientName = $file->getClientOriginalName();    //客户端文件名称..
            $entension = $file->getClientOriginalExtension();   //上传文件的后缀.
            $newName = md5(date('Ymdhis') . $clientName) . "." . $entension;    //定义      上传文件的新名称
            $path = $file->move('upload/Image', $newName);    //把缓存文件移动到指定文件夹
        }

        if (file_exists('upload/Image/'.$newName)) {
            $info = [
                "status"=>true,
                "fileName"=>'upload/Image/'.$newName
            ];
        } else {
            $info = [
                "status"=>false,
            ];
        }

        return $info;
    }

}
