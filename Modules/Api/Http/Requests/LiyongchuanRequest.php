<?php

namespace Modules\Api\Http\Requests;

class LiyongchuanRequest extends BaseRequest
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
        ];
    }
    public function messages()
    {
        return [
            'user_id.required'=>'用户id必填'
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
