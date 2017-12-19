<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/29 0029
 * Time: 上午 10:30
 */

namespace Modules\Api\Http\Controllers\V1;

use App\Events\NotifyPosh;
use Illuminate\Routing\Controller;
use Modules\Api\Http\Requests\LiyongchuanRequest;
class LiyongchuanController extends Controller
{
    public function liTest(LiyongchuanRequest $request)
    {
        $params=$request->input();
        $params=[
            'services'=>'smspush',
            'keyword'=>[
                'month'=>'09',
                'money'=>'1345',
            ],
            'tag'=>'overdue_payment',
            'type'=>2,
            'message_type'=>5,
            'operate_type'=>1,
            'send_object'=>1,
            'user_id'=>['13486627348','15078762345'],
            'sms_send'=>1,
        ];
        \Event::fire(new NotifyPosh($params));
        return 123;
    }
}