<?php
/**
 * 支付模块
 * Author: 葛宏华
 * Date: 2017/11/05
 */

namespace Modules\Pc\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PayController extends Controller
{
    /**
     * 支付宝网页支付
     * @param Request $request
     * @return array
     */
    public function aliPayWeb(Request $request)
    {
        $params = $request->all();
        $params['user_id'] = get_user_id();
        $result = \PayService::aliPayWeb($params);
        return $result;
    }
}
