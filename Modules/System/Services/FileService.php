<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 9:57
 */

namespace Modules\System\Services;

use Illuminate\Support\Facades\Config;
use Modules\System\Models\File;

class FileService
{
    /**
     * 文件的添加
     * @param $params
     * @return array
     */
    public function fileAdd($params)
    {
        if(isset($params['del_id'])){
            if($params['del_id']!=null){
                $file_id=explode(',',$params['del_id']);
                $files=File::UserIdPhoto($file_id);
                $del_id=[];
                foreach ($files as $key=>$vo){
                    \Oss::deleteImage($vo['file_path']);
                    array_push($del_id,$vo['file_id']);
                }
                File::fileDelete($del_id);
            }
        }
        $file = explode(',', $params['file']);
        $file_id = '';
        foreach ($file as $key => $vo) {
            $params['file_path'] = $vo;
            $file = File::fileAdd($params);
            if ($file) {
                $file_id .= $file['file_id'] . ',';
            }
        }
        $file_id = substr($file_id, 0, strlen($file_id) - 1);
        return ['code' => 1, 'data' => $file_id];
    }

    /**
     * 文件的查询
     * @param $params
     * @return array
     */
    public function UserIdPhoto($params)
    {
        $res = File::UserIdPhoto($params);
        foreach ($res as $key => $value) {
            $res[$key]['file_path'] = \Config::get('services.oss.host') . '/' . $res[$key]['file_path'];
            $res[$key]['tag']=$key;
        }
        return $res;
    }
}