<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 16:02
 */
namespace Modules\Pc\Http\Requests\User;

use Modules\Pc\Http\Requests\BaseRequest;

class AddIdCardRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //'user_id'=>'required',
            'real_name'=>'required',
            'user_idcard'=>'required',
        ];
    }
    public function messages()
    {
        return [
            'real_name.required'=>'身份证姓名必填',
            'user_idcard.required'=>'身份证号必填',
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