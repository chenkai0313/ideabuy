<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/31
 * Time: 14:50
 */

namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CommonController extends Controller
{
    /**
     * ios版本跟新
     * @param Request $request
     * @return mixed
     */
    public function iosVersion(Request $request)
    {
        $params = $request->input();
        $version = \VersionService::iosVersion($params);
        return $version;
    }

    /**
     * 前端ios版本更新
     * @param Request $request
     * @return mixed
     */
    public function iosFront(Request $request)
    {
        $params = $request->input();
        $version = \VersionService::iosFront($params);
        return $version;
    }

    /**
     * android版本更新
     * @param Request $request
     * @return mixed
     */
    public function androidVersion(Request $request)
    {
        $params = $request->input();
        $version = \VersionService::androidVersion($params);
        return $version;
    }
    /**
     * 安卓热跟新版本
     * @param Request $request
     * @return mixed
     */
    public function androidHotUpdate(Request $request)
    {
        $params = $request->input();
        $version = \VersionService::androidHotUpdate($params);
        return $version;
    }

    /**
     * 安卓前端版本更新
     * @param Request $request
     * @return array
     */
    public function androidFront(Request $request)
    {
        $params = $request->input();
        $version = \VersionService::androidFront($params);
        return $version;
    }

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
     * 订单不分期
     * @param Request $request
     * @return mixed
     */
    public function orderUnLoan(Request $request)
    {
        $params = $request->all();
        $result = \OrderService::orderUnLoan($params);
        return $result;
    }

    public function checkVersion()
    {
        $check_version = '1.0.0';
        return ['code' => 1, 'data' => ['checkVersion' => $check_version]];
    }
}