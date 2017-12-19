<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/7/25
 * Time: 13:15
 */

namespace Modules\System\Services;

use Modules\System\Models\Ad;
use Modules\System\Models\AdType;

class AdService
{
    /**
     * 广告的获取(api)
     * @param $params['type_id']    int     广告分类ID
     * @return mixed
     */
    public function adObtain($params)
    {
        switch ($params['type'])
        {
            case 'launch_ad'://app开始广告
                $data['type_id']=5;
                break;
            case 'whiteindex_ad'://白条首页广告
                $data['type_id']=8;
                break;
            case 'mainindex_ad_banner': //首页 banner图
                $data['type_id']=4;
                break;
            case 'mainindex_ad_hot': //首页 热推业务
            $data['type_id']=6;
                break;
            case 'mainindex_ad_flow': //首页 流量专区
                $data['type_id']=7;
                break;
            default:
                return ['code'=>90002,'msg'=>'参数错误'];
        }
        $ad_imgs=Ad::adObtainGet($data);
        $ad_type=AdType::adTypeDetail($data);
        $size=explode('*',$ad_type['img_size']);
        $ad=[];
        foreach ($ad_imgs as $key=>$vo){
            $ad[$key]['url']=\Config::get('services.oss.host').'/'.$vo['ad_img'];
            $ad[$key]['wide']=$size[0];
            $ad[$key]['high']=$size[1];
            $ad[$key]['location_href']=$vo['location_href'];
        }

        if(count($ad)==0){
            $return['code']=10106;
            $return['msg']='广告不存在';
        }else{
            $return['code']=1;
            $return['data']=$ad;
        }
        return $return;
    }
}