<?php
/**
 * Created by PhpStorm.
 * User: pc16
 * Date: 2017/8/2
 * Time: 17:28
 */
namespace Modules\System\Services;

use Modules\System\Models\Constant;
use Modules\System\Models\ConstantType;

class BackendConstantService
{
    public function getConstantCache($key,$type)
    {
        if(!rewrite_cache()->has($key)) {
            $params['constant_key']=$key;
            $params['type']=$type;
            $constant = vpost(\Config::get('interactive.riskcontrol.get_constant'), $params);
            $constant = json_decode($constant,true);
            if($constant['code']==1){
                rewrite_cache()->forever($key,$constant['data']['constant_val']);
            }else{
                return ['code'=>10000,'msg'=>'查询出错'];
            }
        }
        return rewrite_cache()->get($key);
    }
}