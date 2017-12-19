<?php
/**
 * Created by PhpStorm.
 * User: 傅跃华
 * Date: 2017/8/31
 * 用户数据统计服务层
 */

namespace Modules\User\Services;

use Modules\User\Models\User;

class UserStatisticsService
{
    /**
     * 用户数据统计
     * @param $params
     * @return array
     */
    public function userCountStatistics($params)
    {
        $data['total'] = User::userCountByClient();
        $data['white_activated'] = User::userCountByStatus(['is_activate' => 1]);
        $data['wait_review'] = User::userCountByStatus(['status' => 1]);
        $data['ios_count'] = User::userCountByClient(['client_device' => 'ios']);
        $data['android_count'] = User::userCountByClient(['client_device' => 'android']);

        return ['code' => 1, 'msg' => '获取成功', 'data' => $data];
    }

}