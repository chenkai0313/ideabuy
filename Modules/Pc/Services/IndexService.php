<?php
/**
 * Created by PhpStorm.
 * User: CK
 * Date: 2017/10/11
 * Time: 13:59
 */

namespace Modules\Pc\Services;


use Modules\Goods\Services\BackendGoodsService;
use Modules\System\Services\MessageService;

class IndexService
{
    /**
     * 商城商品分类
     * @author CK
     * @return array
     */
    public function goodsCategoryList($params)
    {
        $params['is_show']=1;
        $goodsCategoryList=new BackendGoodsService();
        $goodsCategoryList=$goodsCategoryList->goodsCategoryListTree($params);
        $res['code']=1;
        $res['data'] =$goodsCategoryList['data']['goodsCategory_list_tree'];

        return $res ;
    }

    /**
     * 商城公告
     * @author CK
     * @return array
     */
    public function messageAnnounceList($params)
    {
        $announce = new MessageService();
        $params['type'] = 1;
        $params['page'] = 1;
        $params['limit'] = 3;
        $announce = $announce->messageAnnounce($params);
        $res = array();
        foreach ($announce['data']['list'] as $key => $v) {
            $res[$key]['content'] = $v['content'];
            $res[$key]['send_time'] = $v['send_time'];
        }
        $data['code']=1;
        $data['data'] = $res;
        return $data;
    }
}