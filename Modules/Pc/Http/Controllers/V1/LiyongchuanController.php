<?php
/**
 * Created by PhpStorm.
 * User: liyongchuan
 * Date: 2017/9/27
 * Time: 13:09
 */
namespace Modules\Pc\HTTP\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pc\Http\Requests\User\SetPayPwdRequest;

class LiyongchuanController extends Controller
{
    /**
     *
     */
    public function liTest(Request $request)
    {
        //\Log::error("liumiao params is :".json_encode($_FILES['pic']));
       /* $con=['code'=>0,
            'msg'=>'上传成功',
            'data'=>['img_path'=>'article/2017/08/02/07bf13d0d7ef434f4fb92e433ab35565.jpg',
                'img_href'=>'https://ideabuy.oss-cn-hangzhou.aliyuncs.com/article/2017/08/02/07bf13d0d7ef434f4fb92e433ab35565.jpg']];
        $content=json_encode($con);
        return response($content)->header('Content-Type', 'text/html;charset=utf-8');*/
        $params = $request->input();
        $params['user_id'] = 168;//get_user_id();
        $params['user_mobile'] = 15867558095;//get_user_id();
        $result = \UserService::userInfoDetail($params);
        return $result;
    }
}