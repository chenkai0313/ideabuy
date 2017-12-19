<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 16:02
 */
namespace Modules\Pc\Http\Requests\User;

use Modules\Pc\Http\Requests\BaseRequest;

class SetPayPwdRequest extends BaseRequest
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
            'pay_password_confirm'=>['same:pay_password','required'],
        ];
    }
    public function messages()
    {
        return [
            'pay_password.required'=>'交易密码必填',
            'pay_password.regex'=>'交易密码必须为6位数数字',
            'pay_password_confirm.required'=>'确认密码必填',
            'pay_password_confirm.same'=>'二次输入密码不一致',

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