<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 16:02
 */
namespace Modules\Pc\Http\Requests\User;

use Modules\Pc\Http\Requests\BaseRequest;

class EditPayPwdRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'pay_password'=>['regex:/^[0-9]{6}$/','required'],
            'confirm_pay_pwd'=>['same:pay_password','required'],
            'code'=>'required|min:4',
        ];
    }
    public function messages()
    {
        return [
            'pay_password.required'=>'交易密码必填',
            'pay_password.regex'=>'交易密码必须为6位数数字',
            'confirm_pay_pwd.required'=>'确认密码必填',
            'confirm_pay_pwd.same'=>'二次输入密码不一致',
            'code.required'=>'验证码必填',
            'code.min'=>'验证码必须不小于4位',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}