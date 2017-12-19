<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4 0004
 * Time: 下午 13:25
 */

namespace Modules\System\Services;

use Illuminate\Support\Facades\Config;
use Modules\System\Models\Version;

class VersionService
{
    /**
     * ios版本跟新
     * @param $params
     * @return array
     */
    public function iosVersion($params)
    {
        $params['update_type']=1;
        $version_arr= Version::versionGroup($params);
        $ios_bool=false;
        $version='';
        foreach ($version_arr as $key=>$vo) {
            if(version_compare($vo['version'],$params['version'],'>')){
                $ios_bool=true;
                $version=$vo['version'];
            }
        }
        if($ios_bool){
            $version_detail=Version::versionDetail($params['device'],$version);
            return ['code'=>0,'msg'=>$version_detail['version_content']];
        }else{
            return ['code'=>1,'msg'=>'没有跟新内容'];
        }
    }

    /**
     * 前端ios版本更新
     * @param $params
     * @return array
     */
    public function iosFront($params)
    {
        $front['device']='front-end';
        $front['update_type']=2;
        $version_front_arr= Version::versionGroup($front);
        $front_bool=false;
        $front_version=[];
        foreach ($version_front_arr as $key=>$vo){
            if(version_compare($vo['version'],$params['front_version'],'>')){
                $front_bool=true;
                array_push($front_version,$vo['version']);
            }
        }
        if($front_bool){
            return ['code'=>0,'msg'=>'有跟新内容'];
        }else{
            return ['code'=>1,'msg'=>'没有跟新内容'];
        }
    }

    /**
     * android 版本更新
     * @param $params
     * @return array
     */
    public function androidVersion($params)
    {
        $params['update_type']=1;
        $version_arr= Version::versionGroup($params);
        $android_bool=false;
        $android_version=[];
        foreach ($version_arr as $key=>$vo){
            if(version_compare($vo['version'],$params['version'],'>')){
                $android_bool=true;
                array_push($android_version,$vo['version']);
            }
        }
        if($android_bool){
            $version_detail=Version::versionDetailArr($params['device'],$android_version);
            return ['code'=>1,'data'=>\Config::get('services.oss.host').'/'.$version_detail['version_url'],'msg'=>$version_detail['version_content']];
        }else{
            return ['code'=>10000,'msg'=>'没有更新内容'];
        }
    }

    /**
     * 安卓热跟新版本
     * @param $params
     * @return array
     */
    public function androidHotUpdate($params)
    {
        $params['update_type']=3;
        $version_arr= Version::versionGroup($params);
        $android_bool=false;
        $android_version=[];
        foreach ($version_arr as $key=>$vo){
            if(version_compare($vo['version'],$params['hot_version'],'>')){
                $android_bool=true;
                array_push($android_version,$vo['version']);
            }
        }
        if($android_bool){
            $version_detail=Version::versionDetailArr($params['device'],$android_version);
            return ['code'=>1,'data'=>\Config::get('services.oss.host').'/'.$version_detail['version_url'],'msg'=>$version_detail['version_content']];
        }else{
            return ['code'=>10000,'msg'=>'没有更新内容'];
        }
    }

    /**
     * 安卓前端版本更新
     * @param $params
     * @return array
     */
    public function androidFront($params)
    {
        $front['device']='front-end';
        $front['update_type']=2;
        $version_front_arr= Version::versionGroup($front);
        $front_bool=false;
        $front_version=[];
        foreach ($version_front_arr as $key=>$vo){
            if(version_compare($vo['version'],$params['front_version'],'>')){
                $front_bool=true;
                array_push($front_version,$vo['version']);
            }
        }
        if($front_bool){
            $now_version='';
            $version_file=[];
            $version=Version::versionFront($front['device'],$front_version);
            foreach ($version as $key=>$vo){
                if($now_version==''){
                    $now_version=$vo['version'];
                }
                array_push($version_file,['url'=>\Config::get('services.oss.host').'/'.$vo['version_url'],'md5'=>$vo['md5'],'version'=>$vo['version']]);
            }
            return ['code'=>1,'data'=>['now_version'=>$now_version,'version_file'=>$version_file]];
        }else{
            return ['code'=>10000,'msg'=>'没有更新内容'];
        }
    }

}