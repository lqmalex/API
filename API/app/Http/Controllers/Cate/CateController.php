<?php

namespace App\Http\Controllers\Cate;

use App\CateModel;
use App\Http\Controllers\Controller;
use DemeterChain\C;
use Illuminate\Http\Request;

class CateController extends Controller{

    /**
     * 分类数据
     * @param Request $request
     * @return mixed
     */
    public function getCate(Request $request) {
        $model = new CateModel();

        if (!empty($request->name)) {
            $model = $model->where('name','like','%'.$request->name.'%');
        }

        $perPage = $request->total === null ? 5 : 0;
        $columns = ['*'];
        $pageName = 'page';
        $currentPage = $request->page === null ? 1 : $request->page;
        $list = $model->where('status','!=','0')->paginate($perPage,$columns,$pageName,$currentPage);

        return json_encode([
            'status'=>true,
            'data'=>$list
        ]);
    }

    /**
     * 软删除
     * @param Request $request
     * @return array
     */
    public function delete(Request $request) {
        $res = (new CateModel())->where('id','=',$request->id)->update(['status'=>'0']);

        if ($res) {
            $info = [
                'status'=>true,
                'msg'=>'删除成功',
            ];
        } else {
            $info = [
                'status'=>false,
                'msg'=>'删除失败',
            ];
        }

        return $info;
    }

    /**
     * 编辑
     * @param Request $request
     * @return array
     */
    public function edit(Request $request) {
        if (!$request->isMethod('get')) {
            $model = (new CateModel())->find($request->id);
            $model->name = $request->name;
            $model->property = $request->property;
            $model->sort = $request->sort === null ? 10 : $request->sort;

            $res = $model->save();

            if ($res) {
                $info = [
                    'status'=>true,
                    'msg'=>'编辑成功'
                ];
            } else {
                $info = [
                    'status'=>false,
                    'msg'=>'编辑失败'
                ];
            }

            return $info;
        } else {
            $data = (new CateModel())->where('id','=',$request->id)->where('status','!=','0')->get();

            if (count($data) > 0) {
                $info = [
                    'status'=>true,
                    'data'=>$data
                ];
            } else {
                $info = [
                    'status'=>false,
                ];
            }

            return $info;
        }
    }

    /**
     * 添加
     * @param Request $request
     * @return array
     */
    public function create(Request $request) {
        $model = new CateModel();
        $model->name = $request->name;
        $model->property = $request->property;
        $model->sort = $request->sort === null ? 10 : $request->sort ;

        $res = $model->save();

        if ($res) {
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

        return $info;
    }
}
