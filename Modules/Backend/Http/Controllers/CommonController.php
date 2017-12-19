<?php
/**
 * 公共模块
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

Class CommonController extends Controller
{
    /**
     * 生成base64的数据
     *
     * @param Request $request
     * @return mixed
     */
    public function base64Image(Request $request)
    {
        if($_FILES['pic']){
            $base_img=\Oss::encodeBase64Image($_FILES['pic']);
            $return['code']=1;
            $return['data']['pic']=$base_img;
        }else{
            $return['code']=90001;
            $return['msg']='生成临时图片出错';
        }
        return $return;
    }

    /**
     * 文章内容图片上传接口 base64
     */
    public function imgUpload(Request $request) {
        $params = $request->input();
        $result=\Oss::uploadBase64Image($params['article_img'],1);
        if ($result['code'] == 1 ) {
            $result['data']['img_path'] = \Config::get('services.oss.host').'/'.$result['data']['img_path'];
        }
        return $result;
    }

    /**
     * 文件上传
     * @param Request $request
     * @return array
     */
    public function fileUpload(Request $request)
    {
        $params=$request->input();
        if($params['module']==1){
            $params['module']=null;
        }
        $is_uploan=\Oss::uploadFile($_FILES['file'],$params['device'],$params['module']);
        return $is_uploan;
    }

    /**
     * 清除key的缓存
     * @param Request $request
     */
    public function forgetCache(Request $request)
    {
        $params=$request->input();
        rewrite_cache()->forget($params['key']);
    }
}