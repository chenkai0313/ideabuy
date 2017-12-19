<?php

use Illuminate\Database\Seeder;

class SystemAdDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 广告
        $data = [
            ['1', '6', 'ad/2017/09/01/dddcf829ecfcb4e35ba21cd19f2b7ba7.jpg', '0', 'http://h.t.weknet.cn/buyPhone.html', null, '2017-08-18 08:58:39', '2017-09-01 14:06:06'],
            ['2', '6', 'ad/2017/09/01/9d1012ea82743195f6b1c6b67807bb6c.jpg', '0', 'http://h.t.weknet.cn/flowRecharge.html', null, '2017-08-18 08:58:48', '2017-09-01 14:06:14'],
            ['3', '7', 'ad/2017/08/18/54294fb4ec3cea7ca68a1fc156af3539.jpg', '0', '', null, '2017-08-18 09:00:55', '2017-08-18 11:41:18'],
            ['4', '8', 'ad/2017/08/18/6f167cdf86425690c17035a0d65a9b18.jpg', '0', '', null, '2017-08-18 10:32:33', '2017-08-18 11:41:29'],
            ['5', '5', 'ad/2017/08/30/47be4f65d8e7c0944db6f4bba7793b84.jpg', '0', '', null, '2017-08-26 10:40:28', '2017-08-30 11:42:09'],
            ['6', '4', 'ad/2017/09/10/4d787f51bc7c637a30bcd13185e04579.jpg', '0', '', null, '2017-09-10 19:33:27', '2017-09-10 19:33:27'],
        ];

        $field = ['ad_id','type_id','ad_img','is_show','location_href','deleted_at','created_at','updated_at'];

        DB::table('system_ad')->insert(sql_batch_str($field,$data));
        unset($data);unset($field);

        // 广告分类
        $data = [
            ['4', '首页banner', '1000*550', '2017-08-17 10:50:29', '2017-08-17 10:50:29'],
            ['5', '客户端启动页后广告 launch_ad', '750*1334', '2017-08-17 10:51:13', '2017-08-26 10:36:00'],
            ['6', '首页 热推业务 测试', '489*368', '2017-08-18 08:58:19', '2017-08-22 14:19:17'],
            ['7', '首页 流量专区 测试', '1000*330', '2017-08-18 08:59:55', '2017-08-18 11:36:44'],
            ['8', '白条首页 单张海报', '1080*294', '2017-08-18 10:11:23', '2017-08-18 11:38:59'],
        ];

        $field = ['type_id','type_name','img_size','created_at','updated_at'];

        DB::table('system_ad_type')->insert(sql_batch_str($field,$data));
    }
}
