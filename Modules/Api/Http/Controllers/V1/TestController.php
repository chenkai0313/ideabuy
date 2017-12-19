<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/5 0005
 * Time: 下午 15:59
 */
namespace Modules\Api\Http\Controllers\V1;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Libraries\Service\MessageService;
use Modules\User\Models\User;
use Symfony\Bridge\PsrHttpMessage\Tests\Fixtures\Message;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $params=$request->input();
        $data['user']=\DB::table('users')->where('user_mobile',$params['mobile'])->get();
        $data['msg']=\DB::table('system_sms')->select('mobile','code','type','created_at')->where('mobile',$params['mobile'])->where('status',0)->get();
        return $data;
    }

    public function user_test(Request $request)
    {
        $params=$request->input();
        return \UserService::userInfo($params);
    }
    //用户的关联删除
    public function userDelete(Request $request)
    {
        $params=$request->input();
        $user=\DB::table('users')->where('user_mobile',$params['user_mobile'])->value('user_id');
        print_sql();
        $card=\DB::table('user_card')->where('user_id',$user)->where('jl_bind_id','!=','')->get();
        if($card){
            foreach($card as $key=>$vo){
                $is_unbank=\yeepay::unbank($vo->card_number,$vo->jl_bind_id);
                if($is_unbank['code']!=1){
                    return '有银行卡解绑是吧，删除失败';
                }
            }
        }
        $order_sn=\DB::table('order_info')->where('user_id',$user)->pluck('order_sn');
        if($order_sn){
            \DB::table('order_goods')->whereIn('order_sn',$order_sn)->delete();
        }
        $table=['users','user_info','user_status','user_apply','user_third','user_wallet','user_wallet_detail',
            'user_card','user_address','order_info'];
        foreach ($table as $key=>$vo){
            \DB::table($vo)->where('user_id',$user)->delete();
        }
        $json=vget(\Config::get('system.risk_domain') . '/loan/userDelete?user_id='.$user);
        $rc_loan=vpost(\Config::get('system.risk_domain') .'/cron/clear',['user_id'=>$user]);
        $rc_loan=json_decode($rc_loan,true);
        if($rc_loan['code']!=1){
            return 'rc表的借贷数据清理失败';
        }
        return '成功';
    }

    public function userTestWallet(Request $request) {
        $params = $request->input();
        $test = \UserWalletService::UserWalletDetailAdd($params);
        return $test;
    }

    public function testRSA(Request $request) {
        $params = $request->input();
        return $params;
    }
    public function cacheFlush()
    {
        $bool=Cache::store(\Config::get('cache.cache_type'))->flush();
        if($bool){
            return '成功';
        }else{
            return '失败';
        }
    }

    public function testSendsms() {
        $msg = new MessageService();
        $tag = "security_code";
        $content = ['number'=>1234];
        $mobile = [15757390796,15988346742];
        return $msg->sendSms($content,$tag,$mobile);
    }

    public function testPush() {
        $push = [
            'operate_type' => '3',
            'user_id' => 83,
            'audience' => 'regis_id',
            'title' => 'titile',
            'description' => 'descriptionxxxx',
            'result' => json_encode(['code'=>1, 'msg'=>'description']),
            'message_type' => 'user_apply',
            'type' => 2, // 1公告 2通知
        ];
       return  \MessageService::messageEntry($push);
    }


    public function testRedis(Request $request) {
        $params = $request->input();
        return $params;
    }
}