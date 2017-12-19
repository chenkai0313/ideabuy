<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/1
 * Time: 14:42
 */
namespace Modules\System\Services;

use Modules\System\Models\Region;

class RegionService
{
    public function regionGet($params)
    {
        $region=Region::regionGet($params);
        if($region){
            $data['province']=$region[0];
            $data['city']=$region[1];
            $data['district']=$region[2];
            $result['code'] = 1;
            $result['data'] = $data;
        }else{
            $result['code'] = 10175;
            $result['msg'] = '该地址不存在或已删除';
        }
        return $result;
    }

    public function regionByLevel($params)
    {
        $level = range(1, $params['level']);
        $region = Region::regionByLevel($level)->toArray();
        return ['code' => 1, 'data' => $region];
    }
}