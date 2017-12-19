<?php

use Illuminate\Database\Seeder;

class SystemMsgTemplateInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['1', '${number}', '2017-09-04 13:56:31', '2017-09-04 13:56:31', '验证码'],
            ['2', '${month}', '2017-09-04 13:56:54', '2017-09-04 13:56:54', '月份'],
            ['3', '${money}', '2017-09-04 13:57:23', '2017-09-04 13:57:23', '钱'],
            ['4', '${day}', '2017-09-04 13:57:33', '2017-09-04 13:57:33', '日'],
            ['5', '${name}', '2017-09-04 13:57:46', '2017-09-04 13:57:46', '用户名'],
            ['6', '${creditcode}', '2017-09-04 13:58:18', '2017-09-04 13:58:18', '授信码'],
            ['7', '${ordernumber}', '2017-09-04 13:59:24', '2017-09-04 13:59:24', '订单号'],
        ];

        $field = ['keyword_id','keyword_name','created_at','updated_at','keyword_zh'];

        DB::table('system_msg_template_info')->insert(sql_batch_str($field,$data));

    }
}
