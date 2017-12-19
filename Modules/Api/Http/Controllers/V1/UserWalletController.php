<?php
/**
 * Created by PhpStorm.
 * User: 曹晗
 * Date: 2017/8/24
 *
 * 对接rc 控制器
 */
namespace Modules\Api\Http\Controllers\V1;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserWalletController extends Controller
{
    public function UserWalletDetailAdd(Request $request) {
        $params = $request->input();
        $result = \UserWalletService::UserWalletDetailAdd($params);
        return $result;
    }
}