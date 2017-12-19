<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/6 0006
 * Time: 下午 15:48
 */
namespace Modules\System\Services;

use Modules\System\Models\Version;

class BackendVersionService
{
    /**
     * version列表
     * @param $params['page']   int     页码
     * @param $params['limit']   int     页数
     * @param $params['keyword']   string    关键词
     * @return array
     *
     * @author      liyongchuan
     */
    public function versionList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $version=Version::versionList($params);
        $count=Version::versionCount($params);
        if($count==0){
            $return=['code'=>1,'data'=>''];
        }else{
            $return=['code'=>1,'data'=>['version'=>$version,'total'=>$count]];
        }
        return $return;
    }

    /**
     * version 删除
     * @param $params['id']     string      以,隔开的id字符串
     * @return array
     *
     * @author      liyongchuan
     */
    public function versionDelete($params)
    {
        $params['id']=explode(',',$params['id']);
        $del=Version::versionDelete($params);
        if($del){
            $return=['code'=>1,'msg'=>'删除成功'];
        }else{
            $return=['code'=>10000,'msg'=>'删除失败'];
        }
        return $return;
    }

    /**
     * version 新增操作
     * @param $params
     * @return array
     *
     * @author      liyongchuan
     */
    public function versionAdd($params)
    {
        foreach ($params['version_url'] as $key=>$vo){
            $data[$key]['device']=$params['device'];
            $data[$key]['version']=$params['version'];
            $data[$key]['version_url']=$vo['file_path'];
            $data[$key]['version_content']=$params['version_content'];
            $data[$key]['update_type']=$params['update_type'];
            $data[$key]['update_mode']=$params['update_mode'];
            $data[$key]['md5']=$vo['file_md5'];
            $data[$key]['module']=$params['module'];
            $data[$key]['created_at']=date('Y-m-d H:i:s');
        }
        $add=Version::versionAdd($data);
        if($add){
            $return=['code'=>1,'msg'=>'新增成功'];
        }else{
            $return=['code'=>10000,'msg'=>'新增失败'];
        }
        return $return;
    }
    public function versionAddDisplay()
    {
        $return['code']=1;
        $return['data']=\Config::get('version');
        return $return;
    }
}