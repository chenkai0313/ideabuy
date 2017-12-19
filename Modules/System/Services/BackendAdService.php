<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/1
 * Time: 14:31
 */
namespace Modules\System\Services;

use Modules\System\Models\Ad;
use Modules\System\Models\AdType;

class BackendAdService
{
    /**
     * 广告分类的新增(backend)
     * @param $params ['type_name']  string   分类名
     * @param $params ['img_size']  string    广告图片大小
     * @return mixed
     */
    public function adTypeAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.adtype.adtype-add'),
            \Config::get('validator.system.adtype.adtype-key'),
            \Config::get('validator.system.adtype.adtype-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $isAdd = AdType::adTypeAdd($params);
        if ($isAdd) {
            $return['code'] = 1;
            $return['msg'] = '新增成功';
        } else {
            $return['code'] = 10040;
            $return['msg'] = '新增失败';
        }

        return $return;
    }

    /**
     * 广告分类列表(backend)
     * @param $params ['page']       int     页码
     * @param $params ['limit']       int     页数
     * @param $params ['keyword']       string     搜索关键词
     * @return array
     */
    public function adTypeList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['adType_list'] = AdType::adTypeList($params);
        $data['total'] = AdType::adTypeCount($params);
        return ['code' => 1, 'data' =>$data];
    }

    /**
     * 广告分类详情(backend)
     * @param $params ['type_id']    int     广告分类ID
     * @return array
     */
    public function adTypeDetail($params)
    {
        if (!isset($params['type_id']) || $params['type_id'] <= 0) {
            return ['code' => 90002, 'msg' => '广告分类ID参数错误'];
        }
        $adType = AdType::adTypeDetail($params);
        return ['code' => 1, 'data' => ['type_detail'=>$adType]];
    }

    /**
     * 广告分类的修改(backend)
     * @param $params ['type_id']    int     广告分类ID
     * @param $params ['type_name']    string     广告分类名
     * @param $params ['img_size']    string     广告图片大小
     * @return array
     */
    public function adTypeEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.adtype.adtype-edit'),
            \Config::get('validator.system.adtype.adtype-key'),
            \Config::get('validator.system.adtype.adtype-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $adType = AdType::adTypeEdit($params);
        if ($adType) {
            $return = ['code' => 1, 'msg' => '修改成功'];
        } else {
            $return = ['code' => 10041, 'msg' => '修改失败'];
        }

        return $return;
    }

    /**
     * 广告分类的查询全部(backend)
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function adTypeSpinner()
    {
        $adtype = AdType::adTypeSpinner();
        return ['code' => 1, 'data' => ['type_list'=>$adtype]];
    }

    /**
     * 广告的列表(backend)
     * @param $params ['page']       int     页码
     * @param $params ['limit']       int     页数
     * @param $params ['keyword']       string     搜索关键词
     * @return array
     */
    public function adList($params)
    {
        $params['limit'] = isset($params['limit']) ? $params['limit'] : 20;
        $params['page'] = isset($params['page']) ? $params['page'] : 1;
        $params['keyword'] = isset($params['keyword']) ? $params['keyword'] : null;
        $data['ad_list'] = Ad::adList($params);
        foreach ($data['ad_list'] as $key=>$vo){
            $data['ad_list'][$key]['ad_img']=\Config::get('services.oss.host').'/'.$vo['ad_img'];
        }
        $data['total'] = Ad::adCount($params);
        return ['code' => 1, 'data' => $data];
    }

    /**
     * 广告删除(backend)
     * @param $params ['ad_id']      int     广告ID
     * @return mixed
     */
    public function adDelete($params)
    {
        if (!isset($params['ad_id'])) {
            return ['code' => 90002, 'msg' => '广告ID参数错误'];
        }
        if (strpos($params['ad_id'], ',')) {
            $params['ad_id'] = explode(',', $params['ad_id']);
        };
        $isDelete = Ad::adDelete($params);
        if ($isDelete === 0) {
            $return['code'] = 10042;
            $return['msg'] = '删除失败';
        } else {
            $return['code'] = 1;
            $return['msg'] = '删除成功';
        }

        return $return;
    }

    /**
     * 广告的新增(backend)
     * @param $params ['type_id']        int     广告分类ID
     * @param $params ['ad_img']        string     广告图片Base64数据
     * @param $params ['is_show']        int     是否显示
     * @param $params ['location_href']        int     图片跳转url
     * @return array
     */
    public function adAdd($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.ad.ad-add'),
            \Config::get('validator.system.ad.ad-key'),
            \Config::get('validator.system.ad.ad-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        if (is_null($params['location_href'])) {
            $params['location_href'] = "";
        }
        $is_base64 = strpos($params['ad_img'], "base64");
        if ($is_base64) {
            $oss = \Oss::uploadBase64Image($params['ad_img'], 2);
            if ($oss['code'] == 1 && isset($oss['code'])) {
                $params['ad_img'] = $oss['data']['img_path'];
                $isAdd = Ad::adAdd($params);
                if ($isAdd) {
                    $return['code'] = 1;
                    $return['msg'] = '新增成功';
                } else {
                    $return['code'] = 10043;
                    $return['msg'] = '新增失败';
                }
            } else {
                return $oss;
            }
        } else {
            $return['code'] = 90005;
            $return['msg'] = '图片信息不是base64的数据';
        }

        return $return;
    }

    /**
     * 广告的详情(backend)
     * @param $params ['ad_id']      int     广告的ID
     * @param $params ['spinner']    int     是否在同一接口显示广告分类的全部(为前端暂时写的,如果不用,后期删除)
     * @return mixed
     */
    public function adDetail($params)
    {
        if (!isset($params['ad_id']) || $params['ad_id'] <= 0) {
            return ['code' => 90002, 'msg' => '广告ID参数错误'];
        }
        $ad = Ad::adDetail($params);
        if($ad){
            $ad['ad_img']=\Config::get('services.oss.host').'/'.$ad['ad_img'];
        }else{
            $ad='';
        }
        $return['code'] = 1;
        $return['data'] = ['ad_detail'=>$ad];
        if (isset($params['spinner']) && $params['spinner'] == 1) {
            $return['data']['spinner'] = AdType::adTypeSpinner();
        }
        return $return;
    }


    /**
     * 广告的编辑(backend)
     * @param $params ['ad_id']      int     广告ID
     * @param $params ['type_id']      int     广告分类ID
     * @param $params ['ad_img']      string     广告图片base64数据
     * @param $params ['is_show']      int     是否显示(0,1)
     * @return array
     */
    public function adEdit($params)
    {
        $validator = \Validator::make(
            $params,
            \Config::get('validator.system.ad.ad-edit'),
            \Config::get('validator.system.ad.ad-key'),
            \Config::get('validator.system.ad.ad-val')
        );
        if (!$validator->passes()) {
            return ['code' => 90002, 'msg' => $validator->messages()->first()];
        }
        $is_base64 = strpos($params['ad_img'], "base64");
        if ($is_base64) {
            $oss = \Oss::uploadBase64Image($params['ad_img'], 2);
            if ($oss['code'] == 1 && isset($oss['code'])) {
                $params['ad_img'] = $oss['data']['img_path'];
            } else {
                return $oss;
            }
        } else {
            unset($params['ad_img']);
        }
        $ad = Ad::adEdit($params);
        if ($ad) {
            $return = ['code' => 1, 'msg' => '修改成功'];
        } else {
            $return = ['code' => 10044, 'msg' => '修改失败'];
        }

        return $return;
    }
}