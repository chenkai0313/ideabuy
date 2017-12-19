<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/26
 * Time: 11:33
 */
namespace Modules\Pc\HTTP\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommonController extends Controller
{
    /**
     * 发送短信
     * string mobile 手机 必传
     * int type 类型 1.注册 2.找回密码 必传
     * @param Request $request
     * @return mixed
     */
    public function addSMS(Request $request)
    {
        $params = $request->input();
        $result = \SMSService::addSMS($params);
        return $result;
    }
    /**
     * 图片的上传
     * @param Request $request
     * @return array
     */
    public function uploadImage(Request $request)
    {
        $params=$request->input();
        \Log::useFiles(storage_path() . '/logs/pc-' . date('Y-m-d') . '-info.log', 'info');
        \Log::info('客户端请求log：', $_FILES['pic']);
        return \Oss::uploadImage($_FILES['pic']);

    }
}