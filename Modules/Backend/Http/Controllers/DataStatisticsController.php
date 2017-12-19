<?php
/**
 * 数据统计
 * Author: 傅跃华
 * Date: 2017/8/31
 */
namespace Modules\Backend\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DataStatisticsController extends Controller
{
    /**
     *用户数量统计
     * @param Request $request
     * @return mixed
     */
    public function userCountStatistics(Request $request)
    {
        $params = $request->input();
        return \UserStatisticsService::userCountStatistics($params);
    }
}