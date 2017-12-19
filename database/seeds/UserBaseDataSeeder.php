<?php

use Illuminate\Database\Seeder;

class UserBaseDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 会员表  测试账号 登录密码 aa123456 支付密码 123456
        $data = [
            ['1', '测试账号', '', '$2y$10$6cV3TGVQnNIZE0t3D.efbeIiY7w5of/jkEVJiMUTAyZUlZn2og9ZC', '', '330281199407045718', '15988346742', '$2y$10$cwuc2CpxaZuFiYp4P9Z5tuRoZV3jVSinadfeD.8mwbN5p9mlgf3nW', '0', '0', '', '', '', '500', 'PyzahLbx', '5000.0000', '2017-08-28 15:55:42', '2017-09-15 18:10:48', null, '2017-09-15 00:00:00', null, null, '', '', '0'],
            ['2', '葛宏华', '', '$2y$10$PSR4/2OVCa/xfyDr2bvkUOnfdoS1wN/vn2vErberl9cAiOkyZaayy', '', '330226198903210015', '15105840179', '$2y$10$fA5WH5LwokrkVyIQX1Yq8O38mS.zAX4SNXl12sXJvrvt.v0BDboUi', '0', '0', '', '', '', '500', '', '5000.0000', '2017-08-28 16:52:58', '2017-09-13 15:36:14', null, '2017-08-29 00:00:00', '2017-06-01 00:00:00', '2017-06-20 00:00:00', '', '', '1'],
            ['3', '赵志峰', '', '$2y$10$GsFGcjbkRuoY0N6AFu6jeOXmxJLea9GRtFzTxjTZETMx5sbDx5Wea', '', '410922199206154916', '13567929498', '$2y$10$QCtdB8ckCUzP55CYt4xYyetJ/QY6DoqnpTBF9.vZLheAxQ0mHWRbW', '0', '12', '', '', '', '500', 'jowUB223', '5000.0000', '2017-08-29 13:32:59', '2017-08-29 14:08:18', null, '2017-08-29 00:00:00', null, null, '', '', '0'],
            ['4', '丁浩', '', '$2y$10$rMIZMBg8nozITMFS5yo4P.V3nbKDRRa9YPTH6bZozxbNyKdAfYCfK', '', '421002199405224225', '18817925405', '$2y$10$WXZ0zJ37XjcwL/HhySy/Wed33NEsIvaWUUVIp46kDZJWFC4v61tcS', '0', '11', '', '', '', '500', 'jowUB224', '5000.0000', '2017-08-29 13:39:02', '2017-08-29 14:10:54', null, '2017-08-29 00:00:00', null, null, '', '', '0'],
            ['5', '马少琳', '', '$2y$10$zKBYYMECQiXneho9lzdi1uF0QEjAyJGVzOWy1QYQR8lhXtb0P5TfC', '', '330724199203156926', '15888025405', '$2y$10$6ioGFBIH3Z/0G9WC4Q0vNOaIPDB41IirZXuzXxDCUvic7uk6DNIFm', '0', '10', '', '', '', '500', 'jowUB225', '5000.0000', '2017-08-29 13:41:49', '2017-08-29 14:12:17', null, '2017-08-29 00:00:00', null, null, '', '', '0'],
            ['6', '崔冬明', '', '$2y$10$LeNZ3SlqaRDfFWhzNAL7O.dG25Ao2DMv4l4NdlQAHC0frXzVebcHi', 'static/product/ios1_0_8ac4a63ae494bf99233312dd6a37c574f.png', '330203198012282432', '13777979098', '$2y$10$YhIN6O3kjJPGnA6dJ392E.fh0Ymj9SFUX1m7TEEbN9oXDWtNK6y6a', '30', '0', '', '', '', '510', 'JpaBlO2v', '5100.0000', '2017-08-30 18:07:50', '2017-09-15 11:12:36', null, '2017-09-01 00:00:00', '2016-08-01 00:00:00', '2016-08-20 00:00:00', '', '', '0'],
        ];

        $field = ['user_id','real_name','salt','user_password','user_portrait','user_idcard','user_mobile','pay_password','address_id','card_id','qq_openid','wx_openid','remember_token','credit_point','credit_code','white_amount','created_at','updated_at','deleted_at','activate_date','first_bill_date','first_pay_date','client_device','client_version','is_black'];
        DB::table('users')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户信息附加表
        $data = [
            ['1', '1', '', '', '', '', '', '', '', '', '', '2017-08-28 15:55:43', '2017-08-28 15:55:43'],
            ['2', '2', '', '', '', '', '', '', '', '', '', '2017-08-28 16:52:58', '2017-08-28 16:52:58'],
            ['3', '3', '', '', '', '', '', '', '马少琳', '15888025405', '同事', '2017-08-29 13:32:59', '2017-08-29 14:05:56'],
            ['4', '4', '', '', '', '', '', '', '马少琳', '15888025405', '同事', '2017-08-29 13:39:02', '2017-08-29 14:02:56'],
            ['5', '5', '', '', '', '', '', '', '曹晗', '15757390796', '同事', '2017-08-29 13:41:49', '2017-08-29 14:00:42'],
            ['6', '6', '', '', '', '', '', '', '', '', '', '2017-08-30 18:07:51', '2017-09-02 16:34:26'],
        ];

        $field = ['info_id','user_id','user_education','user_profession','user_company','user_income','user_qq','user_email','link_man','link_mobile','link_relation','created_at','updated_at'];

        DB::table('user_info')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户状态表
        $data = [
            ['1', '1', '0', '1', '1', '1', '2'],
            ['2', '2', '0', '1', '1', '1', '2'],
            ['3', '3', '1', '1', '1', '1', '2'],
            ['4', '4', '1', '1', '1', '1', '2'],
            ['5', '5', '1', '1', '1', '1', '2'],
            ['6', '6', '0', '1', '1', '1', '2'],
        ];

        $field = ['status_id','user_id','is_linkman','is_idcard','is_idcard_img','is_activate','status'];

        DB::table('user_status')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户第三方绑定表
        $data = [
            ['1', '1', '', '1', '', '', '1104a8979293a0ba1a0', '', '2017-08-28 15:55:42', '2017-09-15 18:08:11'],
            ['2', '2', '', '1', '', '', '18071adc033859d4636', '', '2017-08-28 16:52:58', '2017-09-01 16:36:29'],
            ['3', '3', '', '1', '', '', '160a3797c83e9b6a7f0', '', '2017-08-29 13:32:59', '2017-08-30 10:32:19'],
            ['4', '4', '', '1', '', '', '140fe1da9e9efb7f373', '', '2017-08-29 13:39:02', '2017-08-29 19:40:15'],
            ['5', '5', '', '1', '', '', '140fe1da9e9efb7f373', '', '2017-08-29 13:41:49', '2017-08-29 19:38:52'],
            ['6', '6', '', '1', '', '', '1104a8979293a0ba1a0', '', '2017-08-30 18:07:50', '2017-09-15 15:36:47'],
        ];

        $field = ['id','user_id','openid','type','access_token','refresh_token','jpush_token','code','created_at','updated_at'];

        DB::table('user_third')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户申请表
        $data = [
            ['1', '1', '测试账号', '330281199407045718', '289,290,291', '2', '2017-09-15 18:10:48', '2017-09-15 18:10:48', '', '1'],
            ['2', '2', '葛宏华', '330226198903210015', '40,41,42', '2', '2017-08-29 09:36:18', '2017-08-29 09:36:18', '', '1'],
            ['3', '3', '赵志峰', '410922199206154916', '64,65,66', '2', '2017-08-29 13:46:04', '2017-08-29 13:46:04', '', '1'],
            ['4', '4', '丁浩', '421002199405224225', '67,68,69', '2', '2017-08-29 13:46:08', '2017-08-29 13:46:08', '', '1'],
            ['5', '5', '马少琳', '330724199203156926', '73,74,75', '2', '2017-08-29 13:49:17', '2017-08-29 13:49:17', '', '1'],
            ['6', '6', '崔冬明', '330203198012282432', '163,164,165', '2', '2017-09-01 14:03:53', '2017-09-01 14:03:53', 'ok', '1'],
        ];

        $field = ['apply_id','user_id','real_name','user_idcard','id_img','status','updated_at','created_at','reason','apply_type'];

        DB::table('user_apply')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户收货地址信息表
        $data = [
            ['1', '1', '', '', '', '0', '', '2017-08-28 15:55:43', '2017-08-28 15:55:43'],
            ['2', '2', '', '', '', '0', '', '2017-08-28 16:52:58', '2017-08-28 16:52:58'],
            ['3', '3', '', '', '', '0', '', '2017-08-29 13:32:59', '2017-08-29 13:32:59'],
            ['4', '4', '', '', '', '0', '', '2017-08-29 13:39:02', '2017-08-29 13:39:02'],
            ['5', '5', '', '', '', '0', '', '2017-08-29 13:41:49', '2017-08-29 13:41:49'],
            ['6', '6', '', '', '', '0', '', '2017-08-30 18:07:51', '2017-08-30 18:07:51'],
        ];

        $field = ['address_id','user_id','province','city','district','street','address','created_at','updated_at'];

        DB::table('user_address')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 用户绑定的银行卡表
        $data = [
            ['1', '3', '6223093320012087690', '', '135****9498', '3', '', '2017-08-29 14:05:18', null],
            ['2', '4', '6223093320012084812', '', '188****5405', '3', '', '2017-08-29 14:02:33', null],
            ['3', '5', '6223093320010484469', '', '158****5405', '3', '', '2017-08-29 13:57:24', null],
        ];

        $field = ['card_id','user_id','card_number','card_address','card_mobile','bank_id','jl_bind_id','created_at','deleted_at'];

        DB::table('user_card')->insert(sql_batch_str($field,$data));

    }
}
