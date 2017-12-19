<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 16:02
 */
namespace Modules\Pc\Http\Requests\User;

use Modules\Pc\Http\Requests\BaseRequest;

class AddUserCardRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'card_mobile'=>['regex:/^1[34578]+\d{9}$/','required'],
            'card_number'=>['required','regex:/^(\d{16}|\d{19})$/'],
            'code'=>'required|min:4',
        ];
    }
    public function messages()
    {
        return [
            'card_mobile.required'=>'预留手机号必填',
            'card_mobile.regex'=>'手机号格式错误',
            'card_number.required'=>'银行卡号必填',
            'card_number.regex'=>'银行卡号格式错误',
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