<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\TokenModel;
use App\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private $user_model;
    private $token_model;

    public function __construct()
    {
        $this->user_model = new UserModel();
        $this->token_model = new TokenModel();
    }

    /**
     * 登录
     * @param UserRequest $request
     * @return array
     */
    public function Login(UserRequest $request)
    {
        $res = $this->user_model->where('email', '=', $request->email)->first();

        if ($res != null) {
            if ($request->password == decrypt($res->password)) {
                $token_res = $this->token_model->where('user_id', '=', $res->id)->first();

                if ($token_res != null) {
                    $this->token_model->where('user_id', '=', $res->id)->delete();
                }

                $token = Str::random(32);

                $this->token_model->token = $token;
                $this->token_model->user_id = $res->id;
                $this->token_model->validity = date('Y-m-d H:i:s', strtotime('+1 week'));
                $toekn_info = $this->token_model->save();

                if ($toekn_info) {
                    $info = [
                        'status' => true,
                        'name'=>$res->name,
                        'token' => $token
                    ];
                } else {
                    $info = [
                        'status' => false,
                        'msg' => '登录失败',
                    ];
                }
            } else {
                $info = [
                    'status' => false,
                    'msg' => '密码不正确',
                ];
            }
        } else {
            $info = [
                'status' => false,
                'msg' => '账号不存在'
            ];
        }

        return $info;
    }

    /**
     * 注册
     * @param UserRequest $request
     * @return array
     */
    public function Register(UserRequest $request)
    {
        $this->user_model->name = $request->name;
        $this->user_model->email = $request->email;
        $this->user_model->password = encrypt($request->password);
        $user_res = $this->user_model->save();
        $id = $this->user_model->id;

        if (!empty($id)) {
            $token = Str::random(32);

            $this->token_model->token = $token;
            $this->token_model->user_id = $id;
            $this->token_model->validity = date('Y-m-d H:i:s', strtotime('+1 week'));
            $toekn_res = $this->token_model->save();

            if ($user_res && $toekn_res) {
                $info = [
                    'status' => true,
                    'msg' => '注册成功',
                    'token' => $token
                ];
            } else {
                $info = [
                    'status' => false,
                    'msg' => '注册失败',
                ];
            }
        } else {
            $info = [
                'status' => false,
                'msg' => '注册失败',
            ];
        }

        return $info;
    }


    /**
     * 退出
     * @param Request $request
     * @return array
     */
    public function out(Request $request)
    {
        $res = $this->token_model->where('token', '=', $request->token)->delete();

        if ($res) {
            $info = [
                'status' => true
            ];
        } else {
            $info = [
                'status' => false
            ];
        }

        return $info;
    }
}
