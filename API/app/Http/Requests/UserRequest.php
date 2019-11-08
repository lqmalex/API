<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if ($this->path() != 'api/Register') {
            return [
                'email'=>'required|email',
                'password'=>'required|min:6'
            ];
        } else {
            return [
                'name'=>'required|unique:pre_user',
                'email'=>'required|email|unique:pre_user',
                'password'=>'required|min:6'
            ];
        }
    }

    public function messages(){
        return [
            'name.required' => '名称不能为空',
            'name.unique' => '名称已存在',
            'email.required' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'email.unique' => '邮箱已存在',
            'password.required' => '密码不能为空',
            'password.min' => '密码长度不正确'
        ];
    }
}
