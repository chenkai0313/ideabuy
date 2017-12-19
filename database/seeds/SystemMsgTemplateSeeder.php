<?php

use Illuminate\Database\Seeder;

class SystemMsgTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', '您${month}月份订单已过期，需支付金额${money}元，请您尽快支付！您${month}月份订单已过期，需支付金额${money}元，请您尽快支付！', '逾期催款', '2017-09-04 14:05:34', '2017-09-04 14:03:42', 'overdue_payment', '逾期催款', 'Overdue_payment'],
            ['2', '您${month}月份订单需支付${money}元，请在本月${day}日前支付', '将逾期催款', '2017-09-04 14:06:38', '2017-09-04 14:06:38', 'willbe_overdue', '将逾期催款', 'willbe_overdue'],
            ['3', '亲爱的${name}，恭喜您成为畅想购会员。您只需身份认证后即可享受畅想购的服务！', '注册成功', '2017-09-04 14:07:31', '2017-09-04 14:07:31', 'register_success', '注册成功', 'register_success'],
            ['4', '${month}月订单应支付${money}元，账单日为${day}日，逾期后有产生服务费，请尽快支付', '账单提醒', '2017-09-04 14:08:50', '2017-09-04 14:08:50', 'bill_reminders', '账单提醒', 'bill_reminders'],
            ['6', '退款订单号：${ordernumber}，退款金额${money}元。', '退款', '2017-09-04 14:11:15', '2017-09-04 14:11:15', 'reimburse_money', '退款', 'reimburse_money'],
            ['7', '订单号${ordernumber}的订单支付成功，共支付${money}元', '订单支付成功', '2017-09-04 14:12:14', '2017-09-04 14:12:14', 'order_success', '订单支付成功', 'order_success'],
            ['8', '订单号为${ordernumber}的未付款订单即将到期，到期后将被取消，请抓紧时间付款哦！', '订单催款', '2017-09-04 14:12:49', '2017-09-04 14:12:49', 'order_dept', '订单催款', 'order_dept'],
            ['9', '您的验证码为${number}。为了您的账户安全，请勿向他人泄露。感谢您的陪 伴！', '验证码', '2017-09-04 14:14:47', '2017-09-04 14:14:47', 'security_code', '验证码', 'security_code'],
        ];

        $field = ['id','content','prepare_node','created_at','updated_at','msg_tag','msg_type','msg_title'];

        DB::table('system_msg_template')->insert(sql_batch_str($field,$data));
    }
}
