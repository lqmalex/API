<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PreController extends Controller
{
    /**
     * 上传文件
     * @param Request $request
     * @return array
     */
    public function upload(Request $request)
    {
        $file = $request->file('image');    //获取文件所有信息

        if ($file->isValid()) { //判断文件是否存在
            $clientName = $file->getClientOriginalName();    //客户端文件名称..
            $entension = $file->getClientOriginalExtension();   //上传文件的后缀.
            $newName = md5(date('Ymdhis') . $clientName) . "." . $entension;    //定义      上传文件的新名称
            $key = Str::random(32); //生成随机key
            Cache::store('file')->put($key,$newName); //缓存存储
            $path = $file->move('upload/porary', $newName);    //把缓存文件移动到指定文件夹
        }

        if (file_exists('upload/porary/' . $newName)) {
            $info = [
                "status" => true,
                "key"=>$key,
                "fileName" => 'upload/porary/' . $newName
            ];
        } else {
            $info = [
                "status" => false,
            ];
        }

        return $info;
    }

    /**
     * 移动文件
     * @param Request $request
     * @return string
     */
    public function moveImage(Request $request) {
        $file = Cache::get($request->key); //获取缓存里的文件名
        $storage = Storage::disk('local');
        $storage->move('/porary/'.$file,'/Image/'.$file);

        return 'upload/Image/'.$file;
    }
}
